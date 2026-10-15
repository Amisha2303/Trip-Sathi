<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TripItemType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A traveller-authored trip.
 *
 * Days are materialised on save by TripService so the itinerary can be reordered
 * with a single indexed UPDATE rather than recomputed on every read.
 *
 * @property string $name
 */
class Trip extends BaseModel
{
    protected $fillable = [
        'user_id', 'destination_id', 'name', 'slug', 'description',
        'start_date', 'end_date', 'total_days', 'travelers', 'budget',
        'currency', 'notes', 'cover_image', 'is_public', 'is_completed',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'completed_at' => 'datetime',
            'total_days' => 'integer',
            'travelers' => 'integer',
            'budget' => 'integer',
            'is_public' => 'boolean',
            'is_completed' => 'boolean',
        ];
    }

    protected $appends = ['image_url', 'date_range_label'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function days(): HasMany
    {
        return $this->hasMany(TripDay::class)->orderBy('day_number');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TripItem::class)->orderBy('day_number')->orderBy('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute(): string
    {
        return self::imageUrl(
            $this->cover_image ?? $this->destination?->main_image,
            'images/trip-placeholder.svg',
        );
    }

    public function getDateRangeLabelAttribute(): string
    {
        if (! $this->start_date || ! $this->end_date) {
            return 'Dates not set';
        }

        if ($this->start_date->isSameDay($this->end_date)) {
            return $this->start_date->format('d M Y');
        }

        return $this->start_date->format('d M') . ' – ' . $this->end_date->format('d M Y');
    }

    public function getDurationLabelAttribute(): string
    {
        $days = (int) $this->total_days;

        return $days <= 1 ? 'Day trip' : $days . '-day trip';
    }

    public function isUpcoming(): bool
    {
        return $this->start_date instanceof Carbon && $this->start_date->isFuture();
    }

    /** Total planned cost from itinerary items, in rupees. */
    public function plannedSpend(): float
    {
        return Booking::toRupees((int) $this->items()->sum('estimated_cost'));
    }

    /** Budget consumed, clamped at 100 for the progress bar. */
    public function budgetUsedPercent(): int
    {
        $budget = (int) $this->budget;

        if ($budget <= 0) {
            return 0;
        }

        return (int) min(100, round(($this->plannedSpend() / Booking::toRupees($budget)) * 100));
    }

    /**
     * Items grouped by day for rendering the itinerary without N+1 queries.
     *
     * @return \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<int, self>>
     */
    public function itineraryByDay(): mixed
    {
        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->with(['itemable'])->get();

        return $items
            ->groupBy('day_number')
            ->sortKeys()
            ->map(fn ($group) => $group->sortBy('sort_order')->values());
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('end_date', '>=', today());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->whereDate('end_date', '<', today());
    }

    public function scopeShared(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function activityModule(): string
    {
        return 'trips';
    }
}
