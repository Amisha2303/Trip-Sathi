<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ContactMessage;

/**
 * Sent to the customer immediately after they submit a message on the contact form,
 * confirming receipt and setting response time expectations.
 */
class ContactConfirmationMail extends BaseMailable
{
    public function __construct(
        public readonly ContactMessage $inquiry
    ) {
        parent::__construct();

        $this->mailSubject = 'We have received your message · ' . config('mail.from.name', 'Trip Sathi') . ' Support';
        $this->view = 'emails.contact-confirmation';
    }

    protected function payload(): array
    {
        return [
            'inquiry' => $this->inquiry,
            'name' => $this->inquiry->name,
            'ticketId' => 'INQ-' . str_pad((string) $this->inquiry->id, 5, '0', STR_PAD_LEFT),
            'homeUrl' => $this->urlFor('home', [], 'home'),
        ];
    }
}
