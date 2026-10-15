<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An abuse report raised against a review. Unique per user and review.
 */
class ReviewReport extends Model
{
    public $timestamps = true;

    protected $fillable = ['review_id', 'user_id', 'reason', 'details', 'status'];

    protected function casts(): array
    {
        return ['status' => 'string'];
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /** Reasons offered on the public "report review" dialog. */
    public static function reasons(): array
    {
        return [
            'spam' => 'Spam or misleading',
            'offensive' => 'Offensive or abusive language',
            'fake' => 'Fake review',
            'irrelevant' => 'Irrelevant to the experience',
            'private' => 'Contains private information',
            'other' => 'Something else',
        ];
    }
}
