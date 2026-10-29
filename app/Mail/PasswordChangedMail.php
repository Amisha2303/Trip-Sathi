<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;

/**
 * Security notification sent when a user's password has been updated.
 */
class PasswordChangedMail extends BaseMailable
{
    public function __construct(
        public readonly User $user
    ) {
        parent::__construct();

        $this->mailSubject = 'Security Notice: Your ' . config('mail.from.name', 'Trip Sathi') . ' password was changed';
        $this->view = 'emails.password-changed';
    }

    protected function payload(): array
    {
        return [
            'user' => $this->user,
            'name' => $this->user->name ?: 'Traveler',
            'updatedAt' => now()->format('d M Y, h:i A T'),
            'resetUrl' => $this->urlFor('password.request', [], 'home'),
            'profileUrl' => $this->urlFor('profile.edit', [], 'dashboard'),
        ];
    }
}
