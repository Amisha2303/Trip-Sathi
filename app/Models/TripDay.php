<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One calendar day of a trip itinerary.
 */
class TripDay extends Model
{
    protected $fillable = ['trip_id', 'day_number', 'date', 'title', 'notes'];

    protected function casts(): array
    {
        return [
            'day_number' => 'integer',
            'date' => 'date',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TripItem::class)->orderBy('sort_order');
    }

    public function getDateLabelAttribute(): string
    {
        return $this->date?->format('D, d M Y') ?? '';
    }
}
