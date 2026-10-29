<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Trip;
use App\Models\User;

/**
 * Sent when a traveller designs or books a new custom Trip itinerary.
 */
class TripCreatedMail extends BaseMailable
{
    public function __construct(
        public readonly Trip $trip,
        public readonly ?User $user = null
    ) {
        parent::__construct();

        $this->mailSubject = 'Your trip itinerary is set: ' . $this->trip->name . ' · ' . config('mail.from.name', 'Trip Sathi');
        $this->view = 'emails.trip-created';
    }

    protected function payload(): array
    {
        $this->trip->loadMissing('destination');

        return [
            'trip' => $this->trip,
            'user' => $this->user ?? $this->trip->user,
            'destinationName' => $this->trip->destination?->name ?? 'Custom Destination',
            'tripUrl' => $this->urlFor('trips.index', ['scope' => 'mine'], 'home'),
            'exploreUrl' => $this->urlFor('destinations.index', [], 'home'),
        ];
    }
}
