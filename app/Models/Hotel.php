<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * A bookable hotel.
 *
 * @property string $slug
 * @property decimal $rating
 * @property decimal $base_price
 */
class Hotel extends BaseModel
{
    protected $fillable = [
        'destination_id', 'category_id', 'name', 'slug', 'description', 'short_description',
        'address', 'city', 'state', 'pincode', 'latitude', 'longitude',
        'rating', 'review_count', 'star_rating', 'main_image',
        'contact_phone', 'contact_email', 'website',
        'check_in_time', 'check_out_time', 'min_nights', 'max_nights',
        'cancellation_policy', 'policies', 'base_price', 'discount_percent',
        'is_featured', 'is_active', 'seo_title', 'seo_description', 'seo_keywords',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'rating' => 'decimal:2',
            'review_count' => 'integer',
            'star_rating' => 'integer',
            'base_price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'min_nights' => 'integer',
            'max_nights' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['image_url', 'effective_price'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

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
        return $this->hasMany(HotelImage::class)->ordered();
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
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
        return self::imageUrl($this->main_image, 'images/hotel-placeholder.svg');
    }

    /** Price once the hotel-level discount is applied. */
    public function getEffectivePriceAttribute(): float
    {
        return round((float) $this->base_price * (1 - ((float) $this->discount_percent / 100)), 2);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Cheapest active room, used for "from ₹X" on cards. */
    public function lowestRoomPrice(): ?float
    {
        if ($this->relationLoaded('rooms')) {
            $prices = $this->rooms
                ->filter(fn (Room $room) => $room->is_active)
                ->map(fn (Room $room) => $room->effective_price)
                ->filter(fn (float $p) => $p > 0);

            return $prices->isEmpty() ? null : (float) $prices->min();
        }

        $price = $this->rooms()
            ->where('is_active', true)
            ->min(\Illuminate\Support\Facades\DB::raw('price * (1 - discount_percent / 100)'));

        return $price === null ? null : round((float) $price, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSearch(Builder $query, ?string $term, array $columns = ['name', 'address', 'city', 'short_description']): Builder
    {
        return parent::scopeSearch($query, $term, $columns);
    }

    public function scopeByRating(Builder $query, float|int|null $min): Builder
    {
        return $query->when($min !== null, fn (Builder $q) => $q->where('rating', '>=', (float) $min));
    }

    public function scopePriceBetween(Builder $query, float|int|null $min, float|int|null $max): Builder
    {
        return $query
            ->when($min !== null, fn (Builder $q) => $q->where('base_price', '>=', (float) $min))
            ->when($max !== null, fn (Builder $q) => $q->where('base_price', '<=', (float) $max));
    }

    /** Booking engine type used by polymorphic `bookable` columns. */
    public function bookingType(): BookingType
    {
        return BookingType::Hotel;
    }
}
