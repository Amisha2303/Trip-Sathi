<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReviewStatus;
use App\Enums\ReviewSubjectType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A review written against a *completed* booking.
 *
 * The `booking_id` foreign key plus the unique (booking_id, subject) index is
 * the duplicate guard: one booking can only ever produce one review per subject.
 */
class Review extends BaseModel
{
    public static bool $usesSlug = false;

    protected $fillable = [
        'user_id', 'booking_id', 'subject_type', 'subject_id', 'destination_id',
        'rating', 'title', 'comment', 'status', 'is_verified',
        'helpful_count', 'report_count', 'is_featured', 'moderated_at', 'moderated_by',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'status' => ReviewStatus::class,
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'helpful_count' => 'integer',
            'report_count' => 'integer',
            'moderated_at' => 'datetime',
        ];
    }

    protected $appends = ['image_url', 'author_initials'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo('subject');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ReviewImage::class)->orderBy('sort_order');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ReviewReport::class);
    }

    public function moderatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Presentation
    |--------------------------------------------------------------------------
    */

    public function getAuthorNameAttribute(): string
    {
        return $this->user?->name ?? 'Traveller';
    }

    public function getAuthorInitialsAttribute(): string
    {
        return $this->user?->initials ?? 'TS';
    }

    public function getAuthorAvatarAttribute(): ?string
    {
        return $this->user?->avatar_url;
    }

    public function getImageUrlAttribute(): string
    {
        return $this->user?->avatar_url ?? 'https://ui-avatars.com/api/?name=Traveller&background=0E7C7B&color=fff&size=96';
    }

    public function subjectType(): ?ReviewSubjectType
    {
        return ReviewSubjectType::tryFrom((string) $this->subject_type);
    }

    public function getSubjectLabelAttribute(): string
    {
        return $this->subjectType()?->label() ?? 'Experience';
    }

    /** Link back to the reviewed record. */
    public function getSubjectUrlAttribute(): ?string
    {
        $subject = $this->subject;

        if ($subject === null) {
            return null;
        }

        $route = match ($this->subject_type) {
            ReviewSubjectType::Hotel->value => 'hotels.show',
            ReviewSubjectType::Restaurant->value => 'restaurants.show',
            ReviewSubjectType::FoodExperience->value => 'food-experiences.show',
            ReviewSubjectType::Event->value => 'events.show',
            ReviewSubjectType::Adventure->value => 'adventures.show',
            ReviewSubjectType::Darshan->value => 'darshan.show',
            ReviewSubjectType::Pooja->value => 'poojas.show',
            ReviewSubjectType::Destination->value => 'destinations.show',
            default => null,
        };

        return $route ? route($route, $subject) : null;
    }

    /** Recompute the aggregated rating on the reviewed record. */
    public function refreshSubjectRating(): void
    {
        $subject = $this->subject;

        if ($subject === null) {
            return;
        }

        $aggregate = static::query()
            ->where('subject_type', $this->subject_type)
            ->where('subject_id', $subject->getKey())
            ->where('status', ReviewStatus::Approved->value)
            ->selectRaw('COUNT(*) as total, AVG(rating) as average')
            ->first();

        if (isset($subject->rating)) {
            $subject->forceFill([
                'rating' => round((float) $aggregate->average, 2),
                'review_count' => (int) $aggregate->total,
            ])->saveQuietly();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ReviewStatus::Approved->value);
    }

    public function scopeForSubject(Builder $query, string $type, int $id): Builder
    {
        return $query->where('subject_type', $type)->where('subject_id', $id);
    }

    public function scopeWithRating(Builder $query, float|int|null $min): Builder
    {
        return $query->when($min !== null, fn (Builder $q) => $q->where('rating', '>=', (int) $min));
    }

    public function activityModule(): string
    {
        return 'reviews';
    }
}
