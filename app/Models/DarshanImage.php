<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Gallery image belonging to a VIP darshan product.
 */
class DarshanImage extends ImageModel
{
    protected $table = 'darshan_images';

    protected function casts(): array
    {
        return parent::casts();
    }

    public function ownerModel(): string
    {
        return Darshan::class;
    }

    public function ownerForeignKey(): string
    {
        return 'darshan_id';
    }

    public function darshan(): BelongsTo
    {
        return $this->belongsTo(Darshan::class, 'darshan_id');
    }
}
