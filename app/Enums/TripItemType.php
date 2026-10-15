<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Items a traveller can drop onto a trip itinerary. `bookable` types resolve to
 * catalogue records through `itemable`; `custom` entries are free-form places
 * typed by the traveller.
 */
enum TripItemType: string
{
    case Hotel = 'hotel';
    case Restaurant = 'restaurant';
    case FoodExperience = 'food_experience';
    case Event = 'event';
    case Adventure = 'adventure';
    case Darshan = 'darshan';
    case Pooja = 'pooja';
    case Place = 'place';
    case Transport = 'transport';
    case Note = 'note';

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
            self::Place => 'Place',
            self::Transport => 'Transport',
            self::Note => 'Note',
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
            self::Place => 'bi-geo-alt',
            self::Transport => 'bi-car-front',
            self::Note => 'bi-journal-text',
        };
    }

    /**
     * Types backed by a catalogue record. Everything else stores its own
     * title/description on the trip item row.
     *
     * @return array<int,self>
     */
    public static function catalogueTypes(): array
    {
        return [
            self::Hotel,
            self::Restaurant,
            self::FoodExperience,
            self::Event,
            self::Adventure,
            self::Darshan,
            self::Pooja,
        ];
    }

    /** Types the traveller authors by hand. */
    public static function isCatalogue(self $type): bool
    {
        return in_array($type, self::catalogueTypes(), true);
    }

    /** Types with a start and end time on the itinerary. */
    public static function isTimed(self $type): bool
    {
        return in_array($type, [self::Hotel, self::Restaurant, self::Event, self::FoodExperience], true);
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
