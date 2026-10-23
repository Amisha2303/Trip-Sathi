<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification
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
            'title' => 'Booking confirmed',
            'message' => $this->booking->booking_number.' is confirmed. We look forward to seeing you.',
            'icon' => 'bi-check-circle',
            'url' => route('bookings.show', $this->booking),
            'booking_id' => $this->booking->getKey(),
        ];
    }
}
