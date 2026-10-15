<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Gallery image belonging to an adventure activity.
 */
class AdventureImage extends ImageModel
{
    protected $table = 'adventure_images';

    protected function casts(): array
    {
        return parent::casts();
    }

    public function ownerModel(): string
    {
        return Adventure::class;
    }

    public function ownerForeignKey(): string
    {
        return 'adventure_id';
    }

    public function adventure(): BelongsTo
    {
        return $this->belongsTo(Adventure::class, 'adventure_id');
    }
}
