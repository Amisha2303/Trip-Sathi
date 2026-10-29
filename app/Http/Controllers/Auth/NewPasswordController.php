<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Mail\PasswordChangedMail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request): \Inertia\Response
    {
        return \Inertia\Inertia::render('Auth/ResetPassword', [
            'email' => $request->query('email'),
            'token' => $request->query('token'),
        ]);
    }

    /**
     * The password mutator on User hashes the value, so it is assigned here in
     * plain form and never persisted raw. Rotating `remember_token` below signs
     * every other device out, which is the intent of a password reset.
     */
    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $user = null;

        $status = Password::reset($request->credentials(), function ($account, string $password) use (&$user): void {
            $user = $account;

            $user->forceFill(['password' => $password]);
            $user->setRememberToken(str_repeat('1', 60));

            $user->save();

            event(new PasswordReset($user));
        });

        if ($status !== Password::PASSWORD_RESET || ! $user instanceof User) {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }

        Auth::login($user);

        if ($user->email) {
            try {
                Mail::to($user->email)->send(new PasswordChangedMail($user));
            } catch (\Throwable $e) {
                Log::warning('Password changed email could not be sent: ' . $e->getMessage());
            }
        }

        return redirect()->route('login')->with(
            'success',
            'Your password has been updated. Sign in with your new password.',
        );
    }
}
