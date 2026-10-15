<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TripItemType;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A single stop on a trip itinerary.
 *
 * Catalogue-backed items store the record in `itemable` and mirror its name into
 * `title` so a renamed hotel does not rewrite the traveller's saved plan.
 * `sort_order` within a day is what drag-and-drop rewrites.
 */
class TripItem extends Model
{
    use LogsActivity;

    public static bool $usesSlug = false;

    protected $fillable = [
        'trip_id', 'trip_day_id', 'day_number', 'item_type', 'itemable_type', 'itemable_id',
        'booking_id', 'title', 'description', 'image', 'location',
        'start_time', 'end_time', 'duration_minutes', 'estimated_cost',
        'currency', 'meta', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'day_number' => 'integer',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'duration_minutes' => 'integer',
            'estimated_cost' => 'decimal:2',
            'meta' => 'array',
            'sort_order' => 'integer',
        ];
    }

    protected $appends = ['icon'];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(TripDay::class, 'trip_day_id');
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo('itemable');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Presentation
    |--------------------------------------------------------------------------
    */

    public function type(): ?TripItemType
    {
        return TripItemType::tryFrom((string) $this->item_type);
    }

    public function getIconAttribute(): string
    {
        return $this->type()?->icon() ?? 'bi-pin-map';
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type()?->label() ?? 'Item';
    }

    public function getTimeLabelAttribute(): ?string
    {
        if (! $this->start_time) {
            return null;
        }

        $start = \Illuminate\Support\Carbon::parse($this->start_time);

        return $this->end_time
            ? $start->format('g:i A') . ' – ' . \Illuminate\Support\Carbon::parse($this->end_time)->format('g:i A')
            : $start->format('g:i A');
    }

    public function getImageUrlAttribute(): string
    {
        return self::imageUrl($this->image, 'images/trip-item-placeholder.svg');
    }

    /** True when the traveller has already paid for this stop. */
    public function isBooked(): bool
    {
        return $this->booking_id !== null;
    }

    public function activityModule(): string
    {
        return 'trip_items';
    }
}
