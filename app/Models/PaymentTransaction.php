<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An append-only audit row for every gateway request/response.
 *
 * `request` and `response` deliberately exclude credentials: the gateway
 * adapters strip keys and signatures before the payload reaches this model.
 */
class PaymentTransaction extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = [
        'payment_id', 'booking_id', 'gateway', 'reference', 'type',
        'status', 'amount', 'currency', 'request', 'response', 'error',
    ];

    protected function casts(): array
    {
        return [
            'status' => TransactionStatus::class,
            'amount' => 'integer',
            'request' => 'array',
            'response' => 'array',
        ];
    }

    protected $appends = ['amount_rupees'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getAmountRupeesAttribute(): float
    {
        return Booking::toRupees($this->amount);
    }

    public function activityModule(): string
    {
        return 'payment_transactions';
    }
}
