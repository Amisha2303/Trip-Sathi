<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Brand
    |--------------------------------------------------------------------------
    */

    'name' => env('TRIPSATHI_NAME', 'Trip Sathi'),

    'tagline' => env('TRIPSATHI_TAGLINE', 'Plan Your Journey. Create Beautiful Memories.'),

    /*
    |--------------------------------------------------------------------------
    | Commerce
    |--------------------------------------------------------------------------
    */

    'currency' => env('TRIPSATHI_CURRENCY', 'INR'),

    'currency_symbol' => env('TRIPSATHI_CURRENCY_SYMBOL', '₹'),

    /* Percentage of GST applied to booking subtotals after discount. */
    'tax_percent' => (float) env('TRIPSATHI_TAX_PERCENT', 5),

    /* Booking numbers look like TS-2026-000001. */
    'booking_prefix' => env('TRIPSATHI_BOOKING_PREFIX', 'TS'),

    /* Number of nights used when a booking omits an explicit end date. */
    'default_nights' => 1,

    /* Hours before a booking starts after which self-service cancellation closes. */
    'cancellation_cutoff_hours' => (int) env('TRIPSATHI_CANCELLATION_CUTOFF_HOURS', 24),

    /* Refund policy used on cancellations, expressed as a percentage of the paid amount. */
    'refund_percent' => (float) env('TRIPSATHI_REFUND_PERCENT', 90),

    /*
    |--------------------------------------------------------------------------
    | Uploads — allowed image mime types & size ceiling (kilobytes)
    |--------------------------------------------------------------------------
    */

    'uploads' => [
        'disk' => env('TRIPSATHI_UPLOAD_DISK', 'public'),

        'max_kb' => (int) env('TRIPSATHI_UPLOAD_MAX_KB', 4096),

        'mimes' => ['jpg', 'jpeg', 'png', 'webp'],

        'mime_types' => ['image/jpeg', 'image/pjpeg', 'image/png', 'image/webp'],

        'extensions' => ['jpg', 'jpeg', 'png', 'webp'],

        'min_width' => 320,
        'min_height' => 200,
        'max_width' => 8000,
        'max_height' => 8000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin access hardening
    |--------------------------------------------------------------------------
    */

    'admin' => [
        /* Force a password change for accounts created by the seeder. */
        'force_password_change' => filter_var(
            env('ADMIN_FORCE_PASSWORD_CHANGE', false),
            FILTER_VALIDATE_BOOL
        ),

        /* Two-factor authentication for the admin panel. */
        'two_factor' => filter_var(env('ADMIN_2FA_ENABLED', false), FILTER_VALIDATE_BOOL),

        /* Comma separated CIDRs / IPs permitted to reach /admin. Blank = any. */
        'allowed_ips' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('ADMIN_ALLOWED_IPS', ''))
        ))),

        /* Minutes an admin session may remain idle before logout. */
        'session_idle_timeout' => (int) env('ADMIN_IDLE_TIMEOUT', 120),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate limiting (requests per minute)
    |--------------------------------------------------------------------------
    */

    'throttle' => [
        'contact' => (int) env('THROTTLE_CONTACT', 3),
        'newsletter' => (int) env('THROTTLE_NEWSLETTER', 3),
        'login' => (int) env('THROTTLE_LOGIN', 5),
        'admin_login' => (int) env('THROTTLE_ADMIN_LOGIN', 4),
        'api' => (int) env('THROTTLE_API', 60),
        'booking' => (int) env('THROTTLE_BOOKING', 15),
        'wishlist' => (int) env('THROTTLE_WISHLIST', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache TTLs (seconds)
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'settings' => 3600,
        'destinations' => 1800,
        'featured' => 900,
        'categories' => 3600,
    ],

    /*
    |--------------------------------------------------------------------------
    | Seeded accounts — credentials come from the environment, never hardcoded
    |--------------------------------------------------------------------------
    */

    'seed_admin_email' => env('ADMIN_EMAIL', 'admin@tripsathi.test'),
    'seed_admin_password' => env('ADMIN_PASSWORD', 'ChangeMe@12345'),
    'seed_user_email' => env('DEMO_USER_EMAIL', 'traveller@tripsathi.test'),
    'seed_user_password' => env('DEMO_USER_PASSWORD', 'Traveler@123'),
    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */

    /*
    | Check passwords against Have I Been Pwned on registration and reset.
    | Requires an outbound HTTPS call, so it is off by default: local
    | development and CI should not depend on a third-party endpoint.
    */
    'pwned_password_check' => (bool) env('TRIPSATHI_PWNED_PASSWORD_CHECK', false),

    /*
    | Allow the back office at /admin. Turn off in a maintenance deployment
    | where the admin surface must be unreachable rather than merely guarded.
    */
    'admin_enabled' => (bool) env('TRIPSATHI_ADMIN_ENABLED', true),

    /*
    | Login attempt ceiling before the email+IP key is locked out.
    */
    'login_max_attempts' => (int) env('TRIPSATHI_LOGIN_MAX_ATTEMPTS', 5),

    'login_lockout_seconds' => (int) env('TRIPSATHI_LOGIN_LOCKOUT_SECONDS', 600),

    /*
    | Seconds a user must wait before re-requesting an email verification link.
    */
    'email_resend_cooldown_seconds' => (int) env('TRIPSATHI_EMAIL_RESEND_COOLDOWN', 60),

];