<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\SettingsService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default across all Inertia pages.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'is_admin' => (bool) ($user->is_admin ?? false),
                    'email_verified' => $user->hasVerifiedEmail(),
                    'preferences' => method_exists($user, 'prefs') ? $user->prefs() : ($user->preferences ?? []),
                ] : null,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'info' => $request->session()->get('info'),
                'warning' => $request->session()->get('warning'),
            ],
            'appSettings' => app(SettingsService::class)->all(),
            'wishlistCount' => $user ? $user->wishlistItems()->count() : 0,
            'unreadNotificationCount' => $user ? $user->notifications()->whereNull('read_at')->count() : 0,
        ];
    }
}
