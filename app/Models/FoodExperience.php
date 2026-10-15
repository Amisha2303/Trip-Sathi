<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * A bookable culinary experience: cooking class, tasting walk, thali trail.
 *
 * @property string $slug
 */
class FoodExperience extends BaseModel
{
    protected $fillable = [
        'destination_id', 'restaurant_id', 'category_id', 'name', 'slug',
        'description', 'short_description', 'main_image',
        'price', 'discount_percent', 'duration_minutes',
        'max_guests', 'min_guests', 'cuisine', 'includes', 'menu_highlights',
        'safety_notes', 'is_featured', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'duration_minutes' => 'integer',
            'max_guests' => 'integer',
            'min_guests' => 'integer',
            'includes' => 'array',
            'menu_highlights' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['image_url', 'effective_price'];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(FoodExperienceImage::class)->ordered();
    }

    public function slots(): MorphMany
    {
        return $this->morphMany(RestaurantSlot::class, 'slottable');
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
        return self::imageUrl($this->main_image, 'images/food-placeholder.svg');
    }

    public function getEffectivePriceAttribute(): float
    {
        return round((float) $this->price * (1 - ((float) $this->discount_percent / 100)), 2);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeSearch(Builder $query, ?string $term, array $columns = ['name', 'cuisine', 'short_description']): Builder
    {
        return parent::scopeSearch($query, $term, $columns);
    }

    public function scopePriceBetween(Builder $query, float|int|null $min, float|int|null $max): Builder
    {
        return $query
            ->when($min !== null, fn (Builder $q) => $q->where('price', '>=', (float) $min))
            ->when($max !== null, fn (Builder $q) => $q->where('price', '<=', (float) $max));
    }

    public function bookingType(): BookingType
    {
        return BookingType::FoodExperience;
    }

    public function activityModule(): string
    {
        return 'food_experiences';
    }
}
