<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Cache;

/**
 * A travel destination — the hub every other catalogue entity hangs off.
 *
 * @property string $slug
 * @property bool $is_active
 * @property bool $is_featured
 */
class Destination extends BaseModel
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'country', 'state', 'city',
        'description', 'short_description', 'best_time_to_visit',
        'main_image', 'latitude', 'longitude', 'currency', 'language', 'timezone',
        'best_months', 'popularity', 'is_featured', 'is_active',
        'seo_title', 'seo_description', 'seo_keywords',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'best_months' => 'integer',
            'popularity' => 'integer',
            'view_count' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['image_url'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(DestinationImage::class)->ordered();
    }

    public function places(): HasMany
    {
        return $this->hasMany(DestinationPlace::class)->orderBy('sort_order');
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    public function foodExperiences(): HasMany
    {
        return $this->hasMany(FoodExperience::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function adventures(): HasMany
    {
        return $this->hasMany(Adventure::class);
    }

    public function darshans(): HasMany
    {
        return $this->hasMany(Darshan::class);
    }

    public function poojas(): HasMany
    {
        return $this->hasMany(Pooja::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'subject');
    }

    public function wishlistEntries(): MorphMany
    {
        return $this->morphMany(Wishlist::class, 'wishlistable');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute(): string
    {
        return self::imageUrl($this->main_image, 'images/destination-placeholder.svg');
    }

    /** "Ahmedabad, Gujarat, India" — used on cards and page titles. */
    public function getLocationAttribute(): string
    {
        return collect([$this->city, $this->state, $this->country])
            ->filter()
            ->unique()
            ->implode(', ');
    }

    /** OpenStreetMap embed URL — no API key required. */
    public function getMapEmbedUrlAttribute(): ?string
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        $delta = 0.02;

        return sprintf(
            'https://www.openstreetmap.org/export/embed.html?bbox=%F,%F,%F,%F&layer=mapnik&marker=%F,%F',
            $this->longitude - $delta,
            $this->latitude - $delta,
            $this->longitude + $delta,
            $this->latitude + $delta,
            $this->latitude,
            $this->longitude,
        );
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Increment the view counter without touching `updated_at`. */
    public function recordView(): void
    {
        $this->incrementQuietly('view_count');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('popularity')->orderByDesc('view_count');
    }

    public function scopeSearch(Builder $query, ?string $term, array $columns = ['name', 'city', 'state', 'short_description']): Builder
    {
        return parent::scopeSearch($query, $term, $columns);
    }

    /** Cache key for the homepage "popular destinations" rail. */
    public static function popularCacheKey(): string
    {
        return 'destinations.popular';
    }

    /** @return \Illuminate\Support\Collection<int, self> */
    public static function cachedPopular(int $limit = 8): mixed
    {
        return Cache::remember(
            static::popularCacheKey() . '.' . $limit,
            (int) config('tripsathi.cache.destinations', 1800),
            fn () => static::query()
                ->active()
                ->with('category')
                ->popular()
                ->limit($limit)
                ->get(),
        );
    }

    /** Busted whenever a destination is created, updated or deleted. */
    public static function flushCatalogueCache(): void
    {
        Cache::forget(static::popularCacheKey() . '.8');
        Cache::forget('destinations.search_options');
        Cache::forget('settings.all');
    }
}
