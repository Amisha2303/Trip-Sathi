<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;

/**
 * Confirms a cancellation and states the refund position, including whether
 * money is on the way back or was not refundable.
 */
class BookingCancelledMail extends BaseMailable
{
    public function __construct(
        public readonly Booking $booking,
        public readonly ?string $refundNote = null,
    ) {
        parent::__construct();

        $this->mailSubject = 'Booking cancelled · ' . $this->booking->booking_number;
        $this->view = 'emails.booking-cancelled';
    }

    protected function payload(): array
    {
        return [
            'booking' => $this->booking,
            'items' => $this->booking->items,
            'refundNote' => $this->refundNote,
            'bookingUrl' => $this->urlFor('bookings.show', $this->booking, 'bookings.index'),
        ];
    }
}