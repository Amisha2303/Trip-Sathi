<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * A reusable feature/amenity tag (Free WiFi, Pool, Visa accepted …).
 *
 * Attached through the polymorphic `amenity_amenity` pivot so hotels, rooms and
 * adventures can share one taxonomy.
 */
class Amenity extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = ['name', 'slug', 'icon', 'group', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function hotels(): MorphToMany
    {
        return $this->morphedByMany(Hotel::class, 'ameniable', 'amenity_amenity');
    }

    public function rooms(): MorphToMany
    {
        return $this->morphedByMany(Room::class, 'ameniable', 'amenity_amenity');
    }

    public function adventures(): MorphToMany
    {
        return $this->morphedByMany(Adventure::class, 'ameniable', 'amenity_amenity');
    }

    public function scopeInGroup(Builder $query, ?string $group): Builder
    {
        return $query->when($group, fn (Builder $q) => $q->where('group', $group));
    }

    /** Suggested amenity vocabulary used by the seeder and the admin form. */
    public static function groups(): array
    {
        return [
            'general' => 'General',
            'connectivity' => 'Connectivity',
            'comfort' => 'Comfort',
            'food' => 'Food & Drink',
            'facilities' => 'Facilities',
            'safety' => 'Safety & Adventure',
            'accessibility' => 'Accessibility',
        ];
    }

    public function activityModule(): string
    {
        return 'amenities';
    }
}
