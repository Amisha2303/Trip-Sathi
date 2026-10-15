<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A catalogue category.
 *
 * `type` scopes a category to one product family (destination, hotel, event …)
 * so the same taxonomy table serves every filter bar without a second table.
 */
class Category extends BaseModel
{
    protected $fillable = [
        'parent_id', 'name', 'slug', 'type', 'description', 'icon', 'image',
        'sort_order', 'is_active', 'is_featured', 'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    protected $appends = ['image_url'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    public function restaurants(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function adventures(): HasMany
    {
        return $this->hasMany(Adventure::class);
    }

    public function getImageUrlAttribute(): string
    {
        return self::imageUrl($this->image, 'images/category-placeholder.svg');
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return $query->when($type, fn (Builder $q) => $q->where('type', $type));
    }

    /** Category families recognised by the catalogue. */
    public static function types(): array
    {
        return [
            'destination' => 'Destination',
            'hotel' => 'Hotel',
            'restaurant' => 'Restaurant',
            'food' => 'Food Experience',
            'event' => 'Event',
            'adventure' => 'Adventure',
            'darshan' => 'VIP Darshan',
            'pooja' => 'Pooja Vidhi',
            'trip' => 'Trip Theme',
        ];
    }

    public function activityModule(): string
    {
        return 'categories';
    }
}
