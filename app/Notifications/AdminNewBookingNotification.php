<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNewBookingNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New booking',
            'message' => $this->booking->customer_name.' booked '.$this->booking->title
                .' ('.$this->booking->booking_number.').',
            'icon' => 'bi-ticket-perforated',
            'url' => route('admin.bookings.show', $this->booking),
            'booking_id' => $this->booking->getKey(),
        ];
    }
}
