<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Booking;

/**
 * Alerts the operations team that a booking needs attention. Goes to the
 * configured operations address rather than to individual admins.
 */
class AdminBookingNotificationMail extends BaseMailable
{
    public function __construct(public readonly Booking $booking)
    {
        parent::__construct();

        $this->mailSubject = 'New booking · ' . $this->booking->booking_number;
        $this->view = 'emails.admin-new-booking';
    }

    /**
     * Operations address from settings, with a hard fallback so a misconfigured
     * DB never silently drops an alert.
     *
     * @return array<int, string>
     */
    public function recipients(): array
    {
        $address = app(\App\Services\SettingsService::class)->string('ops_email')
            ?: app(\App\Services\SettingsService::class)->supportEmail();

        return array_values(array_filter([$address, config('mail.from.address')]));
    }

    protected function payload(): array
    {
        return [
            'booking' => $this->booking,
            'items' => $this->booking->items,
            'adminUrl' => $this->urlFor('admin.bookings.show', $this->booking, 'admin.dashboard'),
        ];
    }
}