<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ContactMessage;

/**
 * Sent to the customer when a team member / admin replies to their enquiry.
 */
class ContactReplyMail extends BaseMailable
{
    public function __construct(
        public readonly ContactMessage $inquiry,
        public readonly string $replyText
    ) {
        parent::__construct();

        $this->mailSubject = 'Re: ' . ($this->inquiry->subject ?: 'Your enquiry') . ' · ' . config('mail.from.name', 'Trip Sathi') . ' Support';
        $this->view = 'emails.contact-reply';
    }

    protected function payload(): array
    {
        return [
            'inquiry' => $this->inquiry,
            'replyText' => $this->replyText,
            'responderName' => $this->inquiry->repliedBy?->name ?? 'Trip Sathi Support Team',
            'ticketId' => 'INQ-' . str_pad((string) $this->inquiry->id, 5, '0', STR_PAD_LEFT),
            'homeUrl' => $this->urlFor('home', [], 'home'),
        ];
    }
}
