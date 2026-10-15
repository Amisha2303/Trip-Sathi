<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;

/**
 * Explains that the booking is on hold and links back to retry payment. The
 * failure reason is included so the traveller is not left guessing.
 */
class PaymentFailedMail extends BaseMailable
{
    public function __construct(
        public readonly Booking $booking,
        public readonly ?string $reason = null,
    ) {
        parent::__construct();

        $this->mailSubject = 'Payment failed · ' . $this->booking->booking_number;
        $this->view = 'emails.payment-failed';
    }

    protected function payload(): array
    {
        return [
            'booking' => $this->booking,
            'reason' => $this->reason,
            'retryUrl' => $this->urlFor('bookings.pay', $this->booking, 'bookings.index'),
            'bookingUrl' => $this->urlFor('bookings.show', $this->booking, 'bookings.index'),
        ];
    }
}