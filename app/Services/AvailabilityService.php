<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Room;
use App\Exceptions\BookingException;
use App\Models\Adventure;
use App\Models\AdventureSlot;
use App\Models\Darshan;
use App\Models\DarshanSlot;
use App\Models\FoodExperience;
use App\Models\Pooja;
use App\Models\PoojaSlot;
use App\Models\Restaurant;
use App\Models\RestaurantSlot;
use App\Support\Money;
use Illuminate\Support\Carbon;

/**
 * Answers "can this be booked, and at what price?" without writing anything.
 *
 * Every booking screen renders availability through this service first, but the
 * authoritative check always runs again inside BookingService's transaction —
 * what the browser shows is a convenience, never a guarantee.
 */
class AvailabilityService
{
    /** Live statuses that hold inventory. */
    private const HOLDING_STATUSES = ['pending', 'confirmed', 'processing', 'completed'];

    /**
     * Can this room take `quantity` units for the whole stay?
     *
     * @return array{available: bool, remaining: int, reason: ?string}
     */
    public function checkRoom(Room $room, string $checkIn, string $checkOut, int $quantity = 1, ?int $excludeBookingId = null): array
    {
        if (! $room->is_active) {
            return $this->deny('This room is no longer available.');
        }

        if (! $room->hotel?->is_active) {
            return $this->deny('This hotel is not currently accepting bookings.');
        }

        $nights = $room->nightsBetween($checkIn, $checkOut);

        if ($nights < 1) {
            return $this->deny('Check-out must be at least one night after check-in.');
        }

        if ($room->min_nights > 0 && $nights < $room->min_nights) {
            return $this->deny("This room has a {$room->min_nights}-night minimum stay.");
        }

        if ($room->max_nights !== null && $nights > $room->max_nights) {
            return $this->deny("This room allows a maximum of {$room->max_nights} nights.");
        }

        $remaining = $room->availabilityFor($checkIn, $checkOut, $excludeBookingId);

        if ($remaining < $quantity) {
            return [
                'available' => false,
                'remaining' => $remaining,
                'reason' => $remaining === 0
                    ? 'No rooms left for one or more nights of your stay.'
                    : "Only {$remaining} room(s) left for your dates.",
            ];
        }

        return ['available' => true, 'remaining' => $remaining, 'reason' => null];
    }

    /**
     * @return array{available: bool, remaining: int, reason: ?string}
     */
    public function checkEventTicket(\App\Models\Event $event, \App\Models\EventTicketType $type, int $quantity): array
    {
        if (! $event->is_active) {
            return $this->deny('This event is no longer on sale.');
        }

        if ($event->is_past) {
            return $this->deny('This event has already taken place.');
        }

        if (! $type->is_active) {
            return $this->deny('This ticket type is not available.');
        }

        if ($type->per_order_limit > 0 && $quantity > $type->per_order_limit) {
            return $this->deny("You can book up to {$type->per_order_limit} ticket(s) per order.");
        }

        $remaining = $type->remaining();

        if ($remaining < $quantity) {
            return [
                'available' => false,
                'remaining' => $remaining,
                'reason' => $remaining === 0
                    ? 'This ticket type is sold out.'
                    : "Only {$remaining} ticket(s) left.",
            ];
        }

        return ['available' => true, 'remaining' => $remaining, 'reason' => null];
    }

    /**
     * @return array{available: bool, remaining: int, reason: ?string}
     */
    public function checkRestaurantSlot(RestaurantSlot $slot, int $seats): array
    {
        if (! $slot->is_active) {
            return $this->deny('This time slot is not available.');
        }

        if (! $slot->slottable?->is_active) {
            return $this->deny('Reservations are closed for this restaurant right now.');
        }

        return $this->checkSlotCapacity($slot, $seats);
    }

    /**
     * @return array{available: bool, remaining: int, reason: ?string}
     */
    public function checkFoodExperienceSlot(RestaurantSlot $slot, int $guests): array
    {
        if (! $slot->is_active) {
            return $this->deny('This time slot is not available.');
        }

        $experience = $slot->slottable;

        if ($experience instanceof FoodExperience) {
            if (! $experience->is_active) {
                return $this->deny('This experience is not currently running.');
            }

            if ($guests < $experience->min_guests) {
                return $this->deny("This experience needs at least {$experience->min_guests} guest(s).");
            }

            if ($guests > $experience->max_guests) {
                return $this->deny("This experience accepts at most {$experience->max_guests} guest(s) per booking.");
            }
        }

        return $this->checkSlotCapacity($slot, $guests);
    }

    /**
     * @return array{available: bool, remaining: int, reason: ?string}
     */
    public function checkAdventureSlot(AdventureSlot $slot, int $participants): array
    {
        if (! $slot->is_active) {
            return $this->deny('This departure slot is closed.');
        }

        if ($slot->isPast()) {
            return $this->deny('This departure has already left.');
        }

        $adventure = $slot->adventure;

        if ($adventure !== null) {
            if (! $adventure->is_active) {
                return $this->deny('This activity is not currently bookable.');
            }

            if ($participants < $adventure->min_participants) {
                return $this->deny("This activity needs at least {$adventure->min_participants} participant(s).");
            }

            if ($participants > $adventure->max_participants) {
                return $this->deny("This activity accepts at most {$adventure->max_participants} participants per booking.");
            }
        }

        return $this->checkSlotCapacity($slot, $participants);
    }

    /**
     * @return array{available: bool, remaining: int, reason: ?string}
     */
    public function checkDarshanSlot(DarshanSlot $slot, int $visitors): array
    {
        if (! $slot->is_active) {
            return $this->deny('This darshan slot is closed.');
        }

        if ($slot->isPast()) {
            return $this->deny('This darshan slot has already passed.');
        }

        $darshan = $slot->darshan;

        if ($darshan !== null) {
            if (! $darshan->is_active) {
                return $this->deny('This darshan is not currently open for booking.');
            }

            if ($visitors > $darshan->max_visitors_per_booking) {
                return $this->deny("You can include up to {$darshan->max_visitors_per_booking} visitors per booking.");
            }
        }

        return $this->checkSlotCapacity($slot, $visitors);
    }

    /**
     * @return array{available: bool, remaining: int, reason: ?string}
     */
    public function checkPoojaSlot(PoojaSlot $slot, int $participants): array
    {
        if (! $slot->is_active) {
            return $this->deny('This pooja slot is closed.');
        }

        if ($slot->isPast()) {
            return $this->deny('This pooja slot has already passed.');
        }

        $pooja = $slot->pooja;

        if ($pooja !== null) {
            if (! $pooja->is_active) {
                return $this->deny('This pooja vidhi is not currently bookable.');
            }

            if ($participants < $pooja->min_participants) {
                return $this->deny("This pooja needs at least {$pooja->min_participants} participant(s).");
            }

            if ($participants > $pooja->max_participants) {
                return $this->deny("This pooja accepts at most {$pooja->max_participants} participants per booking.");
            }
        }

        return $this->checkSlotCapacity($slot, $participants);
    }

    /*
    |--------------------------------------------------------------------------
    | Booking-level checks
    |--------------------------------------------------------------------------
    */

    /**
     * Reject dates in the past — used by every StoreBookingRequest rule set.
     */
    public function assertBookableDate(string|Carbon $date, string $field = 'booking date'): void
    {
        if (Carbon::parse($date)->lt(today())) {
            throw new BookingException("The {$field} cannot be in the past.");
        }
    }

    /** Days a booking may be made in advance, to bound the calendar widget. */
    public function maxAdvanceDays(): int
    {
        return (int) config('tripsathi.booking_max_advance_days', 365);
    }

    public function isWithinBookingWindow(string|Carbon $date): bool
    {
        $date = Carbon::parse($date);

        return $date->gte(today()) && $date->lte(today()->addDays($this->maxAdvanceDays()));
    }

    /*
    |--------------------------------------------------------------------------
    | Internal
    |--------------------------------------------------------------------------
    */

    /**
     * Shared capacity probe for every slot table.
     *
     * @return array{available: bool, remaining: int, reason: ?string}
     */
    private function checkSlotCapacity(
        AdventureSlot|DarshanSlot|PoojaSlot|RestaurantSlot $slot,
        int $quantity,
    ): array {
        $remaining = $this->remainingOnSlot($slot);

        if ($remaining < $quantity) {
            return [
                'available' => false,
                'remaining' => $remaining,
                'reason' => $remaining === 0
                    ? 'This slot is fully booked.'
                    : "Only {$remaining} place(s) left on this slot.",
            ];
        }

        return ['available' => true, 'remaining' => $remaining, 'reason' => null];
    }

    private function remainingOnSlot(
        AdventureSlot|DarshanSlot|PoojaSlot|RestaurantSlot $slot,
    ): int {
        $capacity = match (true) {
            $slot instanceof AdventureSlot => $slot->capacity,
            $slot instanceof DarshanSlot => $slot->max_visitors,
            $slot instanceof PoojaSlot => $slot->max_participants,
            default => $slot->capacity,
        };

        return max(0, $capacity - $this->bookedOnSlot($slot));
    }

    /**
     * Seats already committed, read with a shared lock so two concurrent
     * booking transactions serialise here rather than at commit time.
     */
    private function bookedOnSlot(
        AdventureSlot|DarshanSlot|PoojaSlot|RestaurantSlot $slot,
    ): int {
        return (int) \Illuminate\Support\Facades\DB::table('booking_items')
            ->join('bookings', 'bookings.id', '=', 'booking_items.booking_id')
            ->where('booking_items.slot_table', $slot::class)
            ->where('booking_items.slot_id', $slot->getKey())
            ->whereIn('bookings.status', self::HOLDING_STATUSES)
            ->lockForUpdate()
            ->sum('booking_items.quantity');
    }

    /** @return array{available: false, remaining: int, reason: string} */
    private function deny(string $reason, int $remaining = 0): array
    {
        return ['available' => false, 'remaining' => $remaining, 'reason' => $reason];
    }
}
