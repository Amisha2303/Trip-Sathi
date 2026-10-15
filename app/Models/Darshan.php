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
 * A VIP darshan product: temple, deity, slot-based visitor access.
 *
 * @property string $slug
 */
class Darshan extends BaseModel
{
    protected $table = 'darshan';

    protected $fillable = [
        'destination_id', 'category_id', 'name', 'slug', 'description', 'short_description',
        'temple_name', 'deity', 'darshan_type', 'temple_address', 'latitude', 'longitude',
        'price', 'discount_percent', 'duration_minutes',
        'max_visitors_per_booking', 'max_visitors_per_day',
        'instructions', 'documents_required', 'dress_code', 'cancellation_policy',
        'main_image', 'is_featured', 'is_active', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'duration_minutes' => 'integer',
            'max_visitors_per_booking' => 'integer',
            'max_visitors_per_day' => 'integer',
            'documents_required' => 'array',
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
        return $this->hasMany(DarshanImage::class)->ordered();
    }

    public function slots(): HasMany
    {
        return $this->hasMany(DarshanSlot::class)->orderBy('slot_date')->orderBy('start_time');
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
        return self::imageUrl($this->main_image, 'images/darshan-placeholder.svg');
    }

    public function getEffectivePriceAttribute(): float
    {
        return round((float) $this->price * (1 - ((float) $this->discount_percent / 100)), 2);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Documents a visitor must carry, as a bullet list. */
    public function getDocumentsListAttribute(): array
    {
        $docs = $this->documents_required;

        if (is_string($docs)) {
            $decoded = json_decode($docs, true);
            $docs = is_array($decoded) ? $decoded : [];
        }

        return is_array($docs) ? array_values(array_filter($docs)) : [];
    }

    public function requiresIdProof(): bool
    {
        return $this->documents_list !== [];
    }

    public function nextSlot(): ?DarshanSlot
    {
        return $this->slots()
            ->where('is_active', true)
            ->where('slot_date', '>=', today()->toDateString())
            ->whereColumn('booked_visitors', '<', 'max_visitors')
            ->orderBy('slot_date')
            ->orderBy('start_time')
            ->first();
    }

    public function scopeSearch(Builder $query, ?string $term, array $columns = ['name', 'temple_name', 'deity', 'short_description']): Builder
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
        return BookingType::Darshan;
    }

    public function activityModule(): string
    {
        return 'darshan';
    }
}
