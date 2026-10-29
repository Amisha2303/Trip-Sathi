<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;

/**
 * Security notification sent to a user when a new sign-in occurs.
 */
class LoginAlertMail extends BaseMailable
{
    /**
     * @param array{ip?: string, user_agent?: string, time?: string} $deviceInfo
     */
    public function __construct(
        public readonly User $user,
        public readonly array $deviceInfo = []
    ) {
        parent::__construct();

        $this->mailSubject = 'Security alert: New login to your ' . config('mail.from.name', 'Trip Sathi') . ' account';
        $this->view = 'emails.login-alert';
    }

    protected function payload(): array
    {
        return [
            'user' => $this->user,
            'name' => $this->user->name ?: 'Traveler',
            'email' => $this->user->email,
            'ip' => $this->deviceInfo['ip'] ?? request()->ip() ?? 'Unknown IP',
            'userAgent' => $this->deviceInfo['user_agent'] ?? request()->userAgent() ?? 'Web Browser',
            'loginTime' => $this->deviceInfo['time'] ?? now()->format('d M Y, h:i A T'),
            'resetUrl' => $this->urlFor('password.request', [], 'home'),
            'profileUrl' => $this->urlFor('profile.edit', [], 'dashboard'),
        ];
    }
}
