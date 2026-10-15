<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;

/**
 * Immediate acknowledgement that the booking exists. Payment is not yet
 * settled at this point, so the template must not imply confirmation.
 */
class BookingCreatedMail extends BaseMailable
{
    public function __construct(public readonly Booking $booking)
    {
        parent::__construct();

        $this->mailSubject = 'Booking received · ' . $this->booking->booking_number;
        $this->view = 'emails.booking-created';
    }

    protected function payload(): array
    {
        return [
            'booking' => $this->booking,
            'items' => $this->booking->items,
            'bookingUrl' => $this->urlFor('bookings.show', $this->booking, 'bookings.index'),
            'payUrl' => $this->urlFor('bookings.pay', $this->booking, 'bookings.index'),
        ];
    }
}