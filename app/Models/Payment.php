<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A payment attempt against a booking.
 *
 * Only one settled payment counts towards revenue; the analytics queries filter
 * on `status = paid` so a failed retry never double counts.
 */
class Payment extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = [
        'booking_id', 'user_id', 'gateway', 'transaction_reference', 'status',
        'amount', 'amount_refunded', 'currency', 'payload', 'paid_at', 'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount' => 'integer',
            'amount_refunded' => 'integer',
            'payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    protected $appends = ['amount_rupees', 'amount_refunded_rupees'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function getAmountRupeesAttribute(): float
    {
        return Booking::toRupees($this->attributes['amount'] ?? 0);
    }

    public function getAmountRefundedRupeesAttribute(): float
    {
        return Booking::toRupees($this->attributes['amount_refunded'] ?? 0);
    }

    public function getIsRefundedAttribute(): bool
    {
        return $this->status === PaymentStatus::Refunded;
    }

    /** Method used on the invoice — never a gateway secret. */
    public function getMethodLabelAttribute(): string
    {
        return match ($this->gateway) {
            'razorpay' => 'Razorpay',
            'stripe' => 'Stripe Card',
            'offline' => 'Offline / Manual',
            default => ucfirst((string) $this->gateway),
        };
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->whereIn('status', [
            PaymentStatus::Paid->value,
            PaymentStatus::PartiallyRefunded->value,
            PaymentStatus::Refunded->value,
        ]);
    }

    public function activityModule(): string
    {
        return 'payments';
    }
}
