<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * A dated, capacity-limited departure for an adventure.
 *
 * Availability is derived from live booking_items rather than the `booked`
 * counter so a cancellation frees seats immediately; the counter exists purely
 * for fast "is it full" reads on listing pages.
 */
class AdventureSlot extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = [
        'adventure_id', 'slot_date', 'start_time', 'end_time',
        'capacity', 'booked', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'slot_date' => 'date',
            'capacity' => 'integer',
            'booked' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['label', 'time_label'];

    public function adventure(): BelongsTo
    {
        return $this->belongsTo(Adventure::class);
    }

    public function getTimeLabelAttribute(): string
    {
        return Carbon::parse((string) $this->start_time)->format('g:i A')
            . ' – ' . Carbon::parse((string) $this->end_time)->format('g:i A');
    }

    public function getLabelAttribute(): string
    {
        return $this->slot_date->format('D, d M') . ' · ' . $this->time_label;
    }

    public function bookedSeats(): int
    {
        return (int) DB::table('booking_items')
            ->join('bookings', 'bookings.id', '=', 'booking_items.booking_id')
            ->where('booking_items.slot_table', static::class)
            ->where('booking_items.slot_id', $this->getKey())
            ->whereIn('bookings.status', ['pending', 'confirmed', 'processing', 'completed'])
            ->sum('booking_items.quantity');
    }

    public function remaining(): int
    {
        return max(0, (int) $this->capacity - $this->bookedSeats());
    }

    public function isPast(): bool
    {
        return Carbon::parse($this->slot_date->toDateString() . ' ' . $this->start_time)->isPast();
    }

    public function isAvailable(int $wanted = 1): bool
    {
        return $this->is_active && ! $this->isPast() && $this->remaining() >= $wanted;
    }

    public function activityModule(): string
    {
        return 'adventure_slots';
    }
}
