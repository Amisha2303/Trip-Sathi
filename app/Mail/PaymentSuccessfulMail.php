<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;

/**
 * Payment receipt. Pairs with the downloadable invoice so the traveller has
 * both a human-readable summary and a PDF record.
 */
class PaymentSuccessfulMail extends BaseMailable
{
    public function __construct(public readonly Booking $booking)
    {
        parent::__construct();

        $this->mailSubject = 'Payment received · ' . $this->booking->booking_number;
        $this->view = 'emails.payment-successful';
    }

    protected function payload(): array
    {
        return [
            'booking' => $this->booking,
            'payment' => $this->booking->payment,
            'bookingUrl' => $this->urlFor('bookings.show', $this->booking, 'bookings.index'),
            'invoiceUrl' => $this->urlFor('bookings.invoice', $this->booking, 'bookings.index'),
        ];
    }
}