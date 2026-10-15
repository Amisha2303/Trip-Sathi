<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ActivityAction;
use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * The booking header — one row is one invoice.
 *
 * Money is stored in the smallest currency unit (paise for INR) as integers so
 * every total is exact; `*_rupees` accessors return the decimal view for
 * display. Status changes are guarded by `canTransitionTo()` which the admin
 * and customer paths both go through.
 *
 * @property string $booking_number
 * @property int $final_amount
 */
class Booking extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = [
        'booking_number', 'user_id', 'destination_id', 'trip_id', 'coupon_id',
        'booking_type', 'bookable_type', 'bookable_id',
        'customer_name', 'customer_email', 'customer_phone',
        'booking_date', 'start_time', 'end_time', 'end_date', 'nights', 'quantity', 'guests',
        'subtotal', 'discount', 'tax', 'final_amount', 'currency',
        'payment_status', 'status', 'cancelled', 'cancelled_at', 'cancellation_reason',
        'refunded_amount', 'notes', 'admin_notes',
        'confirmed_at', 'completed_at', 'ip_address', 'source',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'end_date' => 'date',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'cancelled' => 'boolean',
            'nights' => 'integer',
            'quantity' => 'integer',
            'guests' => 'integer',
            'subtotal' => 'integer',
            'discount' => 'integer',
            'tax' => 'integer',
            'final_amount' => 'integer',
            'refunded_amount' => 'integer',
            'status' => BookingStatus::class,
            'payment_status' => PaymentStatus::class,
        ];
    }

    protected $appends = ['final_amount_rupees', 'status_label', 'type_label'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /** Single-item fast path used by listings and filters. */
    public function bookable(): MorphTo
    {
        return $this->morphTo();
    }

    public function items(): HasMany
    {
        return $this->hasMany(BookingItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function couponUsage(): HasOne
    {
        return $this->hasOne(CouponUsage::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Money accessors
    |--------------------------------------------------------------------------
    */

    public static function toMinor(int|float|string $rupees): int
    {
        return (int) round((float) $rupees * 100);
    }

    public static function toRupees(int|float|string $minor): float
    {
        return round((float) $minor / 100, 2);
    }

    public function getFinalAmountRupeesAttribute(): float
    {
        return self::toRupees($this->final_amount);
    }

    public function getSubtotalRupeesAttribute(): float
    {
        return self::toRupees($this->subtotal);
    }

    public function getDiscountRupeesAttribute(): float
    {
        return self::toRupees($this->discount);
    }

    public function getTaxRupeesAttribute(): float
    {
        return self::toRupees($this->tax);
    }

    public function getRefundedRupeesAttribute(): float
    {
        return self::toRupees($this->refunded_amount);
    }

    /** Amount still owed after refunds. */
    public function getRefundableAmountAttribute(): float
    {
        return self::toRupees(max(0, (int) $this->final_amount - (int) $this->refunded_amount));
    }

    /*
    |--------------------------------------------------------------------------
    | Status helpers
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        return $this->status?->label() ?? '—';
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->bookingType()?->label() ?? ucfirst((string) $this->booking_type);
    }

    public function bookingType(): ?BookingType
    {
        return BookingType::tryFrom((string) $this->booking_type);
    }

    /** Legal status transitions for the admin status control. */
    public function allowedTransitions(): array
    {
        return match ($this->status) {
            BookingStatus::Pending => [BookingStatus::Confirmed, BookingStatus::Cancelled, BookingStatus::Failed],
            BookingStatus::Confirmed => [BookingStatus::Processing, BookingStatus::Completed, BookingStatus::Cancelled, BookingStatus::Refunded],
            BookingStatus::Processing => [BookingStatus::Completed, BookingStatus::Cancelled],
            BookingStatus::Completed => [BookingStatus::Refunded],
            BookingStatus::Cancelled => [BookingStatus::Refunded],
            BookingStatus::Refunded => [],
            BookingStatus::Failed => [],
            default => [],
        };
    }

    public function canTransitionTo(BookingStatus $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    /**
     * Effective start moment: booking_date combined with start_time when set,
     * otherwise midnight on booking_date.
     */
    public function startsAt(): Carbon
    {
        if (! $this->booking_date instanceof Carbon) {
            return Carbon::parse((string) $this->booking_date)->startOfDay();
        }

        $startTime = $this->start_time;

        return $startTime
            ? $this->booking_date->copy()->setTimeFromTimeString((string) $startTime)
            : $this->booking_date->copy()->startOfDay();
    }

    /** Hours remaining before the customer-facing cancellation window shuts. */
    public function hoursUntilStart(): float
    {
        $start = $this->booking_date instanceof Carbon
            ? $this->booking_date->copy()->setTimeFromTimeString((string) ($this->start_time ?? '00:00:00'))
            : Carbon::parse($this->booking_date . ' ' . ($this->start_time ?? '00:00:00'));

        return round(Carbon::now()->diffInMinutes($start, false) / 60, 2);
    }

    public function isCancellableByCustomer(): bool
    {
        if (! $this->status?->isCancellable()) {
            return false;
        }

        return $this->hoursUntilStart() >= (float) config('tripsathi.cancellation_cutoff_hours', 24);
    }

    public function isUpcoming(): bool
    {
        return $this->booking_date instanceof Carbon
            && $this->booking_date->isFuture()
            && $this->status?->isActive();
    }

    /** Human title: the primary line item name, falling back to the type label. */
    public function getTitleAttribute(): string
    {
        $first = $this->items->first();

        return $first?->name ?? $this->type_label;
    }

    public function getPrimaryItemAttribute(): ?BookingItem
    {
        return $this->relationLoaded('items') ? $this->items->first() : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return $query->when($type, fn (Builder $q) => $q->where('booking_type', $type));
    }

    public function scopeWithStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, fn (Builder $q) => $q->where('status', $status));
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->whereIn('payment_status', [PaymentStatus::Paid->value, PaymentStatus::PartiallyRefunded->value, PaymentStatus::Refunded->value]);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('booking_date', '>=', today())
            ->whereIn('status', [
                BookingStatus::Pending->value,
                BookingStatus::Confirmed->value,
                BookingStatus::Processing->value,
            ]);
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->whereDate('booking_date', '<', today());
    }

    public function scopeBetweenDates(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn (Builder $q) => $q->whereDate('booking_date', '>=', Carbon::parse($from)->toDateString()))
            ->when($to, fn (Builder $q) => $q->whereDate('booking_date', '<=', Carbon::parse($to)->toDateString()));
    }

    public function activityModule(): string
    {
        return 'bookings';
    }

    public function activitySubjectLabel(): string
    {
        return $this->booking_number . ' · ' . $this->type_label;
    }

    /**
     * Change status through the legal-transition guard and log the change.
     * Inventory is not touched here — CancellationService owns that.
     */
    public function transitionTo(BookingStatus $target, ?string $note = null): bool
    {
        if ($target === $this->status) {
            return false;
        }

        if (! $this->canTransitionTo($target)) {
            return false;
        }

        $from = $this->status;

        $attributes = ['status' => $target->value];

        match ($target) {
            BookingStatus::Confirmed => $attributes['confirmed_at'] = now(),
            BookingStatus::Completed => $attributes['completed_at'] = now(),
            BookingStatus::Cancelled => $attributes += [
                'cancelled' => true,
                'cancelled_at' => now(),
            ],
            default => null,
        };

        $this->forceFill($attributes)->save();

        $this->logActivity(
            ActivityAction::StatusChange,
            sprintf('%s → %s%s', $from?->label() ?? '—', $target->label(), $note ? ' · ' . $note : ''),
            ['booking_id' => $this->booking_number],
        );

        return true;
    }
}
