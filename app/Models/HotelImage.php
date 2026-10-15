<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Gallery image belonging to a hotel.
 */
class HotelImage extends ImageModel
{
    protected $table = 'hotel_images';

    protected function casts(): array
    {
        return parent::casts();
    }

    public function ownerModel(): string
    {
        return Hotel::class;
    }

    public function ownerForeignKey(): string
    {
        return 'hotel_id';
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
}
