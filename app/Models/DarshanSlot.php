<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * A dated visitor-capacity window for a VIP darshan product.
 */
class DarshanSlot extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = [
        'darshan_id', 'slot_date', 'start_time', 'end_time',
        'max_visitors', 'booked_visitors', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'slot_date' => 'date',
            'max_visitors' => 'integer',
            'booked_visitors' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['label', 'time_label'];

    public function darshan(): BelongsTo
    {
        return $this->belongsTo(Darshan::class);
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

    public function bookedVisitors(): int
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
        return max(0, (int) $this->max_visitors - $this->bookedVisitors());
    }

    public function isPast(): bool
    {
        return Carbon::parse($this->slot_date->toDateString() . ' ' . $this->start_time)->isPast();
    }

    public function isAvailable(int $visitors = 1): bool
    {
        return $this->is_active && ! $this->isPast() && $this->remaining() >= $visitors;
    }

    public function activityModule(): string
    {
        return 'darshan_slots';
    }
}
