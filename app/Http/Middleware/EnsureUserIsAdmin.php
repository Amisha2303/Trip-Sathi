<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate the entire /admin area.
 *
 * Split into an authentication step and a role step so the visitor is never
 * told "you need staff access" while signed out — that leaks the existence of
 * the back office.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $request->is('api/*') || $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login', ['next' => $request->fullUrl()]);
        }

        if (! $user->is_admin) {
            return $request->is('api/*') || $request->expectsJson()
                ? response()->json(['message' => 'You do not have access to the administration area.'], 403)
                : abort(403, 'You do not have access to the administration area.');
        }

        // A deactivated account loses its session immediately: leaving an
        // idle admin signed in after suspension is the failure this catches.
        if ($user->isDeactivated()) {
            auth()->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return $request->expectsJson()
                ? response()->json(['message' => 'Your account has been suspended.'], 403)
                : redirect()->route('login', ['suspended' => 1]);
        }

        return $next($request);
    }
}
