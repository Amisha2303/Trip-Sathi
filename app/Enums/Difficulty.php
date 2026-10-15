<?php

declare(strict_types=1);

namespace App\Enums;

enum Difficulty: string
{
    case Easy = 'easy';
    case Moderate = 'moderate';
    case Hard = 'hard';
    case Extreme = 'extreme';

    public function label(): string
    {
        return match ($this) {
            self::Easy => 'Easy',
            self::Moderate => 'Moderate',
            self::Hard => 'Hard',
            self::Extreme => 'Extreme',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Easy => 'success',
            self::Moderate => 'info',
            self::Hard => 'warning',
            self::Extreme => 'danger',
        };
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
