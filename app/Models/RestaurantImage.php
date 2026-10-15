<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Gallery image belonging to a restaurant.
 */
class RestaurantImage extends ImageModel
{
    protected $table = 'restaurant_images';

    protected function casts(): array
    {
        return parent::casts();
    }

    public function ownerModel(): string
    {
        return Restaurant::class;
    }

    public function ownerForeignKey(): string
    {
        return 'restaurant_id';
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }
}
