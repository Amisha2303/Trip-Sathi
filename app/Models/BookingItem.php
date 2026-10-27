<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BookingType;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A single billable line on a booking.
 *
 * `itemable` points at the exact record sold (a Room, an EventTicketType, a
 * FoodExperience …) rather than the parent product, so availability checks and
 * refunds always target the real inventory row.
 */
class BookingItem extends Model
{
    use LogsActivity;

    public static bool $usesSlug = false;

    public $timestamps = true;

    protected $fillable = [
        'booking_id', 'item_type', 'itemable_type', 'itemable_id',
        'slot_id', 'slot_table', 'name', 'description', 'image',
        'service_date', 'start_time', 'end_time', 'quantity', 'guests',
        'unit_price', 'discount', 'tax', 'total_amount',
        'options', 'visitor_details',
    ];

    /**
     * Gross line value before tax, in minor units.
     *
     * This is computed rather than stored: keeping it out of `$attributes`
     * means Eloquent never emits a phantom `line_total` column to SQL, and it
     * stays correct whenever unit_price or quantity is reassigned (tax
     * allocation rewrites unit_price after the row is built).
     */
    public function getLineTotalAttribute(): int
    {
        return ((int) $this->unit_price) * ((int) $this->quantity);
    }

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'quantity' => 'integer',
            'guests' => 'integer',
            'unit_price' => 'integer',
            'discount' => 'integer',
            'tax' => 'integer',
            'total_amount' => 'integer',
            'options' => 'array',
            'visitor_details' => 'array',
        ];
    }

    protected $appends = ['total_amount_rupees', 'unit_price_rupees'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo('itemable');
    }

    /*
    |--------------------------------------------------------------------------
    | Money
    |--------------------------------------------------------------------------
    */

    public function getTotalAmountRupeesAttribute(): float
    {
        return Booking::toRupees($this->total_amount);
    }

    public function getUnitPriceRupeesAttribute(): float
    {
        return Booking::toRupees($this->unit_price);
    }

    public function getImageUrlAttribute(): string
    {
        return self::imageUrl($this->attributes['image'] ?? null, 'images/placeholder.svg');
    }

    /*
    |--------------------------------------------------------------------------
    | Presentation
    |--------------------------------------------------------------------------
    */

    public function type(): ?BookingType
    {
        return BookingType::tryFrom((string) $this->item_type);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type()?->label() ?? ucfirst((string) $this->item_type);
    }

    public function getTimeRangeAttribute(): ?string
    {
        if (! $this->start_time) {
            return null;
        }

        $format = fn (?string $t): ?string => $t
            ? \Illuminate\Support\Carbon::parse($t)->format('g:i A')
            : null;

        return $format($this->start_time) . ' – ' . ($format($this->end_time) ?? '—');
    }

    /** "Room · Deluxe King" style subtitle for the booking detail page. */
    public function getSubtitleAttribute(): string
    {
        $options = collect($this->options ?? [])
            ->filter(fn ($v) => is_scalar($v))
            ->map(fn ($v, $k) => ucwords(str_replace('_', ' ', (string) $k)) . ': ' . $v)
            ->implode(' · ');

        return $options !== '' ? $options : $this->type_label;
    }

    /** Visitor names captured for VIP darshan ID checks. */
    public function visitors(): array
    {
        $details = $this->visitor_details ?? [];

        return is_array($details) ? array_values(array_filter($details)) : [];
    }

    public function activityModule(): string
    {
        return 'booking_items';
    }
}
