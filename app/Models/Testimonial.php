<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Curated homepage testimonial. May be promoted from an approved review.
 */
class Testimonial extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = [
        'user_id', 'review_id', 'author_name', 'author_designation', 'author_avatar',
        'location', 'quote', 'rating', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['avatar_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->author_avatar) {
            return self::imageUrl($this->author_avatar, 'images/avatar-placeholder.svg');
        }

        return 'https://ui-avatars.com/api/?name=' . rawurlencode((string) $this->author_name)
            . '&background=0E7C7B&color=fff&size=128';
    }

    public function activityModule(): string
    {
        return 'testimonials';
    }
}
