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
 * A bookable Pooja Vidhi (ritual offering) at a temple.
 *
 * @property string $slug
 */
class Pooja extends BaseModel
{
    protected $fillable = [
        'destination_id', 'category_id', 'name', 'slug', 'description', 'short_description',
        'temple_name', 'deity', 'benefits', 'duration_minutes',
        'max_participants', 'min_participants', 'price', 'discount_percent',
        'requirements', 'includes', 'process', 'dress_code', 'cancellation_policy',
        'main_image', 'is_featured', 'is_active', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'duration_minutes' => 'integer',
            'max_participants' => 'integer',
            'min_participants' => 'integer',
            'includes' => 'array',
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
        return $this->hasMany(PoojaImage::class)->ordered();
    }

    public function slots(): HasMany
    {
        return $this->hasMany(PoojaSlot::class)->orderBy('slot_date')->orderBy('start_time');
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
        return self::imageUrl($this->attributes['main_image'] ?? null, 'images/pooja-placeholder.svg');
    }

    public function getEffectivePriceAttribute(): float
    {
        return round((float) $this->price * (1 - ((float) $this->discount_percent / 100)), 2);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Included items as bullets (samagri, prasad, gift). */
    public function getIncludesListAttribute(): array
    {
        $includes = $this->includes;

        if (is_string($includes)) {
            $decoded = json_decode($includes, true);
            $includes = is_array($decoded) ? $decoded : [];
        }

        return is_array($includes) ? array_values(array_filter($includes)) : [];
    }

    public function nextSlot(): ?PoojaSlot
    {
        return $this->slots()
            ->where('is_active', true)
            ->where('slot_date', '>=', today()->toDateString())
            ->whereColumn('booked_participants', '<', 'max_participants')
            ->orderBy('slot_date')
            ->orderBy('start_time')
            ->first();
    }

    public function scopeSearch(Builder $query, ?string $term, array $columns = ['name', 'temple_name', 'deity', 'benefits']): Builder
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
        return BookingType::Pooja;
    }

    public function activityModule(): string
    {
        return 'poojas';
    }
}
