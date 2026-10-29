<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;

/**
 * Password reset mail carrying the signed token URL.
 */
class PasswordResetMail extends BaseMailable
{
    public function __construct(
        public readonly User $user,
        public readonly string $token,
    ) {
        parent::__construct();

        $this->mailSubject = 'Reset your Trip Sathi password';
        $this->view = 'emails.reset-password';
    }

    protected function payload(): array
    {
        return [
            'user' => $this->user,
            'resetUrl' => $this->urlFor('password.reset', ['token' => $this->token, 'email' => $this->user->email]),
            'expiresIn' => (int) config('auth.passwords.users.expire', 60),
        ];
    }
}