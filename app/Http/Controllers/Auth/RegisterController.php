<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): \Inertia\Response
    {
        return \Inertia\Inertia::render('Auth/Register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        // Hash::make happens in User::setPasswordAttribute, so no plain-text
        // password is ever assigned to the model or written to the log.
        $user = User::create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'phone' => $request->string('phone')->toString(),
            'password' => $request->input('password'),
            'is_active' => true,
            'email_verified_at' => null,
        ]);

        // RBAC roles in this application are staff-only; a regular customer
        // deliberately holds no role, and is authorised by ownership checks
        // rather than by a permission. Assigning a staff role here would grant
        // catalogue write access to every self-registration.
        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->put('auth_user_email', $user->email);
        $request->session()->put('auth_user_id', $user->id);
        $request->session()->put('auth_user_snapshot', $user->only(['id', 'name', 'email', 'phone', 'is_active', 'is_admin']));

        event(new Registered($user));

        try {
            Mail::to($user)->send(new WelcomeMail($user));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Welcome email could not be sent: ' . $e->getMessage());
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Welcome to Trip Sathi! Please verify your email address.');
    }
}
