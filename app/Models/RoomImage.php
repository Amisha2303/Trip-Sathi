<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Gallery image belonging to a hotel room.
 */
class RoomImage extends ImageModel
{
    protected $table = 'room_images';

    protected function casts(): array
    {
        return parent::casts();
    }

    public function ownerModel(): string
    {
        return Room::class;
    }

    public function ownerForeignKey(): string
    {
        return 'room_id';
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
