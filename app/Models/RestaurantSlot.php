<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A bookable reservation slot.
 *
 * Shared by restaurants, food experiences and anything else that sells a
 * time-bound table or tasting session. Capacity is enforced by BookingService
 * with a `lockForUpdate()` on this row.
 */
class RestaurantSlot extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = [
        'slottable_type', 'slottable_id', 'start_time', 'end_time', 'capacity', 'tables', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'tables' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['label'];

    public function slottable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function getLabelAttribute(): string
    {
        return \Illuminate\Support\Carbon::parse((string) $this->start_time)->format('g:i A')
            . ' – ' . \Illuminate\Support\Carbon::parse((string) $this->end_time)->format('g:i A');
    }

    /** Free seats on a date. Callers pass the date explicitly to avoid N+1. */
    public function remainingOn(string $date): int
    {
        return max(0, (int) $this->capacity - $this->bookedFor($date));
    }

    /**
     * Seats already committed for a date.
     *
     * Booked seats are summed from live booking_items rather than kept in a
     * counter column, which means a cancelled booking automatically releases
     * inventory and can never drift out of sync.
     */
    public function bookedFor(string $date): int
    {
        return (int) \Illuminate\Support\Facades\DB::table('booking_items')
            ->join('bookings', 'bookings.id', '=', 'booking_items.booking_id')
            ->where('booking_items.slot_table', static::class)
            ->where('booking_items.slot_id', $this->getKey())
            ->whereDate('booking_items.service_date', $date)
            ->whereIn('bookings.status', ['pending', 'confirmed', 'processing', 'completed'])
            ->sum('booking_items.quantity');
    }

    public function hasCapacity(string $date, int $wanted = 1): bool
    {
        return $this->remainingOn($date) >= $wanted;
    }

    public function activityModule(): string
    {
        return 'restaurant_slots';
    }
}
