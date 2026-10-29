<?php

declare(strict_types=1);

namespace App\Mail;

/**
 * Sent when a visitor subscribes to the Trip Sathi newsletter.
 * Gives them a warm welcome and an exclusive 10% coupon code.
 */
class NewsletterSubscribedMail extends BaseMailable
{
    public function __construct(
        public readonly string $subscriberEmail
    ) {
        parent::__construct();

        $this->mailSubject = 'Welcome to the Trip Sathi Travel Club! Here is your 10% gift code';
        $this->view = 'emails.newsletter-subscribed';
    }

    protected function payload(): array
    {
        return [
            'subscriberEmail' => $this->subscriberEmail,
            'couponCode' => 'WELCOME10',
            'discountPercent' => 10,
            'exploreUrl' => $this->urlFor('destinations.index', [], 'home'),
            'tripsUrl' => $this->urlFor('trips.index', [], 'home'),
        ];
    }
}
