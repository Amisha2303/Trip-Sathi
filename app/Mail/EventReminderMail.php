<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;

/**
 * Sent by the scheduler the day before an event, adventure, darshan or pooja
 * booking is due.
 */
class EventReminderMail extends BaseMailable
{
    public function __construct(public readonly Booking $booking)
    {
        parent::__construct();

        $this->mailSubject = 'Coming up: ' . $this->subjectLine();
        $this->view = 'emails.event-reminder';
    }

    protected function payload(): array
    {
        return [
            'booking' => $this->booking,
            'items' => $this->booking->items,
            'startsAt' => $this->booking->startsAt(),
            'bookingUrl' => $this->urlFor('bookings.show', $this->booking, 'bookings.index'),
        ];
    }

    /** Bookings carry no title column; derive a readable label from the first line item. */
    private function subjectLine(): string
    {
        return $this->booking->title ?: 'your ' . $this->booking->type_label . ' booking';
    }
}