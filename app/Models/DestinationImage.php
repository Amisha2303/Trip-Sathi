<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Gallery image for a destination page.
 */
class DestinationImage extends ImageModel
{
    protected $table = 'destination_images';

    protected function casts(): array
    {
        return parent::casts();
    }

    public function ownerModel(): string
    {
        return Destination::class;
    }

    public function ownerForeignKey(): string
    {
        return 'destination_id';
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }
}
