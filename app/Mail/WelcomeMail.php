<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;

/**
 * Sent immediately after registration: sets expectations and points at the
 * first booking.
 */
class WelcomeMail extends BaseMailable
{
    public function __construct(public readonly User $user)
    {
        parent::__construct();

        $this->mailSubject = 'Welcome to Trip Sathi, ' . $this->firstName() . '!';
        $this->view = 'emails.welcome';
    }

    protected function payload(): array
    {
        return [
            'user' => $this->user,
            'firstName' => $this->firstName(),
            'exploreUrl' => $this->urlFor('destinations.index', [], 'home'),
        ];
    }

    private function firstName(): string
    {
        return explode(' ', trim($this->user->name))[0] ?: $this->user->name;
    }
}
