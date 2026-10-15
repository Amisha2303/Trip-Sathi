<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * A priced ticket tier for an event (Early Bird, Regular, VIP …).
 *
 * `capacity` is the hard ceiling for this tier. Availability is re-derived from
 * live booking_items inside the booking transaction so a cancelled booking frees
 * a seat again.
 */
class EventTicketType extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = [
        'event_id', 'name', 'description', 'price', 'discount_percent',
        'capacity', 'sold_count', 'per_order_limit', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'capacity' => 'integer',
            'sold_count' => 'integer',
            'per_order_limit' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected $appends = ['effective_price'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getEffectivePriceAttribute(): float
    {
        return round((float) $this->price * (1 - ((float) $this->discount_percent / 100)), 2);
    }

    /** Seats sold for this tier, from live bookings. */
    public function sold(): int
    {
        return (int) DB::table('booking_items')
            ->join('bookings', 'bookings.id', '=', 'booking_items.booking_id')
            ->where('booking_items.itemable_type', $this->getMorphClass())
            ->where('booking_items.itemable_id', $this->getKey())
            ->whereIn('bookings.status', ['pending', 'confirmed', 'processing', 'completed'])
            ->sum('booking_items.quantity');
    }

    public function remaining(): int
    {
        return max(0, (int) $this->capacity - $this->sold());
    }

    public function isSoldOut(): bool
    {
        return $this->remaining() <= 0;
    }

    public function activityModule(): string
    {
        return 'event_ticket_types';
    }
}
