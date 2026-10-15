<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * A "don't miss" landmark shown on the destination page.
 */
class DestinationPlace extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = [
        'destination_id', 'name', 'description', 'image', 'address',
        'latitude', 'longitude', 'opening_hours', 'entry_fee', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'sort_order' => 'integer',
        ];
    }

    protected $appends = ['image_url'];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function getImageUrlAttribute(): string
    {
        return self::imageUrl($this->image, 'images/place-placeholder.svg');
    }

    public function activityModule(): string
    {
        return 'destination_places';
    }
}
