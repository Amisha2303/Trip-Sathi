<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures absolute integrity between the browser session and authenticated user model.
 * Prevents cross-user session bleeding or container ID collisions in serverless environments.
 */
class VerifySessionUserIntegrity
{
    public function handle(Request $request, Closure $next): Response
    {
        $session = $request->hasSession() ? $request->session() : null;

        if ($session && Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $sessionEmail = $session->get('auth_user_email');

            if ($sessionEmail && $user && strtolower($user->email) !== strtolower($sessionEmail)) {
                Log::warning('Cross-user session mismatch prevented!', [
                    'session_email' => $sessionEmail,
                    'model_email' => $user->email,
                    'ip' => $request->ip(),
                ]);

                Auth::guard('web')->logout();
                $session->invalidate();
                $session->regenerateToken();

                if ($request->expectsJson() || $request->header('X-Inertia')) {
                    return redirect()->route('login')->withErrors([
                        'email' => 'Your session was reset for security. Please sign in again.',
                    ]);
                }

                return redirect()->route('login');
            }

            // Sync session verification tokens if missing
            if ($user && ! $sessionEmail) {
                $session->put('auth_user_email', $user->email);
                $session->put('auth_user_id', $user->id);
                $session->put('auth_user_snapshot', $user->only(['id', 'name', 'email', 'phone', 'is_active', 'is_admin']));
            }
        }

        $response = $next($request);

        // Enforce strict no-cache on all web/HTML/Inertia responses so edge CDN never caches user data
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
