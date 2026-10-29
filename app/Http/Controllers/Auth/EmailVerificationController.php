<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    /** Landing page after a successful verification. */
    private const VERIFIED_REDIRECT = 'https://the-trip-sathi.vercel.app/dashboard';

    public function notice(Request $request): \Inertia\Response|RedirectResponse
    {
        // A signed-in user who is already verified never sees this screen.
        if ($request->user()?->hasVerifiedEmail()) {
            return redirect()->away(self::VERIFIED_REDIRECT);
        }

        return \Inertia\Inertia::render('Auth/VerifyEmail', [
            'email' => $request->user()?->getEmailForVerification(),
            'status' => session('status'),
        ]);
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->away(self::VERIFIED_REDIRECT);
        }

        $request->user()->markEmailAsVerified();

        event(new Verified($request->user()));

        $intended = session()->pull('url.intended');
        $redirectUrl = (is_string($intended) && ! str_contains($intended, 'orpin'))
            ? $intended
            : self::VERIFIED_REDIRECT;

        return redirect()
            ->away($redirectUrl)
            ->with('success', 'Your email address has been verified.');
    }

    /**
     * Re-send the verification link behind a cooldown.
     *
     * The cooldown is tracked in cache rather than through Laravel's
     * `hasSentEmailVerificationNotification()` helper, because verification
     * here is queued via a Mailable — no notification row is ever written, so
     * that helper would always be false.
     */
    public function send(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = $request->user();

        if ($user === null) {
            return redirect()->route('login');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(self::VERIFIED_REDIRECT);
        }

        $cooldown = max(0, (int) config('tripsathi.email_resend_cooldown_seconds', 60));
        $key = 'verification.sent.' . $user->getAuthIdentifier();
        $sentAt = (int) cache()->get($key, 0);

        if ($sentAt > 0 && $cooldown > 0) {
            $wait = ($sentAt + $cooldown) - time();

            if ($wait > 0) {
                return back()->with(
                    'status',
                    'A verification link was already sent. Please wait ' . $wait . ' seconds before requesting another.',
                );
            }
        }

        $user->sendEmailVerificationNotification();
        cache()->put($key, time(), max(60, $cooldown));

        return back()->with(
            'status',
            'A fresh verification link has been sent to ' . $user->getEmailForVerification() . '.',
        );
    }
}
