<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A refund issued against a booking's settled payment.
 */
class Refund extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = [
        'booking_id', 'payment_id', 'processed_by', 'gateway', 'reference',
        'amount', 'currency', 'status', 'reason', 'notes', 'response', 'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'response' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    protected $appends = ['amount_rupees'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getAmountRupeesAttribute(): float
    {
        return Booking::toRupees($this->amount);
    }

    public function isSucceeded(): bool
    {
        return $this->status === 'succeeded';
    }

    public function activityModule(): string
    {
        return 'refunds';
    }
}
