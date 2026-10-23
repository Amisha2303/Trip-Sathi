<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public ?string $reason = null,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Booking cancelled',
            'message' => $this->booking->booking_number.' was cancelled.'
                .($this->reason ? ' Reason: '.$this->reason : ''),
            'icon' => 'bi-x-circle',
            'url' => route('bookings.show', $this->booking),
            'booking_id' => $this->booking->getKey(),
        ];
    }
}
