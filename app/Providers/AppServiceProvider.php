<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\BookingType;
use App\Enums\PaymentGateway;
use App\Enums\WishlistType;
use App\Services\Payment\PaymentService;
use App\Services\SettingsService;
use App\Support\AdminIpGuard;
use App\Support\CachedContent;
use App\Models\Adventure;
use App\Models\AdventureSlot;
use App\Models\User;
use App\Models\Darshan;
use App\Models\DarshanSlot;
use App\Models\Destination;
use App\Models\Event;
use App\Models\EventTicketType;
use App\Models\FoodExperience;
use App\Models\Hotel;
use App\Models\Pooja;
use App\Models\PoojaSlot;
use App\Models\Restaurant;
use App\Models\RestaurantSlot;
use App\Models\Room;
use App\Models\TripItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Application wiring: morph map, eager-load defaults, view composers,
 * security headers and third-party service binding.
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingsService::class);
        $this->app->singleton(CachedContent::class);
        $this->app->singleton(PaymentService::class);
    }

    public function boot(): void
    {
        $this->registerMorphMap();
        $this->enforceProductionDefaults();
        $this->shareViewData();
        $this->configureValidationDefaults();
        $this->configureRateLimiters();
        $this->configureDatesAndCurrency();
        \Illuminate\Pagination\Paginator::useBootstrapFive();
    }

    /**
     * Explicit morph map.
     *
     * Without it Eloquent stores the fully-qualified class name in
     * `*_type` columns, which makes the tables brittle under refactoring and
     * wastes 60+ bytes per row.
     *
     * @var array<string, class-string<\Illuminate\Database\Eloquent\Model>>
     */
    protected function registerMorphMap(): void
    {
        // Laravel expects alias => model class. The aliases below are what the
        // `*_type` columns already contain.
        $map = [
            'destination' => Destination::class,
            'hotel' => Hotel::class,
            'room' => Room::class,
            'restaurant' => Restaurant::class,
            'food_experience' => FoodExperience::class,
            'event' => Event::class,
            'event_ticket_type' => EventTicketType::class,
            'adventure' => Adventure::class,
            'darshan' => Darshan::class,
            'pooja' => Pooja::class,
            'trip_item' => TripItem::class,
            'adventure_slot' => AdventureSlot::class,
            'darshan_slot' => DarshanSlot::class,
            'pooja_slot' => PoojaSlot::class,
            'restaurant_slot' => RestaurantSlot::class,
            'user' => User::class,
        ];

        // BookingType and WishlistType enums share the same short aliases so
        // `where('type', 'hotel')` works regardless of which side wrote it.
        foreach (BookingType::cases() as $type) {
            $map[$type->value] ??= $type->modelClass();
        }

        foreach (WishlistType::cases() as $type) {
            $map[$type->value] ??= $type->modelClass();
        }

        Relation::enforceMorphMap($map);
    }

    /**
     * Settings that must never be relaxed, even if .env is wrong.
     * Guarding here beats trusting every deployer to remember them.
     */
    protected function enforceProductionDefaults(): void
    {
        if (config('app.env') !== 'production') {
            return;
        }

        Config::set('app.debug', false);
        Config::set('session.secure_cookies', true);
        Config::set('session.same_site', 'lax');

        // Cookie-based sessions are not appropriate behind a load balancer with
        // more than one app server.
        if (Config::get('session.driver') === 'file') {
            Config::set('session.driver', 'database');
        }

        if (config('mail.from.address') === 'hello@example.com') {
            Config::set('mail.from.address', 'no-reply@tripsathi.invalid');
        }

        URL::forceScheme('https');

        \Illuminate\Support\Facades\Vite::useHotFile('/tmp/nonexistent-vite-hot');
    }

    /**
     * Single source of truth for password strength.
     *
     * Referencing Password::defaults() from every FormRequest means tightening
     * the policy here immediately covers registration, reset and profile edits
     * — the failure mode of not having this is three divergent rules.
     */
    protected function configureValidationDefaults(): void
    {
        Password::defaults(function (): Password {
            $rule = Password::min(10)
                ->letters()
                ->numbers()
                ->symbols();

            // Breached-password screening needs outbound HTTP, so it is opted
            // into rather than silently applied to local/CI environments.
            return config('tripsathi.pwned_password_check', false)
                ? $rule->uncompromised()
                : $rule;
        });
    }

    /** Globals every Blade template can rely on. */
    protected function shareViewData(): void
    {
        View::composer('*', function ($view): void {
            try {
                $user = auth()->user();
                $appSettings = app(SettingsService::class)->all();
            } catch (\Throwable) {
                $user = null;
                $appSettings = [];
            }

            $view->with([
                'appSettings' => $appSettings,
                'signedIn' => $user !== null,
                'currentUser' => $user,
                'isAdmin' => $user?->isAdmin() ?? false,
            ]);
        });

        // Only layout views need the unread count; composing every partial for
        // it would issue one query per fragment.
        View::composer(['layouts.app', 'layouts.partials.*'], function ($view): void {
            $view->with('unreadNotificationCount', $this->unreadNotificationCount());
        });
    }

    protected function unreadNotificationCount(): int
    {
        try {
            $user = auth()->user();

            return $user ? $user->notifications()->whereNull('read_at')->count() : 0;
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * Rate limits. Login throttling uses the email+IP composite so one user
     * cannot lock out another from the same NAT address.
     */
    protected function configureRateLimiters(): void
    {
        // RateLimiter is a container singleton; for() is an instance method, so
        // calling it statically is a fatal error at boot.
        $limiter = $this->app->make(RateLimiter::class);

        $limiter->for('api', fn ($request) => \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by(
            $request->user()?->getAuthIdentifier() ?: $request->ip()
        ));

        $limiter->for('login', fn ($request) => \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by(
            $this->throttleKey($request)
        ));

        $limiter->for('booking', fn ($request) => \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by(
            'booking:' . ($request->user()?->getAuthIdentifier() ?: $request->ip())
        ));

        $limiter->for('register', fn ($request) => \Illuminate\Cache\RateLimiting\Limit::perMinute(3)->by(
            'register:' . $request->ip()
        ));

        $limiter->for('contact', fn ($request) => \Illuminate\Cache\RateLimiting\Limit::perMinute(3)->by(
            'contact:' . $request->ip()
        ));
    }

    protected function throttleKey($request): string
    {
        return \Illuminate\Support\Str::transliterate(
            \Illuminate\Support\Str::lower((string) $request->input('email')) . '|' . $request->ip()
        );
    }

    protected function configureDatesAndCurrency(): void
    {
        \Illuminate\Support\Carbon::setLocale(config('app.locale'));

        Blade::directive('money', function (string $expression): void {
            echo "<?php echo \App\Support\Money::format($expression); ?>";
        });

        Blade::directive('rupees', function (string $expression): void {
            echo "<?php echo \App\Support\Money::format($expression); ?>";
        });

        Blade::directive('activeNav', function (string $expression): void {
            echo "<?php echo \App\Support\Nav::active($expression); ?>";
        });

        // Nothing in this app benefits from lazy loading by default, and hiding
        // N+1 mistakes during development is how they reach production.
        Model::preventLazyLoading(! app()->isProduction());
        Model::preventSilentlyDiscardingAttributes(! app()->isProduction());
        Model::preventAccessingMissingAttributes(! app()->isProduction());
    }
}
