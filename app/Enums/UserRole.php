<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Seeded platform roles. Permission assignment lives in the role/permission
 * pivot tables (spatie/laravel-permission); this enum exists so role names are
 * never written as magic strings in code.
 */
enum UserRole: string
{
    case SuperAdmin = 'super-admin';
    case Admin = 'admin';
    case Manager = 'manager';
    case BookingManager = 'booking-manager';
    case ContentManager = 'content-manager';
    case SupportStaff = 'support-staff';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Manager => 'Manager',
            self::BookingManager => 'Booking Manager',
            self::ContentManager => 'Content Manager',
            self::SupportStaff => 'Support Staff',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Unrestricted access to every module and setting.',
            self::Admin => 'Full access to day-to-day operations except role management.',
            self::Manager => 'Manages catalogue, bookings and reports; cannot manage users.',
            self::BookingManager => 'Handles booking lifecycle, cancellations and refunds only.',
            self::ContentManager => 'Maintains destinations, hotels, events and media.',
            self::SupportStaff => 'Read-only catalogue access plus contact message handling.',
        };
    }

    /**
     * Default permission slugs granted to the role. Super Admin bypasses
     * individual checks entirely via Gate::before().
     *
     * @return array<int,string>
     */
    public function defaultPermissions(): array
    {
        return match ($this) {
            self::SuperAdmin => ['*'],
            self::Admin => Permission::all(),
            self::Manager => array_values(array_filter(
                Permission::all(),
                static fn (string $slug): bool => ! str_starts_with($slug, 'roles.')
                    && ! str_starts_with($slug, 'users.delete')
                    && ! str_starts_with($slug, 'settings.')
                    && ! str_starts_with($slug, 'refunds.create'),
            )),
            self::BookingManager => [
                ...Permission::group('bookings'),
                ...Permission::group('payments'),
                'dashboard.view',
                'reports.view',
                'refunds.create',
                'coupons.view',
            ],
            self::ContentManager => [
                'dashboard.view',
                ...Permission::group('catalog'),
                'reviews.moderate',
                ...Permission::group('media'),
            ],
            self::SupportStaff => [
                'dashboard.view',
                ...Permission::readOnly(),
                'messages.view',
                'messages.update',
                'messages.delete',
                'newsletter.view',
                'bookings.view',
                'bookings.update',
            ],
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
