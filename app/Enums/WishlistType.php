<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * The polymorphic wishlist target types. Kept separate from BookingType because
 * destinations and hotels are both wishlistable but only hotels are bookable.
 */
enum WishlistType: string
{
    case Destination = 'destination';
    case Hotel = 'hotel';
    case Restaurant = 'restaurant';
    case Event = 'event';
    case Adventure = 'adventure';
    case Darshan = 'darshan';
    case Pooja = 'pooja';

    public function label(): string
    {
        return match ($this) {
            self::Destination => 'Destination',
            self::Hotel => 'Hotel',
            self::Restaurant => 'Restaurant',
            self::Event => 'Event',
            self::Adventure => 'Adventure',
            self::Darshan => 'VIP Darshan',
            self::Pooja => 'Pooja Vidhi',
        };
    }

    /** @return class-string<\Illuminate\Database\Eloquent\Model> */
    public function modelClass(): string
    {
        return match ($this) {
            self::Destination => \App\Models\Destination::class,
            self::Hotel => \App\Models\Hotel::class,
            self::Restaurant => \App\Models\Restaurant::class,
            self::Event => \App\Models\Event::class,
            self::Adventure => \App\Models\Adventure::class,
            self::Darshan => \App\Models\Darshan::class,
            self::Pooja => \App\Models\Pooja::class,
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Destination => 'bi-geo-alt',
            self::Hotel => 'bi-building',
            self::Restaurant => 'bi-cup-hot',
            self::Event => 'bi-calendar-event',
            self::Adventure => 'bi-mountain',
            self::Darshan => 'bi-gem',
            self::Pooja => 'bi-flower1',
        };
    }

    /** @return array<int,string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** @return array<int,string> */
    public static function options(): array
    {
        $out = [];

        foreach (self::cases() as $case) {
            $out[$case->value] = $case->label();
        }

        return $out;
    }
}
