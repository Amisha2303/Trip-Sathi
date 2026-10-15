<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Login request with brute-force throttling.
 *
 * Throttling lives here rather than in middleware so a failed *attempt* (bad
 * password) and a blocked *submission* produce one consistent response, and so
 * the limiter is shared with any other caller of the `login` bucket.
 */
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Authenticate with a locked-out path distinct from "wrong password",
     * because telling a user which one failed is an information leak only
     * after lockout — before that we must say nothing useful either way.
     *
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public function authenticate(): array
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');
        $remember = (bool) $this->input('remember', false);

        if (Auth::attempt($credentials, $remember)) {
            // A successful sign-in resets the counter immediately; otherwise a
            // user who signs in regularly could still be locked out from one
            // honest typo five minutes later.
            RateLimiter::clear($this->throttleKey());

            return $credentials;
        }

        RateLimiter::hit($this->throttleKey(), $this->lockSeconds());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /** @throws ValidationException */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $this->maxAttempts())) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function maxAttempts(): int
    {
        return max(1, (int) config('tripsathi.login_max_attempts', 5));
    }

    protected function lockSeconds(): int
    {
        return max(60, (int) config('tripsathi.login_lockout_seconds', 600));
    }

    /** email + IP: one address cannot lock out everyone behind a NAT. */
    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower((string) $this->input('email')) . '|' . $this->ip()
        );
    }
}
