<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Booking;

/**
 * Currency formatting.
 *
 * Money lives in the database as integer minor units (paise); every value that
 * reaches a template passes through here so rounding and symbol placement are
 * decided in exactly one place.
 */
final class Money
{
    /** Format minor units (integer paise) for display. */
    public static function format(?int $minor, bool $withSymbol = true): string
    {
        if ($minor === null) {
            return $withSymbol ? self::symbol() . '0' : '0';
        }

        $amount = Booking::toRupees($minor);
        $formatted = number_format($amount, ($amount == (int) $amount) ? 0 : 2);

        return $withSymbol ? self::symbol() . $formatted : $formatted;
    }

    /** Format a decimal rupee value (admin-entered prices, CSV exports). */
    public static function decimal(float|int|string|null $rupees, bool $withSymbol = true): string
    {
        if ($rupees === null) {
            return $withSymbol ? self::symbol() . '0' : '0';
        }

        $value = (float) $rupees;
        $formatted = number_format($value, fmod($value, 1.0) === 0.0 ? 0 : 2);

        return $withSymbol ? self::symbol() . $formatted : $formatted;
    }

    public static function symbol(): string
    {
        return config('tripsathi.currency_symbol', '₹');
    }

    public static function code(): string
    {
        return config('tripsathi.currency', 'INR');
    }

    /**
     * Percentage of a minor-unit amount, rounded half-up.
     * Integer math keeps tax and discount totals exactly reconcilable.
     */
    public static function percentOf(int $minor, float $percent): int
    {
        return (int) round($minor * ($percent / 100));
    }
}
