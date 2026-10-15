<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * A bookable room inside a hotel.
 *
 * `total_rooms` is inventory per night, consumed by BookingService through a
 * `SELECT ... FOR UPDATE` on overlapping booking_items rather than a room-level
 * counter, so the night count always reflects confirmed bookings.
 *
 * @property decimal $price
 */
class Room extends BaseModel
{
    protected $fillable = [
        'hotel_id', 'category_id', 'name', 'slug', 'description', 'room_type',
        'max_adults', 'max_children', 'max_occupancy', 'price', 'discount_percent',
        'extra_bed_price', 'total_rooms', 'bed_type', 'room_size_sqft', 'main_image',
        'breakfast_included', 'is_refundable', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
            'extra_bed_price' => 'decimal:2',
            'max_adults' => 'integer',
            'max_children' => 'integer',
            'max_occupancy' => 'integer',
            'total_rooms' => 'integer',
            'room_size_sqft' => 'integer',
            'breakfast_included' => 'boolean',
            'is_refundable' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected $appends = ['image_url', 'effective_price'];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function destination(): HasOneThrough
    {
        return $this->hasOneThrough(Destination::class, Hotel::class, 'id', 'id', 'hotel_id', 'destination_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class)->ordered();
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
        return self::imageUrl($this->main_image, 'images/room-placeholder.svg');
    }

    public function getEffectivePriceAttribute(): float
    {
        return round((float) $this->price * (1 - ((float) $this->discount_percent / 100)), 2);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Occupancy guard used by the booking form request. */
    public function fitsGuests(int $adults, int $children = 0): bool
    {
        return $adults <= $this->max_adults && $children <= $this->max_children;
    }

    /*
    |--------------------------------------------------------------------------
    | Availability
    |--------------------------------------------------------------------------
    */

    /**
     * Nights fully committed for this room within a date range.
     *
     * Counts distinct booked dates from live (non-cancelled, non-failed)
     * bookings so overlapping ranges cannot double count a single night.
     */
    public function bookedNightsBetween(string $from, string $to, ?int $excludeBookingId = null): int
    {
        $rows = DB::table('booking_items')
            ->join('bookings', 'bookings.id', '=', 'booking_items.booking_id')
            ->where('booking_items.itemable_type', static::class)
            ->where('booking_items.itemable_id', $this->getKey())
            ->whereIn('bookings.status', ['pending', 'confirmed', 'processing', 'completed'])
            ->where('booking_items.service_date', '>=', $from)
            ->where('booking_items.service_date', '<', $to)
            ->when($excludeBookingId, fn ($q) => $q->where('bookings.id', '!=', $excludeBookingId))
            ->select('booking_items.service_date', 'booking_items.quantity')
            ->get();

        // A room may only be sold once per night; take the maximum committed
        // quantity on each date so inventory can never exceed the room count.
        $byDate = [];

        foreach ($rows as $row) {
            $date = (string) $row->service_date;
            $byDate[$date] = max($byDate[$date] ?? 0, (int) $row->quantity);
        }

        return count($byDate);
    }

    /**
     * Rooms still sellable across every night of the stay.
     *
     * `total_rooms` is inventory per night, so a stay is only sellable when the
     * busiest night in the range still has a free room.
     */
    public function availabilityFor(string $checkIn, string $checkOut, ?int $excludeBookingId = null): int
    {
        $nights = $this->nightsBetween($checkIn, $checkOut);

        if ($nights < 1) {
            return 0;
        }

        return max(0, (int) $this->total_rooms - $this->bookedNightsBetween($checkIn, $checkOut, $excludeBookingId));
    }

    /** Nights in a stay (check-out date is exclusive). */
    public function nightsBetween(string $checkIn, string $checkOut): int
    {
        return max(0, (int) Carbon::parse($checkIn)->startOfDay()
            ->diffInDays(Carbon::parse($checkOut)->startOfDay()));
    }

    public function scopeAvailableOn(Builder $query, string $date): Builder
    {
        return $query->where('is_active', true)
            ->whereDoesntHave('bookings', function (Builder $inner) use ($date): void {
                $inner->whereIn('status', ['pending', 'confirmed', 'processing', 'completed'])
                    ->whereDate('booking_date', $date);
            });
    }

    public function activityModule(): string
    {
        return 'rooms';
    }
}
