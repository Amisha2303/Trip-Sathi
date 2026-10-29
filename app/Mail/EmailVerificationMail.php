<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;

/**
 * Carries the signed verification link produced by the MustVerifyEmail
 * contract.
 */
class EmailVerificationMail extends BaseMailable
{
    public function __construct(public readonly User $user)
    {
        parent::__construct();

        $this->mailSubject = 'Verify your email address';
        $this->view = 'emails.verify-email';
    }

    protected function payload(): array
    {
        return [
            'user' => $this->user,
            'verificationUrl' => self::toFrontendUrl($this->user->getEmailVerificationUrl()),
        ];
    }
}