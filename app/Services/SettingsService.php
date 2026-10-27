<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Read access to runtime settings with sensible defaults.
 *
 * Every getter falls back to a hard default so the site renders even before the
 * seeder runs — a missing settings row must never blank the header or the tax
 * line on an invoice.
 */
class SettingsService
{
    /** @var array<string, mixed>|null */
    private ?array $cache = null;

    /**
     * The default matrix. Seeded into the `settings` table on first boot, and
     * used verbatim whenever a row is absent.
     *
     * @return array<string, array{value: mixed, type: string, group: string, public: bool, label: string, hint?: string}>
     */
    public static function defaults(): array
    {
        return [
            // Brand
            'site_name' => ['value' => config('tripsathi.name', 'Trip Sathi'), 'type' => 'string', 'group' => 'general', 'public' => true, 'label' => 'Website Name'],
            'site_tagline' => ['value' => config('tripsathi.tagline'), 'type' => 'string', 'group' => 'general', 'public' => true, 'label' => 'Tagline'],
            'site_logo' => ['value' => null, 'type' => 'image', 'group' => 'general', 'public' => true, 'label' => 'Logo'],
            'site_favicon' => ['value' => null, 'type' => 'image', 'group' => 'general', 'public' => true, 'label' => 'Favicon'],

            // Contact
            'owner_name' => ['value' => 'Amisha Chauhan', 'type' => 'string', 'group' => 'contact', 'public' => true, 'label' => 'Owner / Proprietor'],
            'contact_email' => ['value' => 'amisha@tripsathi.com', 'type' => 'string', 'group' => 'contact', 'public' => true, 'label' => 'Support Email'],
            'contact_phone' => ['value' => '+91 9904530641', 'type' => 'string', 'group' => 'contact', 'public' => true, 'label' => 'Support Phone'],
            'contact_address' => ['value' => 'Bhavnagar, Gujarat, India', 'type' => 'text', 'group' => 'contact', 'public' => true, 'label' => 'Address'],
            'contact_whatsapp' => ['value' => null, 'type' => 'string', 'group' => 'contact', 'public' => true, 'label' => 'WhatsApp Number'],

            // Social
            'social_facebook' => ['value' => null, 'type' => 'string', 'group' => 'social', 'public' => true, 'label' => 'Facebook URL'],
            'social_instagram' => ['value' => null, 'type' => 'string', 'group' => 'social', 'public' => true, 'label' => 'Instagram URL'],
            'social_x' => ['value' => null, 'type' => 'string', 'group' => 'social', 'public' => true, 'label' => 'X (Twitter) URL'],
            'social_youtube' => ['value' => null, 'type' => 'string', 'group' => 'social', 'public' => true, 'label' => 'YouTube URL'],

            // Commerce
            'currency_symbol' => ['value' => config('tripsathi.currency_symbol', '₹'), 'type' => 'string', 'group' => 'commerce', 'public' => true, 'label' => 'Currency Symbol'],
            'tax_percent' => ['value' => (float) config('tripsathi.tax_percent', 5), 'type' => 'string', 'group' => 'commerce', 'public' => false, 'label' => 'Tax Percentage', 'hint' => 'Applied to the booking subtotal after discount.'],
            'booking_prefix' => ['value' => config('tripsathi.booking_prefix', 'TS'), 'type' => 'string', 'group' => 'commerce', 'public' => false, 'label' => 'Booking Number Prefix'],
            'cancellation_cutoff_hours' => ['value' => (int) config('tripsathi.cancellation_cutoff_hours', 24), 'type' => 'string', 'group' => 'commerce', 'public' => true, 'label' => 'Free Cancellation Cut-off (hours)'],
            'refund_percent' => ['value' => (float) config('tripsathi.refund_percent', 90), 'type' => 'string', 'group' => 'commerce', 'public' => true, 'label' => 'Refund Percentage', 'hint' => 'Refunded when a booking is cancelled inside the free window.'],
            'terms_url' => ['value' => '/terms', 'type' => 'string', 'group' => 'commerce', 'public' => true, 'label' => 'Terms & Conditions URL'],

            // SEO
            'seo_title' => ['value' => 'Trip Sathi — Plan Your Journey', 'type' => 'string', 'group' => 'seo', 'public' => true, 'label' => 'Default SEO Title'],
            'seo_description' => ['value' => 'Book hotels, restaurants, events, adventure activities, VIP darshan and pooja vidhi across India with Trip Sathi.', 'type' => 'text', 'group' => 'seo', 'public' => true, 'label' => 'Default Meta Description'],
            'seo_keywords' => ['value' => 'travel planning, hotel booking, india tourism, vip darshan, adventure activities', 'type' => 'string', 'group' => 'seo', 'public' => true, 'label' => 'Meta Keywords'],
            'seo_og_image' => ['value' => null, 'type' => 'image', 'group' => 'seo', 'public' => true, 'label' => 'Default Open Graph Image'],

            // Footer
            'footer_about' => ['value' => 'Trip Sathi helps travellers plan and book complete journeys — stays, food, events, adventures, darshan and rituals — in one place.', 'type' => 'text', 'group' => 'general', 'public' => true, 'label' => 'Footer About Text'],
        ];
    }

    /**
     * Merged view of defaults + database overrides.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        try {
            $stored = Setting::allCached();
        } catch (\Throwable) {
            $stored = [];
        }

        $merged = [];

        foreach (self::defaults() as $key => $meta) {
            $merged[$key] = array_key_exists($key, $stored) ? $stored[$key] : $meta['value'];
        }

        // Extra rows created at runtime are exposed too.
        foreach ($stored as $key => $value) {
            $merged[$key] ??= $value;
        }

        return $this->cache = $merged;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function bool(string $key, bool $default = false): bool
    {
        return filter_var($this->get($key, $default), FILTER_VALIDATE_BOOL);
    }

    public function int(string $key, int $default = 0): int
    {
        return (int) $this->get($key, $default);
    }

    public function float(string $key, float $default = 0.0): float
    {
        return (float) $this->get($key, $default);
    }

    public function string(string $key, string $default = ''): string
    {
        return (string) $this->get($key, $default);
    }

    public function taxPercent(): float
    {
        return $this->float('tax_percent', (float) config('tripsathi.tax_percent', 5));
    }

    public function currencySymbol(): string
    {
        return $this->string('currency_symbol', (string) config('tripsathi.currency_symbol', '₹'));
    }

    public function siteName(): string
    {
        return $this->string('site_name', (string) config('tripsathi.name', 'Trip Sathi'));
    }

    public function supportEmail(): string
    {
        return $this->string('contact_email', config('mail.from.address', 'support@tripsathi.invalid'));
    }

    /** Persist a batch of settings and bust the cache. */
    public function put(array $values, string $group = 'general'): void
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            Setting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value === null ? null : (string) $value,
                    'group' => $group,
                ],
            );
        }

        $this->flush();
    }

    public function flush(): void
    {
        $this->cache = null;
        Setting::flushCache();
        Cache::forget('settings.all');
    }
}
