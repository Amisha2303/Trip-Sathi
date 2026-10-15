<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentGateway: string
{
    case Razorpay = 'razorpay';
    case Stripe = 'stripe';
    case Offline = 'offline';

    public function label(): string
    {
        return match ($this) {
            self::Razorpay => 'Razorpay',
            self::Stripe => 'Stripe',
            self::Offline => 'Offline / Manual',
        };
    }

    /** Whether money actually leaves the platform through this gateway. */
    public function isOnline(): bool
    {
        return $this !== self::Offline;
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
