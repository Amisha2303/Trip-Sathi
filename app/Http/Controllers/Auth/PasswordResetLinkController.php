<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): \Inertia\Response
    {
        return \Inertia\Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Always reports success regardless of whether the address is registered.
     * Distinguishing the two outcomes would turn this form into an account
     * enumeration oracle, and the limiter above it already caps abuse.
     */
    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $request->sendLink();

        return back()->with(
            'status',
            'If that address is on file, a password reset link is on its way. Check your inbox.',
        );
    }
}
