<?php

declare(strict_types=1);

namespace App\Enums;

/** Payment row status mirrored from the gateway, kept separate from booking status. */
enum TransactionStatus: string
{
    case Initiated = 'initiated';
    case Pending = 'pending';
    case Authorized = 'authorized';
    case Captured = 'captured';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Initiated => 'Initiated',
            self::Pending => 'Pending',
            self::Authorized => 'Authorized',
            self::Captured => 'Captured',
            self::Failed => 'Failed',
            self::Refunded => 'Refunded',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Initiated, self::Pending => 'warning',
            self::Authorized => 'info',
            self::Captured => 'success',
            self::Failed => 'danger',
            self::Refunded => 'dark',
        };
    }

    /** @return array<int,string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** @return array<string,string> */
    public static function badgeMap(): array
    {
        $out = [];

        foreach (self::cases() as $case) {
            $out[$case->value] = $case->badge();
        }

        return $out;
    }
}
