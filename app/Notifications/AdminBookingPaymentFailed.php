<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminBookingPaymentFailed extends Notification
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public string $reason,
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
            'title' => 'Payment failed',
            'message' => $this->booking->booking_number.' failed: '.$this->reason,
            'icon' => 'bi-exclamation-triangle',
            'url' => route('admin.bookings.show', $this->booking),
            'booking_id' => $this->booking->getKey(),
        ];
    }
}
