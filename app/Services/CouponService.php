<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookingType;
use App\Enums\CouponType;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Coupon;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Collection;

/**
 * Server-side coupon validation and discount computation.
 *
 * Every rule that the client-side checker mirrors is re-checked here — validity
 * window, global usage cap, per-user cap, minimum order, product-type and
 * destination restrictions. The client only ever *suggests*; this class decides.
 */
class CouponService
{
    /**
     * Validate a code against a live booking context.
     *
     * @param  Collection<int, BookingItem>  $items
     * @return Coupon|null Null means invalid; `reasonFor()` explains why.
     */
    public function resolve(?string $code, ?User $user, int $subtotalMinor, Collection $items): ?Coupon
    {
        $code = trim((string) $code);

        if ($code === '') {
            return null;
        }

        $coupon = Coupon::query()->whereRaw('UPPER(code) = ?', [mb_strtoupper($code)])->first();

        if ($coupon === null || ! $coupon->is_active) {
            return null;
        }

        if (! $coupon->is_started || $coupon->is_expired) {
            return null;
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return null;
        }

        if ($subtotalMinor < Booking::toMinor((float) $coupon->min_order_amount)) {
            return null;
        }

        if ($user !== null && $coupon->per_user_limit > 0) {
            $used = $coupon->usages()->where('user_id', $user->getKey())->count();

            if ($used >= $coupon->per_user_limit) {
                return null;
            }
        }

        if (! $this->appliesToItems($coupon, $items)) {
            return null;
        }

        return $coupon;
    }

    /**
     * Human-readable failure reason, used by the checkout's coupon field so the
     * traveller is told *why* rather than just "invalid code".
     *
     * @param  Collection<int, BookingItem>  $items
     */
    public function reasonFor(string $code, ?User $user, int $subtotalMinor, Collection $items): ?string
    {
        $code = trim($code);

        if ($code === '') {
            return null;
        }

        $coupon = Coupon::query()->whereRaw('UPPER(code) = ?', [mb_strtoupper($code)])->first();

        if ($coupon === null) {
            return 'We could not find that coupon code.';
        }

        if (! $coupon->is_active) {
            return 'That coupon is no longer active.';
        }

        if (! $coupon->is_started) {
            return 'That coupon starts on ' . $coupon->starts_at->format('d M Y') . '.';
        }

        if ($coupon->is_expired) {
            return 'That coupon expired on ' . $coupon->expires_at->format('d M Y') . '.';
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return 'That coupon has reached its usage limit.';
        }

        $minimum = Booking::toMinor((float) $coupon->min_order_amount);

        if ($minimum > 0 && $subtotalMinor < $minimum) {
            return 'This coupon needs a minimum order of ' . Money::format($minimum) . '.';
        }

        if ($user !== null && $coupon->per_user_limit > 0) {
            if ($coupon->usages()->where('user_id', $user->getKey())->count() >= $coupon->per_user_limit) {
                return 'You have already used this coupon ' . $coupon->per_user_limit . ' time(s).';
            }
        }

        if (! $this->appliesToItems($coupon, $items)) {
            $types = $coupon->applicable_types;

            if (is_array($types) && $types !== []) {
                $labels = array_map(
                    fn (string $t) => BookingType::tryFrom($t)?->label() ?? $t,
                    $types,
                );

                return 'This coupon only applies to: ' . implode(', ', $labels) . '.';
            }

            return 'This coupon does not apply to the selected destination.';
        }

        return null;
    }

    /**
     * Discount in minor units, respecting the coupon's cap.
     */
    public function discountFor(Coupon $coupon, int $subtotalMinor): int
    {
        $discount = $coupon->type === CouponType::Percentage
            ? Money::percentOf($subtotalMinor, (float) $coupon->value)
            : Booking::toMinor((float) $coupon->value);

        if ($coupon->max_discount !== null && $coupon->max_discount > 0) {
            $discount = min($discount, Booking::toMinor((float) $coupon->max_discount));
        }

        // A coupon can zero an order but never create a credit.
        return min($discount, $subtotalMinor);
    }

    /*
    |--------------------------------------------------------------------------
    | Restrictions
    |--------------------------------------------------------------------------
    */

    /**
     * @param  Collection<int, BookingItem>  $items
     */
    private function appliesToItems(Coupon $coupon, Collection $items): bool
    {
        $types = $coupon->applicable_types;
        $destinations = $coupon->applicable_destinations;

        if ((! is_array($types) || $types === []) && (! is_array($destinations) || $destinations === [])) {
            return true;
        }

        $typeAllowList = is_array($types) ? array_map('strval', $types) : null;
        $destinationAllowList = is_array($destinations) ? array_map('strval', $destinations) : null;

        return $items->contains(function (BookingItem $item) use ($typeAllowList, $destinationAllowList): bool {
            if ($typeAllowList !== null && ! in_array((string) $item->item_type, $typeAllowList, true)) {
                return false;
            }

            if ($destinationAllowList === null) {
                return true;
            }

            $slug = $this->destinationSlugFor($item);

            return $slug === null || in_array($slug, $destinationAllowList, true);
        });
    }

    /** Destination slug behind a line item, when it can be resolved cheaply. */
    private function destinationSlugFor(BookingItem $item): ?string
    {
        $bookable = $item->itemable;

        if ($bookable === null) {
            return null;
        }

        if (isset($bookable->destination)) {
            return $bookable->destination?->slug;
        }

        if (method_exists($bookable, 'slug') && property_exists($bookable, 'destination_id')) {
            return \App\Models\Destination::query()
                ->whereKey($bookable->destination_id)
                ->value('slug');
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Maintenance
    |--------------------------------------------------------------------------
    */

    /**
     * Coupons whose window has closed — the scheduler archives these so the
     * admin list and the checkout suggestions stay short.
     *
     * @return int Number archived.
     */
    public function archiveExpired(): int
    {
        return Coupon::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['is_active' => false]);
    }
}
