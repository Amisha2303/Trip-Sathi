<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Gallery image belonging to a pooja vidhi.
 */
class PoojaImage extends ImageModel
{
    protected $table = 'pooja_images';

    protected function casts(): array
    {
        return parent::casts();
    }

    public function ownerModel(): string
    {
        return Pooja::class;
    }

    public function ownerForeignKey(): string
    {
        return 'pooja_id';
    }

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class, 'pooja_id');
    }
}
