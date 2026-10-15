<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Every bookable line item carries the enum that produced it. The value is the
 * morph map key, which keeps booking queries index-friendly and lets the
 * polymorphic booking engine resolve a single "bookable" relation.
 */
enum BookingType: string
{
    case Hotel = 'hotel';
    case Restaurant = 'restaurant';
    case FoodExperience = 'food_experience';
    case Event = 'event';
    case Adventure = 'adventure';
    case Darshan = 'darshan';
    case Pooja = 'pooja';

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
        };
    }

    /** Plural label used in admin menu headings and report columns. */
    public function pluralLabel(): string
    {
        return $this->label() . ' Bookings';
    }

    /** Human readable model class for the polymorph target. */
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
        };
    }

    /** Route name prefix for the public "explore" pages. */
    public function routePrefix(): string
    {
        return match ($this) {
            self::Hotel => 'hotels',
            self::Restaurant => 'restaurants',
            self::FoodExperience => 'food-experiences',
            self::Event => 'events',
            self::Adventure => 'adventures',
            self::Darshan => 'darshan',
            self::Pooja => 'poojas',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Hotel => 'bi-building',
            self::Restaurant => 'bi-cup-hot',
            self::FoodExperience => 'bi-basket2',
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

    /** @return array<string,string> */
    public static function badgeMap(): array
    {
        $out = [];

        foreach (self::cases() as $case) {
            $out[$case->value] = $case->label();
        }

        return $out;
    }
}
