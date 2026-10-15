<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A discount code.
 *
 * Validation lives in CouponService — the model only holds state and exposes
 * cheap `isValidOn()` checks for the booking form.
 */
class Coupon extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = [
        'code', 'name', 'description', 'type', 'value', 'max_discount', 'min_order_amount',
        'applicable_types', 'applicable_destinations', 'starts_at', 'expires_at',
        'usage_limit', 'used_count', 'per_user_limit', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => CouponType::class,
            'value' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'applicable_types' => 'array',
            'applicable_destinations' => 'array',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'per_user_limit' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['value_label', 'is_expired'];

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Presentation
    |--------------------------------------------------------------------------
    */

    public function getValueLabelAttribute(): string
    {
        $symbol = config('tripsathi.currency_symbol');

        return $this->type === CouponType::Percentage
            ? rtrim(rtrim((string) $this->value, '0'), '.') . '% OFF'
            : $symbol . number_format((float) $this->value, 0);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function getIsStartedAttribute(): bool
    {
        return $this->starts_at === null || $this->starts_at->isPast();
    }

    public function usesRemaining(): ?int
    {
        return $this->usage_limit === null ? null : max(0, $this->usage_limit - $this->used_count);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeLive(Builder $query, ?Carbon $at = null): Builder
    {
        $at ??= now();

        return $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $at))
            ->where(fn (Builder $q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', $at))
            ->where(fn (Builder $q) => $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit'));
    }

    public function activityModule(): string
    {
        return 'coupons';
    }
}
