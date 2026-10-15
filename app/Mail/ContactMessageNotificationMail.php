<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ContactMessage;

/**
 * Delivers a public contact-form submission to the support inbox.
 */
class ContactMessageNotificationMail extends BaseMailable
{
    public function __construct(public readonly ContactMessage $message)
    {
        parent::__construct();

        $this->mailSubject = 'New support message from ' . $this->message->name;
        $this->view = 'emails.contact-message';
    }

    /** @return array<int, string> */
    public function recipients(): array
    {
        $settings = app(\App\Services\SettingsService::class);

        return array_values(array_filter([$settings->supportEmail()]));
    }

    protected function payload(): array
    {
        // `$message` is reserved in mailable views (the Mail\Message instance),
        // so the ContactMessage model is exposed as `$inquiry` instead.
        return [
            'inquiry' => $this->message,
            'adminUrl' => $this->urlFor('admin.contact-messages.show', $this->message, 'admin.dashboard'),
        ];
    }
}