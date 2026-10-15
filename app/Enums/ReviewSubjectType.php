<?php

declare(strict_types=1);

namespace App\Enums;

/** Who can leave a review, derived from the verified booking relationship. */
enum ReviewSubjectType: string
{
    case Hotel = 'hotel';
    case Restaurant = 'restaurant';
    case FoodExperience = 'food_experience';
    case Event = 'event';
    case Adventure = 'adventure';
    case Darshan = 'darshan';
    case Pooja = 'pooja';
    case Destination = 'destination';

    public function label(): string
    {
        return match ($this) {
            self::Hotel => 'Hotel',
            self::Restaurant => 'Restaurant',
            self::FoodExperience => 'Food Experience',
            self::Event => 'Event',
            self::Adventure => 'Adventure',
            self::Darshan => 'VIP Darshan',
            self::Pooja => 'Pooja Vidhi',
            self::Destination => 'Destination',
        };
    }

    /** @return class-string<\Illuminate\Database\Eloquent\Model> */
    public function modelClass(): string
    {
        return match ($this) {
            self::Hotel => \App\Models\Hotel::class,
            self::Restaurant => \App\Models\Restaurant::class,
            self::FoodExperience => \App\Models\FoodExperience::class,
            self::Event => \App\Models\Event::class,
            self::Adventure => \App\Models\Adventure::class,
            self::Darshan => \App\Models\Darshan::class,
            self::Pooja => \App\Models\Pooja::class,
            self::Destination => \App\Models\Destination::class,
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
