<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;
use App\Models\Refund;

/**
 * Refund confirmation with the amount and the gateway reference, so the
 * traveller can reconcile it against their statement.
 */
class PaymentRefundProcessedMail extends BaseMailable
{
    public function __construct(
        public readonly Booking $booking,
        public readonly Refund $refund,
    ) {
        parent::__construct();

        $this->mailSubject = 'Refund processed · ' . $this->booking->booking_number;
        $this->view = 'emails.payment-refunded';
    }

    protected function payload(): array
    {
        return [
            'booking' => $this->booking,
            'refund' => $this->refund,
            'bookingUrl' => $this->urlFor('bookings.show', $this->booking, 'bookings.index'),
        ];
    }
}