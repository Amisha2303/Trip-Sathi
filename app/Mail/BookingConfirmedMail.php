<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;

/**
 * Sent once a booking reaches Confirmed — payment settled, or staff
 * confirmed it out of band.
 */
class BookingConfirmedMail extends BaseMailable
{
    public function __construct(public readonly Booking $booking)
    {
        parent::__construct();

        $this->mailSubject = 'Booking confirmed · ' . $this->booking->booking_number;
        $this->view = 'emails.booking-confirmed';
    }

    protected function payload(): array
    {
        return [
            'booking' => $this->booking,
            'items' => $this->booking->items,
            'bookingUrl' => $this->urlFor('bookings.show', $this->booking, 'bookings.index'),
            'invoiceUrl' => $this->urlFor('bookings.invoice', $this->booking, 'bookings.index'),
        ];
    }
}