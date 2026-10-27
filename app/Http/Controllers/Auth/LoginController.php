<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /** Where to land after a successful sign-in. */
    private const DEFAULT_INTENDED = '/dashboard';

    public function create(): \Inertia\Response
    {
        return \Inertia\Inertia::render('Auth/Login', [
            'canResetPassword' => true,
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Deactivated accounts are rejected after credential validation rather
        // than before: it proves the credentials while denying the session, and
        // it cannot be used to discover which addresses have been disabled.
        if ($user->isDeactivated()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'This account has been deactivated. Contact support.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return $this->intendedResponse($request);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been signed out.');
    }

    /**
     * Honour a validated `next` target so "you were logged out, sign back in"
     * returns the visitor to where they were — but only for same-site paths.
     * Accepting an absolute URL here is an open redirect.
     */
    protected function intendedResponse(Request $request): RedirectResponse
    {
        $next = $request->input('next');

        if (is_string($next) && $this->isSafeLocalPath($next)) {
            return redirect()->to($next);
        }

        if (Auth::user()?->isAdmin()) {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->intended(self::DEFAULT_INTENDED);
    }

    protected function isSafeLocalPath(string $path): bool
    {
        // Relative path on this app: "/dashboard" — never "//evil.com" or
        // "/\evil", which some browsers treat as protocol-relative.
        if (str_starts_with($path, '/')) {
            return strlen($path) < 2 || ($path[1] !== '/' && $path[1] !== '\\');
        }

        // Absolute URLs are accepted when they point at the host the visitor
        // is already on: the guest middleware stores fullUrl() as `next`, so
        // through ngrok (or any tunnel) it includes the public host + scheme.
        $host = parse_url($path, PHP_URL_HOST);

        return is_string($host) && $host === request()->getHost();
    }
}
