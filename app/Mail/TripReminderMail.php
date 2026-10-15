<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Trip;

/**
 * Sent by the scheduler a few days before a trip starts.
 */
class TripReminderMail extends BaseMailable
{
    public function __construct(public readonly Trip $trip)
    {
        parent::__construct();

        $this->mailSubject = sprintf(
            'Your trip to %s is coming up',
            $this->trip->destination?->name ?? 'your destination',
        );

        $this->view = 'emails.trip-reminder';
    }

    protected function payload(): array
    {
        return [
            'trip' => $this->trip,
            'items' => $this->trip->items,
            'tripUrl' => $this->urlFor('trips.show', $this->trip, 'trips.index'),
            'daysAway' => (int) now()
                ->startOfDay()
                ->diffInDays($this->trip->start_date->startOfDay(), false),
        ];
    }
}