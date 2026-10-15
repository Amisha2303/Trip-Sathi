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
use Illuminate\Support\Collection;

/**
 * A bookable restaurant offering table reservations and tasting experiences.
 *
 * @property string $slug
 */
class Restaurant extends BaseModel
{
    protected $fillable = [
        'destination_id', 'category_id', 'name', 'slug', 'description', 'short_description',
        'address', 'city', 'state', 'pincode', 'latitude', 'longitude',
        'rating', 'review_count', 'cuisine', 'price_range', 'average_cost',
        'main_image', 'contact_phone', 'contact_email', 'website',
        'opens_at', 'closes_at', 'total_tables', 'reservation_slot_minutes',
        'online_reservation', 'offers', 'is_featured', 'is_active',
        'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'rating' => 'decimal:2',
            'review_count' => 'integer',
            'average_cost' => 'decimal:2',
            'total_tables' => 'integer',
            'reservation_slot_minutes' => 'integer',
            'online_reservation' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['image_url'];

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
        return $this->hasMany(RestaurantImage::class)->ordered();
    }

    public function menus(): HasMany
    {
        return $this->hasMany(RestaurantMenu::class)->orderBy('section')->orderBy('sort_order');
    }

    public function slots(): MorphMany
    {
        return $this->morphMany(RestaurantSlot::class, 'slottable');
    }

    public function foodExperiences(): HasMany
    {
        return $this->hasMany(FoodExperience::class);
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

    public function getImageUrlAttribute(): string
    {
        return self::imageUrl($this->main_image, 'images/restaurant-placeholder.svg');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Slots available on a date, as [id => "12:00 – 13:30 (4 left)"] labels. */
    public function slotOptionsFor(string $date): Collection
    {
        return $this->slots()
            ->where('is_active', true)
            ->whereTime('start_time', '>=', (string) $this->opens_at)
            ->whereTime('end_time', '<=', (string) $this->closes_at)
            ->get()
            ->filter(fn (RestaurantSlot $slot) => $slot->hasCapacity($date))
            ->mapWithKeys(fn (RestaurantSlot $slot) => [
                $slot->id => $slot->label(),
            ])
            ->values();
    }

    public function scopeSearch(Builder $query, ?string $term, array $columns = ['name', 'cuisine', 'city', 'short_description']): Builder
    {
        return parent::scopeSearch($query, $term, $columns);
    }

    public function scopeByRating(Builder $query, float|int|null $min): Builder
    {
        return $query->when($min !== null, fn (Builder $q) => $q->where('rating', '>=', (float) $min));
    }

    public function bookingType(): BookingType
    {
        return BookingType::Restaurant;
    }

    public function activityModule(): string
    {
        return 'restaurants';
    }

    /** Opening hours as a human sentence for the detail page. */
    public function getOpeningHoursAttribute(): string
    {
        return Carbon::parse((string) $this->opens_at)->format('g:i A')
            . ' – ' . Carbon::parse((string) $this->closes_at)->format('g:i A');
    }
}
