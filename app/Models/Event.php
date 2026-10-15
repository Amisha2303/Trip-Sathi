<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * A scheduled event with capacity-tracked ticket types.
 *
 * `sold_count` and `reserved_count` are maintained inside the booking
 * transaction with a row lock; `remainingCapacity()` re-reads live state so the
 * UI can never show a stale number after a concurrent purchase.
 *
 * @property string $slug
 */
class Event extends BaseModel
{
    protected $fillable = [
        'destination_id', 'category_id', 'name', 'slug', 'description', 'short_description',
        'venue', 'address', 'city', 'latitude', 'longitude', 'organiser',
        'starts_at', 'ends_at', 'event_date', 'timezone',
        'capacity', 'base_price', 'discount_percent', 'main_image', 'terms',
        'is_featured', 'is_active', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'event_date' => 'date',
            'latitude' => 'float',
            'longitude' => 'float',
            'capacity' => 'integer',
            'sold_count' => 'integer',
            'reserved_count' => 'integer',
            'base_price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['image_url', 'effective_price'];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(EventImage::class)->ordered();
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(EventTicketType::class)->orderBy('sort_order');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'amenity_amenity', 'ameniable_id')
            ->where('amenity_amenity.ameniable_type', static::class)
            ->withTimestamps();
    }

    public function bookings(): MorphMany
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'subject');
    }

    public function wishlistEntries(): MorphMany
    {
        return $this->morphMany(Wishlist::class, 'wishlistable');
    }

    public function tripItems(): MorphMany
    {
        return $this->morphMany(TripItem::class, 'itemable');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute(): string
    {
        return self::imageUrl($this->main_image, 'images/event-placeholder.svg');
    }

    public function getEffectivePriceAttribute(): float
    {
        return round((float) $this->base_price * (1 - ((float) $this->discount_percent / 100)), 2);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getTimeRangeAttribute(): string
    {
        return $this->starts_at->format('g:i A') . ' – ' . $this->ends_at->format('g:i A');
    }

    public function getIsPastAttribute(): bool
    {
        return $this->ends_at->isPast();
    }

    public function getIsSoldOutAttribute(): bool
    {
        return $this->remainingCapacity() <= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Capacity
    |--------------------------------------------------------------------------
    */

    /** Seats left, recomputed from live ticket rows so it is never stale. */
    public function remainingCapacity(): int
    {
        $capacity = (int) $this->ticketTypes()->sum('capacity');
        $sold = (int) $this->ticketTypes()->sum('sold_count');

        return max(0, ($capacity ?: (int) $this->capacity) - $sold);
    }

    /**
     * Seats left on one ticket type. Called inside the booking transaction
     * after `lockForUpdate()` on the ticket row.
     */
    public function remainingForTicketType(EventTicketType $type): int
    {
        $sold = (int) DB::table('booking_items')
            ->join('bookings', 'bookings.id', '=', 'booking_items.booking_id')
            ->where('booking_items.itemable_type', $type->getMorphClass())
            ->where('booking_items.itemable_id', $type->getKey())
            ->whereIn('bookings.status', ['pending', 'confirmed', 'processing', 'completed'])
            ->sum('booking_items.quantity');

        return max(0, (int) $type->capacity - $sold);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>=', now());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('starts_at', '<', now());
    }

    public function scopeOnDate(Builder $query, string|Carbon $date): Builder
    {
        return $query->whereDate('event_date', Carbon::parse($date)->toDateString());
    }

    public function scopeSearch(Builder $query, ?string $term, array $columns = ['name', 'venue', 'city', 'short_description']): Builder
    {
        return parent::scopeSearch($query, $term, $columns);
    }

    public function scopePriceBetween(Builder $query, float|int|null $min, float|int|null $max): Builder
    {
        return $query
            ->when($min !== null, fn (Builder $q) => $q->where('base_price', '>=', (float) $min))
            ->when($max !== null, fn (Builder $q) => $q->where('base_price', '<=', (float) $max));
    }

    public function bookingType(): BookingType
    {
        return BookingType::Event;
    }

    public function activityModule(): string
    {
        return 'events';
    }
}
