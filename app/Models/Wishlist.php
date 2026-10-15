<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WishlistType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A saved catalogue item for a traveller.
 *
 * The unique index on (user_id, wishlistable) is what makes the AJAX toggle
 * idempotent — a double click cannot create two rows.
 */
class Wishlist extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = ['user_id', 'wishlistable_type', 'wishlistable_id', 'type'];

    protected function casts(): array
    {
        return ['type' => WishlistType::class];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wishlistable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getLabelAttribute(): string
    {
        return $this->wishlistable?->name ?? $this->type?->label() ?? 'Saved item';
    }

    public function getImageUrlAttribute(): string
    {
        return $this->wishlistable?->image_url ?? self::imageUrl(null, 'images/placeholder.svg');
    }

    public function getUrlAttribute(): string
    {
        $item = $this->wishlistable;

        if ($item === null) {
            return '#';
        }

        $route = match ($this->type) {
            WishlistType::Destination => 'destinations.show',
            WishlistType::Hotel => 'hotels.show',
            WishlistType::Restaurant => 'restaurants.show',
            WishlistType::Event => 'events.show',
            WishlistType::Adventure => 'adventures.show',
            WishlistType::Darshan => 'darshan.show',
            WishlistType::Pooja => 'poojas.show',
            default => null,
        };

        return $route ? route($route, $item) : '#';
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return $query->when($type, fn (Builder $q) => $q->where('type', $type));
    }

    public function activityModule(): string
    {
        return 'wishlists';
    }
}
