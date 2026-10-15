<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Gallery image belonging to a food experience.
 */
class FoodExperienceImage extends ImageModel
{
    protected $table = 'food_experience_images';

    protected function casts(): array
    {
        return parent::casts();
    }

    public function ownerModel(): string
    {
        return FoodExperience::class;
    }

    public function ownerForeignKey(): string
    {
        return 'food_experience_id';
    }

    public function foodExperience(): BelongsTo
    {
        return $this->belongsTo(FoodExperience::class, 'food_experience_id');
    }
}
