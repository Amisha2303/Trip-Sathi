<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingType;
use App\Enums\Difficulty;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * A bookable adventure activity with date/time slots.
 *
 * @property string $slug
 */
class Adventure extends BaseModel
{
    protected $fillable = [
        'destination_id', 'category_id', 'name', 'slug', 'description', 'short_description',
        'activity_type', 'safety_instructions', 'inclusions', 'equipment', 'cancellation_policy',
        'price', 'discount_percent', 'duration_minutes', 'difficulty',
        'min_age', 'max_age', 'max_participants', 'min_participants',
        'height_min_cm', 'height_max_cm', 'weight_max_kg',
        'meeting_point', 'main_image', 'is_featured', 'is_active',
        'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'duration_minutes' => 'integer',
            'difficulty' => Difficulty::class,
            'min_age' => 'integer',
            'max_age' => 'integer',
            'max_participants' => 'integer',
            'min_participants' => 'integer',
            'height_min_cm' => 'decimal:2',
            'height_max_cm' => 'decimal:2',
            'weight_max_kg' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected $appends = ['image_url', 'effective_price', 'difficulty_label'];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(AdventureImage::class)->ordered();
    }

    public function slots(): HasMany
    {
        return $this->hasMany(AdventureSlot::class)->orderBy('slot_date')->orderBy('start_time');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'amenity_amenity', 'ameniable_id')
            ->where('amenity_amenity.ameniable_type', static::class)
            ->withTimestamps();
    }

    public function bookings(): MorphMany
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'subject');
    }

    public function wishlistEntries(): MorphMany
    {
        return $this->morphMany(Wishlist::class, 'wishlistable');
    }

    public function tripItems(): MorphMany
    {
        return $this->morphMany(TripItem::class, 'itemable');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute(): string
    {
        return self::imageUrl($this->main_image, 'images/adventure-placeholder.svg');
    }

    public function getEffectivePriceAttribute(): float
    {
        return round((float) $this->price * (1 - ((float) $this->discount_percent / 100)), 2);
    }

    public function getDifficultyLabelAttribute(): string
    {
        return $this->difficulty?->label() ?? 'Moderate';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** "3 hours" / "2 h 30 m" */
    public function getDurationLabelAttribute(): string
    {
        $minutes = (int) $this->duration_minutes;
        $hours = intdiv($minutes, 60);
        $rest = $minutes % 60;

        return match (true) {
            $hours === 0 => $rest . ' min',
            $rest === 0 => $hours . ($hours === 1 ? ' hour' : ' hours'),
            default => $hours . 'h ' . $rest . 'm',
        };
    }

    /** Next bookable slot, used on cards ("Next slot: Sat 9:00 AM"). */
    public function nextSlot(): ?AdventureSlot
    {
        return $this->slots()
            ->where('is_active', true)
            ->where('slot_date', '>=', today()->toDateString())
            ->where(fn (Builder $q) => $q->whereColumn('booked', '<', 'capacity'))
            ->orderBy('slot_date')
            ->orderBy('start_time')
            ->first();
    }

    /** Eligibility check surfaced as inline warnings on the booking form. */
    public function checkEligibility(?int $age = null, ?float $heightCm = null, ?float $weightKg = null): array
    {
        $issues = [];

        if ($age !== null && $age < (int) $this->min_age) {
            $issues[] = "Minimum age for this activity is {$this->min_age} years.";
        }

        if ($age !== null && $this->max_age !== null && $age > (int) $this->max_age) {
            $issues[] = "Maximum age for this activity is {$this->max_age} years.";
        }

        if ($heightCm !== null && $this->height_min_cm !== null && $heightCm < (float) $this->height_min_cm) {
            $issues[] = 'Minimum height requirement is ' . rtrim(rtrim((string) $this->height_min_cm, '0'), '.') . ' cm.';
        }

        if ($heightCm !== null && $this->height_max_cm !== null && $heightCm > (float) $this->height_max_cm) {
            $issues[] = 'Maximum height requirement is ' . rtrim(rtrim((string) $this->height_max_cm, '0'), '.') . ' cm.';
        }

        if ($weightKg !== null && $this->weight_max_kg !== null && $weightKg > (float) $this->weight_max_kg) {
            $issues[] = 'Maximum weight requirement is ' . rtrim(rtrim((string) $this->weight_max_kg, '0'), '.') . ' kg.';
        }

        return $issues;
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSearch(Builder $query, ?string $term, array $columns = ['name', 'activity_type', 'short_description']): Builder
    {
        return parent::scopeSearch($query, $term, $columns);
    }

    public function scopePriceBetween(Builder $query, float|int|null $min, float|int|null $max): Builder
    {
        return $query
            ->when($min !== null, fn (Builder $q) => $q->where('price', '>=', (float) $min))
            ->when($max !== null, fn (Builder $q) => $q->where('price', '<=', (float) $max));
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return $query->when($type, fn (Builder $q) => $q->where('activity_type', $type));
    }

    /** Activity types offered by the platform, for filter chips. */
    public static function activityTypes(): array
    {
        return [
            'Trekking' => 'bi-signpost-split',
            'River Rafting' => 'bi-water',
            'Paragliding' => 'bi-airplane',
            'Scuba Diving' => 'bi-snorkel',
            'Camping' => 'bi-tree',
            'Wildlife Safari' => 'bi-binoculars',
            'Zipline' => 'bi-bezier2',
            'Water Sports' => 'bi-sailboat',
            'Cycling' => 'bi-bicycle',
            'Rock Climbing' => 'bi-bar-chart-steps',
        ];
    }

    public function bookingType(): BookingType
    {
        return BookingType::Adventure;
    }

    public function activityModule(): string
    {
        return 'adventures';
    }
}
