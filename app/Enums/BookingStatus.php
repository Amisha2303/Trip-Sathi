<?php

declare(strict_types=1);

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Confirmed => 'Confirmed',
            self::Processing => 'Processing',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
            self::Refunded => 'Refunded',
            self::Failed => 'Failed',
        };
    }

    /** Bootstrap contextual colour token used by status badges. */
    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Confirmed => 'success',
            self::Processing => 'info',
            self::Completed => 'primary',
            self::Cancelled => 'secondary',
            self::Refunded => 'dark',
            self::Failed => 'danger',
        };
    }

    /** The customer may cancel the booking while it is in one of these states. */
    public function isCancellable(): bool
    {
        return in_array($this, [self::Pending, self::Confirmed, self::Processing], true);
    }

    /** These states hold inventory and can transition to Completed. */
    public function isActive(): bool
    {
        return in_array($this, [self::Pending, self::Confirmed, self::Processing], true);
    }

    /** Verified buyers may only review once a booking reaches this state. */
    public function isReviewable(): bool
    {
        return $this === self::Completed;
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
            $out[$case->value] = $case->badge();
        }

        return $out;
    }
}
