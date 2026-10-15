<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Canonical permission catalogue.
 *
 * Slugs follow `module.action`. `Permission::all()` is the single source of
 * truth: role seeding, the admin permissions screen and the `can:` Blade
 * directive all derive from this enum, so adding a permission here propagates
 * everywhere after re-seeding.
 */
enum Permission: string
{
    // Dashboard
    case DashboardView = 'dashboard.view';

    // Users & access control
    case UsersView = 'users.view';
    case UsersCreate = 'users.create';
    case UsersEdit = 'users.edit';
    case UsersDelete = 'users.delete';
    case RolesView = 'roles.view';
    case RolesCreate = 'roles.create';
    case RolesEdit = 'roles.edit';
    case RolesDelete = 'roles.delete';

    // Catalogue
    case DestinationsView = 'destinations.view';
    case DestinationsCreate = 'destinations.create';
    case DestinationsEdit = 'destinations.edit';
    case DestinationsDelete = 'destinations.delete';
    case HotelsView = 'hotels.view';
    case HotelsCreate = 'hotels.create';
    case HotelsEdit = 'hotels.edit';
    case HotelsDelete = 'hotels.delete';
    case RoomsView = 'rooms.view';
    case RoomsCreate = 'rooms.create';
    case RoomsEdit = 'rooms.edit';
    case RoomsDelete = 'rooms.delete';
    case RestaurantsView = 'restaurants.view';
    case RestaurantsCreate = 'restaurants.create';
    case RestaurantsEdit = 'restaurants.edit';
    case RestaurantsDelete = 'restaurants.delete';
    case FoodView = 'food.view';
    case FoodCreate = 'food.create';
    case FoodEdit = 'food.edit';
    case FoodDelete = 'food.delete';
    case EventsView = 'events.view';
    case EventsCreate = 'events.create';
    case EventsEdit = 'events.edit';
    case EventsDelete = 'events.delete';
    case AdventuresView = 'adventures.view';
    case AdventuresCreate = 'adventures.create';
    case AdventuresEdit = 'adventures.edit';
    case AdventuresDelete = 'adventures.delete';
    case DarshanView = 'darshan.view';
    case DarshanCreate = 'darshan.create';
    case DarshanEdit = 'darshan.edit';
    case DarshanDelete = 'darshan.delete';
    case PoojasView = 'poojas.view';
    case PoojasCreate = 'poojas.create';
    case PoojasEdit = 'poojas.edit';
    case PoojasDelete = 'poojas.delete';

    // Supporting catalogue data
    case CategoriesView = 'categories.view';
    case CategoriesManage = 'categories.manage';
    case AmenitiesView = 'amenities.view';
    case AmenitiesManage = 'amenities.manage';
    case MediaView = 'media.view';
    case MediaManage = 'media.manage';

    // Bookings
    case BookingsView = 'bookings.view';
    case BookingsCreate = 'bookings.create';
    case BookingsUpdate = 'bookings.update';
    case BookingsCancel = 'bookings.cancel';
    case BookingsDelete = 'bookings.delete';

    // Finance
    case PaymentsView = 'payments.view';
    case PaymentsUpdate = 'payments.update';
    case RefundsView = 'refunds.view';
    case RefundsCreate = 'refunds.create';
    case CouponsView = 'coupons.view';
    case CouponsManage = 'coupons.manage';

    // Content & engagement
    case ReviewsView = 'reviews.view';
    case ReviewsModerate = 'reviews.moderate';
    case ReviewsDelete = 'reviews.delete';
    case TestimonialsView = 'testimonials.view';
    case TestimonialsManage = 'testimonials.manage';

    // Inbox / system
    case MessagesView = 'messages.view';
    case MessagesUpdate = 'messages.update';
    case MessagesDelete = 'messages.delete';
    case NewsletterView = 'newsletter.view';
    case NewsletterManage = 'newsletter.manage';
    case NotificationsView = 'notifications.view';
    case EmailLogsView = 'emails.view';
    case LogsView = 'logs.view';

    // System
    case ReportsView = 'reports.view';
    case ReportsExport = 'reports.export';
    case SettingsView = 'settings.view';
    case SettingsManage = 'settings.manage';
    case AdminUsersView = 'admins.view';
    case AdminUsersManage = 'admins.manage';

    public function slug(): string
    {
        return $this->value;
    }

    public function module(): string
    {
        return explode('.', $this->value)[0];
    }

    public function action(): string
    {
        return explode('.', $this->value)[1] ?? 'view';
    }

    /** @return array<int,string> */
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Every `.view` slug — the read-only bundle granted to Support Staff.
     *
     * @return array<int,string>
     */
    public static function readOnly(): array
    {
        return array_values(array_filter(
            self::all(),
            static fn (string $slug): bool => str_ends_with($slug, '.view')
        ));
    }

    /**
     * All slugs belonging to one module.
     *
     * @return array<int,string>
     */
    public static function group(string $module): array
    {
        return array_values(array_filter(
            array_column(self::cases(), 'value'),
            static fn (string $slug): bool => explode('.', $slug)[0] === $module
        ));
    }

    /**
     * Permission slugs that match a leading fragment, e.g. `bookings`.
     * Returns [] when the fragment matches nothing so an empty group never
     * accidentally grants broad access.
     *
     * @return array<int,string>
     */
    public static function groupPrefixed(string $prefix): array
    {
        return array_values(array_filter(
            self::all(),
            static fn (string $slug): bool => str_starts_with($slug, $prefix)
        ));
    }

    /** @return array<string,string> Slug => "Module — Action" label for the permissions screen. */
    public static function labelMap(): array
    {
        $out = [];

        foreach (self::cases() as $case) {
            $out[$case->value] = self::humanise($case->module()) . ' — ' . self::humanise($case->action());
        }

        return $out;
    }

    /**
     * Group slugs into modules for rendering the permissions matrix.
     *
     * @return array<string,array<int,self>>
     */
    public static function grouped(): array
    {
        $groups = [];

        foreach (self::cases() as $case) {
            $groups[$case->module()][] = $case;
        }

        ksort($groups);

        return $groups;
    }

    private static function humanise(string $value): string
    {
        return ucwords(str_replace(['.', '_', '-'], ' ', $value));
    }
}
