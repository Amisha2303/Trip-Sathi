<?php

declare(strict_types=1);

namespace App\Enums;

/** Admin-facing audit trail of who did what. */
enum ActivityAction: string
{
    case Login = 'login';
    case LoginFailed = 'login_failed';
    case Logout = 'logout';
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case Restored = 'restored';
    case Viewed = 'viewed';
    case StatusChange = 'status_change';
    case PaymentUpdate = 'payment_update';
    case Refund = 'refund';
    case Export = 'export';
    case SettingsUpdate = 'settings_update';
    case TwoFactorEnabled = 'two_factor_enabled';
    case TwoFactorDisabled = 'two_factor_disabled';

    public function label(): string
    {
        return ucwords(str_replace('_', ' ', $this->value));
    }

    public function badge(): string
    {
        return match ($this) {
            self::Created => 'success',
            self::Updated, self::StatusChange => 'info',
            self::Deleted, self::LoginFailed => 'danger',
            self::Login, self::Logout => 'secondary',
            self::PaymentUpdate, self::Refund => 'success',
            self::TwoFactorEnabled => 'warning',
            self::TwoFactorDisabled => 'danger',
            default => 'secondary',
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
