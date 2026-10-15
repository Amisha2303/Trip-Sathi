<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Exceptions\BookingException;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmedMail;
use App\Mail\BookingCreatedMail;
use App\Models\Adventure;
use App\Models\AdventureSlot;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Darshan;
use App\Models\DarshanSlot;
use App\Models\Event;
use App\Models\EventTicketType;
use App\Models\FoodExperience;
use App\Models\Hotel;
use App\Models\Pooja;
use App\Models\PoojaSlot;
use App\Models\Restaurant;
use App\Models\RestaurantSlot;
use App\Models\Room;
use App\Models\User;
use App\Services\Payment\PaymentService;
use App\Support\Money;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * The single write path for every booking in the platform.
 *
 * Concurrency model
 * -----------------
 * All work happens inside one transaction. Availability is re-verified there
 * with row locks (`lockForUpdate`) rather than trusting the earlier read, so two
 * simultaneous submissions for the last room cannot both succeed. Money is
 * computed with integer minor units and stored on the header, then recalculated
 * from the persisted line items so the invoice always adds up.
 *
 * Side effects (mail, notifications, PDF warm-up) run *after* commit, never
 * inside the transaction — a slow SMTP server must not hold a database lock.
 */
class BookingService
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly CouponService $coupons,
        private readonly PaymentService $payments,
        private readonly SettingsService $settings,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Hotel bookings
    |--------------------------------------------------------------------------
    */

    /**
     * @param  array{guests?: array<int,array<string,mixed>>, notes?: string|null, coupon_code?: string|null, trip_id?: int|null}  $data
     */
    public function bookHotel(User $user, Room $room, array $data, string $checkIn, string $checkOut, int $quantity = 1): Booking
    {
        $adults = (int) ($data['guests']['adults'] ?? 1);
        $children = (int) ($data['guests']['children'] ?? 0);
        $guests = $adults + $children;

        if (! $room->fitsGuests($adults, $children)) {
            throw new BookingException(sprintf(
                'This room sleeps %d adult(s) and %d child(ren); you selected %d and %d.',
                $room->max_adults,
                $room->max_children,
                $adults,
                $children,
            ));
        }

        $check = $this->availability->checkRoom($room, $checkIn, $checkOut, $quantity);

        if (! $check['available']) {
            throw new BookingException($check['reason']);
        }

        $nights = $room->nightsBetween($checkIn, $checkOut);
        $unitPrice = Booking::toMinor($room->effective_price);
        $discount = (int) round($unitPrice * $nights * ((float) $room->discount_percent / 100));

        return $this->create($user, [
            'booking_type' => BookingType::Hotel,
            'bookable' => $room,
            'destination_id' => $room->hotel?->destination_id,
            'booking_date' => $checkIn,
            'end_date' => $checkOut,
            'start_time' => $room->hotel?->check_in_time,
            'end_time' => $room->hotel?->check_out_time,
            'nights' => $nights,
            'quantity' => $quantity,
            'guests' => $guests,
            'coupon_code' => $data['coupon_code'] ?? null,
            'notes' => $data['notes'] ?? null,
            'trip_id' => $data['trip_id'] ?? null,
            'items' => [[
                'item_type' => BookingType::Hotel,
                'itemable' => $room,
                'name' => $room->name,
                'description' => $room->description,
                'image' => $room->main_image,
                'service_date' => $checkIn,
                'quantity' => $quantity,
                'guests' => $guests,
                'unit_price' => $unitPrice,
                'discount' => $discount,
                'options' => [
                    'hotel' => $room->hotel?->name,
                    'room_type' => $room->room_type,
                    'bed_type' => $room->bed_type,
                    'nights' => $nights,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'breakfast_included' => $room->breakfast_included ? 'Yes' : 'No',
                    'refundable' => $room->is_refundable ? 'Yes' : 'No',
                ],
                // A hotel stay occupies one inventory unit per night; the
                // availability check already proved every night is free.
                'per_night' => true,
            ]],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Restaurant / food experience bookings
    |--------------------------------------------------------------------------
    */

    /**
     * @param  array{guests?: int, occasion?: string|null, seating?: string|null, notes?: string|null, coupon_code?: string|null}  $data
     */
    public function bookRestaurant(User $user, Restaurant $restaurant, RestaurantSlot $slot, array $data, string $date): Booking
    {
        $guests = max(1, (int) ($data['guests'] ?? 2));

        $check = $this->availability->checkRestaurantSlot($slot, $guests);

        if (! $check['available']) {
            throw new BookingException($check['reason']);
        }

        // A table reservation is charged per guest.
        $unitPrice = Booking::toMinor($restaurant->average_cost);

        return $this->create($user, [
            'booking_type' => BookingType::Restaurant,
            'bookable' => $restaurant,
            'destination_id' => $restaurant->destination_id,
            'booking_date' => $date,
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'quantity' => $guests,
            'guests' => $guests,
            'coupon_code' => $data['coupon_code'] ?? null,
            'notes' => $data['notes'] ?? null,
            'items' => [[
                'item_type' => BookingType::Restaurant,
                'itemable' => $restaurant,
                'slot_id' => $slot->getKey(),
                'slot_table' => $slot::class,
                'name' => $restaurant->name,
                'description' => $restaurant->short_description,
                'image' => $restaurant->main_image,
                'service_date' => $date,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'quantity' => $guests,
                'guests' => $guests,
                'unit_price' => $unitPrice,
                'options' => [
                    'cuisine' => $restaurant->cuisine,
                    'occasion' => $data['occasion'] ?? null,
                    'seating' => $data['seating'] ?? null,
                    'time' => $slot->time_label ?? $slot->label,
                ],
            ]],
        ]);
    }

    /**
     * @param  array{guests?: int, notes?: string|null, coupon_code?: string|null, dietary?: string|null}  $data
     */
    public function bookFoodExperience(User $user, FoodExperience $experience, RestaurantSlot $slot, array $data, string $date): Booking
    {
        $guests = max(1, (int) ($data['guests'] ?? 2));

        $check = $this->availability->checkFoodExperienceSlot($slot, $guests);

        if (! $check['available']) {
            throw new BookingException($check['reason']);
        }

        return $this->create($user, [
            'booking_type' => BookingType::FoodExperience,
            'bookable' => $experience,
            'destination_id' => $experience->destination_id,
            'booking_date' => $date,
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'quantity' => $guests,
            'guests' => $guests,
            'coupon_code' => $data['coupon_code'] ?? null,
            'notes' => $data['notes'] ?? null,
            'items' => [[
                'item_type' => BookingType::FoodExperience,
                'itemable' => $experience,
                'slot_id' => $slot->getKey(),
                'slot_table' => $slot::class,
                'name' => $experience->name,
                'description' => $experience->short_description,
                'image' => $experience->main_image,
                'service_date' => $date,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'quantity' => $guests,
                'guests' => $guests,
                // Priced per participant.
                'unit_price' => Booking::toMinor($experience->effective_price),
                'options' => [
                    'cuisine' => $experience->cuisine,
                    'duration' => $experience->duration_minutes . ' minutes',
                    'dietary' => $data['dietary'] ?? null,
                ],
            ]],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    /**
     * @param  array{coupon_code?: string|null, notes?: string|null, guests?: array<int,string>}  $data
     */
    public function bookEvent(User $user, Event $event, EventTicketType $type, array $data, int $quantity = 1): Booking
    {
        $check = $this->availability->checkEventTicket($event, $type, $quantity);

        if (! $check['available']) {
            throw new BookingException($check['reason']);
        }

        return $this->create($user, [
            'booking_type' => BookingType::Event,
            'bookable' => $event,
            'destination_id' => $event->destination_id,
            'booking_date' => $event->event_date->toDateString(),
            'start_time' => $event->starts_at->format('H:i:s'),
            'end_time' => $event->ends_at->format('H:i:s'),
            'quantity' => $quantity,
            'guests' => $quantity,
            'coupon_code' => $data['coupon_code'] ?? null,
            'notes' => $data['notes'] ?? null,
            'items' => [[
                'item_type' => BookingType::Event,
                'itemable' => $type,
                'name' => $event->name . ' — ' . $type->name,
                'description' => $event->short_description,
                'image' => $event->main_image,
                'service_date' => $event->event_date->toDateString(),
                'start_time' => $event->starts_at->format('H:i:s'),
                'end_time' => $event->ends_at->format('H:i:s'),
                'quantity' => $quantity,
                'guests' => $quantity,
                'unit_price' => Booking::toMinor($type->effective_price),
                'options' => [
                    'event' => $event->name,
                    'ticket_type' => $type->name,
                    'venue' => $event->venue,
                    'attendees' => $data['guests'] ?? [],
                ],
            ]],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Adventures
    |--------------------------------------------------------------------------
    */

    /**
     * @param  array{participants?: array<int,array<string,mixed>>, coupon_code?: string|null, notes?: string|null, emergency_contact?: string|null}  $data
     */
    public function bookAdventure(User $user, Adventure $adventure, AdventureSlot $slot, array $data): Booking
    {
        $participants = collect($data['participants'] ?? [])->values();
        $count = max(1, $participants->count());

        $check = $this->availability->checkAdventureSlot($slot, $count);

        if (! $check['available']) {
            throw new BookingException($check['reason']);
        }

        // Eligibility is advisory but surfaced: the operator still gets to refuse.
        $warnings = $adventure->checkEligibility(
            isset($data['participants'][0]['age']) ? (int) $data['participants'][0]['age'] : null,
        );

        return $this->create($user, [
            'booking_type' => BookingType::Adventure,
            'bookable' => $adventure,
            'destination_id' => $adventure->destination_id,
            'booking_date' => $slot->slot_date->toDateString(),
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'quantity' => $count,
            'guests' => $count,
            'coupon_code' => $data['coupon_code'] ?? null,
            'notes' => implode(' ', array_filter([$data['notes'] ?? null, ...$warnings])) ?: null,
            'items' => [[
                'item_type' => BookingType::Adventure,
                'itemable' => $adventure,
                'slot_id' => $slot->getKey(),
                'slot_table' => $slot::class,
                'name' => $adventure->name,
                'description' => $adventure->short_description,
                'image' => $adventure->main_image,
                'service_date' => $slot->slot_date->toDateString(),
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'quantity' => $count,
                'guests' => $count,
                'unit_price' => Booking::toMinor($adventure->effective_price),
                'options' => [
                    'activity' => $adventure->activity_type,
                    'difficulty' => $adventure->difficulty_label,
                    'duration' => $adventure->duration_label,
                    'slot' => $slot->label,
                    'participants' => $participants->all(),
                    'emergency_contact' => $data['emergency_contact'] ?? null,
                ],
            ]],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | VIP Darshan
    |--------------------------------------------------------------------------
    */

    /**
     * @param  array{visitors?: array<int,array<string,string>>, id_proof?: string|null, coupon_code?: string|null, notes?: string|null}  $data
     */
    public function bookDarshan(User $user, Darshan $darshan, DarshanSlot $slot, array $data): Booking
    {
        $visitors = collect($data['visitors'] ?? [])->values();
        $count = max(1, $visitors->count());

        // Document requirements are enforced here, not just warned about in the UI.
        if ($darshan->requiresIdProof()) {
            $missing = $visitors->first(fn ($v) => blank(data_get($v, 'id_number')));

            if ($missing !== null && $visitors->count() > 0) {
                throw new BookingException(
                    'An ID document number is required for every visitor (' . $darshan->name . ').',
                );
            }
        }

        $check = $this->availability->checkDarshanSlot($slot, $count);

        if (! $check['available']) {
            throw new BookingException($check['reason']);
        }

        return $this->create($user, [
            'booking_type' => BookingType::Darshan,
            'bookable' => $darshan,
            'destination_id' => $darshan->destination_id,
            'booking_date' => $slot->slot_date->toDateString(),
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'quantity' => $count,
            'guests' => $count,
            'coupon_code' => $data['coupon_code'] ?? null,
            'notes' => $data['notes'] ?? null,
            'items' => [[
                'item_type' => BookingType::Darshan,
                'itemable' => $darshan,
                'slot_id' => $slot->getKey(),
                'slot_table' => $slot::class,
                'name' => $darshan->name,
                'description' => $darshan->short_description,
                'image' => $darshan->main_image,
                'service_date' => $slot->slot_date->toDateString(),
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'quantity' => $count,
                'guests' => $count,
                // Priced per visitor.
                'unit_price' => Booking::toMinor($darshan->effective_price),
                'visitor_details' => $visitors->all(),
                'options' => [
                    'temple' => $darshan->temple_name,
                    'deity' => $darshan->deity,
                    'darshan_type' => $darshan->darshan_type,
                    'slot' => $slot->label,
                    'documents' => $darshan->documents_list,
                    'dress_code' => $darshan->dress_code,
                ],
            ]],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Pooja Vidhi
    |--------------------------------------------------------------------------
    */

    /**
     * @param  array{participants?: array<int,array<string,string>>|int, devotee_name?: string|null, coupon_code?: string|null, notes?: string|null}  $data
     */
    public function bookPooja(User $user, Pooja $pooja, PoojaSlot $slot, array $data): Booking
    {
        $participants = is_array($data['participants'] ?? null)
            ? collect($data['participants'])->values()
            : collect(range(1, max(1, (int) ($data['participants'] ?? 1))))
                ->map(fn (int $i) => ['name' => $data['devotee_name'] ?? $user->name, 'index' => $i]);

        $count = max(1, $participants->count());

        $check = $this->availability->checkPoojaSlot($slot, $count);

        if (! $check['available']) {
            throw new BookingException($check['reason']);
        }

        return $this->create($user, [
            'booking_type' => BookingType::Pooja,
            'bookable' => $pooja,
            'destination_id' => $pooja->destination_id,
            'booking_date' => $slot->slot_date->toDateString(),
            'start_time' => $slot->start_time,
            'end_time' => $slot->end_time,
            'quantity' => $count,
            'guests' => $count,
            'coupon_code' => $data['coupon_code'] ?? null,
            'notes' => $data['notes'] ?? null,
            'items' => [[
                'item_type' => BookingType::Pooja,
                'itemable' => $pooja,
                'slot_id' => $slot->getKey(),
                'slot_table' => $slot::class,
                'name' => $pooja->name,
                'description' => $pooja->short_description,
                'image' => $pooja->main_image,
                'service_date' => $slot->slot_date->toDateString(),
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'quantity' => $count,
                'guests' => $count,
                // Priced per participant.
                'unit_price' => Booking::toMinor($pooja->effective_price),
                'visitor_details' => $participants->all(),
                'options' => [
                    'temple' => $pooja->temple_name,
                    'deity' => $pooja->deity,
                    'slot' => $slot->label,
                    'duration' => $pooja->duration_minutes . ' minutes',
                    'requirements' => $pooja->requirements,
                ],
            ]],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Core create
    |--------------------------------------------------------------------------
    */

    /**
     * Persist a booking and its line items inside one transaction.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws BookingException
     */
    public function create(User $user, array $data): Booking
    {
        $booking = DB::transaction(function () use ($user, $data): Booking {
            $items = $this->persistItems($data['items'] ?? []);

            if ($items->isEmpty()) {
                throw new BookingException('A booking needs at least one item.');
            }

            $totals = $this->calculateTotals($items, $data['coupon_code'] ?? null, $user);

            $booking = Booking::create([
                'booking_number' => $this->nextBookingNumber(),
                'user_id' => $user->getKey(),
                'destination_id' => $data['destination_id'] ?? null,
                'trip_id' => $data['trip_id'] ?? null,
                'coupon_id' => $totals['coupon']?->getKey(),
                'booking_type' => $data['booking_type'],
                'bookable_type' => $data['bookable']?->getMorphClass(),
                'bookable_id' => $data['bookable']?->getKey(),
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'booking_date' => $data['booking_date'],
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'nights' => $data['nights'] ?? 1,
                'quantity' => $data['quantity'] ?? 1,
                'guests' => $data['guests'] ?? 1,
                'subtotal' => $totals['subtotal'],
                'discount' => $totals['discount'],
                'tax' => $totals['tax'],
                'final_amount' => $totals['final'],
                'currency' => config('tripsathi.currency', 'INR'),
                'payment_status' => PaymentStatus::Pending->value,
                'status' => BookingStatus::Pending->value,
                'notes' => $data['notes'] ?? null,
                'ip_address' => request()->ip(),
                'source' => $data['source'] ?? 'web',
            ]);

            // Tax is allocated against the gross line totals so the printed
            // invoice always re-adds to the header, rounding included.
            $grossLineTotal = (int) $items->sum(fn (BookingItem $i) => (int) $i->getAttribute('line_total'));

            foreach ($items as $item) {
                $lineTotal = (int) $item->getAttribute('line_total');

                $item->booking_id = $booking->getKey();
                $item->tax = $this->allocateTax($totals['tax'], $lineTotal, $grossLineTotal);
                $item->total_amount = $lineTotal - (int) $item->discount + (int) $item->tax;
                $item->save();
            }

            // Reconcile rounding drift so the lines always sum exactly to the header.
            $this->reconcileLineTotals($booking, $items);

            if ($totals['coupon'] !== null) {
                $this->recordCouponUsage($totals['coupon'], $user, $booking, $totals['discount']);
            }

            $booking->setRelation('items', $items);

            return $booking;
        });

        $booking->load(['user', 'items', 'destination']);

        // Outside the transaction: mail must never hold the booking lock.
        Mail::to($booking->customer_email)->queue(new BookingCreatedMail($booking));
        User::notifyAdmins(\App\Notifications\AdminNewBookingNotification::class, $booking);

        return $booking;
    }

    /*
    |--------------------------------------------------------------------------
    | Status transitions
    |--------------------------------------------------------------------------
    */

    public function confirm(Booking $booking): Booking
    {
        if ($booking->transitionTo(BookingStatus::Confirmed)) {
            Mail::to($booking->customer_email)->queue(new BookingConfirmedMail($booking));
            $booking->user?->notifyBookingConfirmed($booking);
        }

        return $booking->refresh();
    }

    public function complete(Booking $booking): Booking
    {
        return $booking->transitionTo(BookingStatus::Completed) ? $booking->refresh() : $booking;
    }

    /*
    |--------------------------------------------------------------------------
    | Booking numbers
    |--------------------------------------------------------------------------
    */

    /**
     * Sequential, human-readable reference: TS-2026-000001.
     *
     * The count is taken under a named MySQL advisory lock so two concurrent
     * signups can never mint the same number.
     */
    public function nextBookingNumber(): string
    {
        $prefix = $this->settings->string('booking_prefix', (string) config('tripsathi.booking_prefix', 'TS'));
        $year = now()->year;

        $lockName = 'tripsathi:booking_number:' . $year;

        $connection = DB::connection();
        $sequence = 0;

        // Advisory locks are MySQL-only; degrade to the unlocked path elsewhere.
        if ($connection->getDriverName() === 'mysql') {
            $connection->statement('SELECT GET_LOCK(?, 5) AS acquired', [$lockName]);

            try {
                $sequence = (int) Booking::withTrashed()
                    ->whereYear('created_at', $year)
                    ->count() + 1;
            } finally {
                $connection->statement('SELECT RELEASE_LOCK(?)', [$lockName]);
            }
        } else {
            $sequence = (int) Booking::withTrashed()
                ->whereYear('created_at', $year)
                ->count() + 1;
        }

        // Retry once on the (astronomically unlikely) unique-constraint clash.
        do {
            $number = sprintf('%s-%d-%06d', strtoupper($prefix), $year, $sequence);
            $sequence++;

            $exists = Booking::withTrashed()->where('booking_number', $number)->exists();
        } while ($exists && $sequence < 999999);

        return $number;
    }

    /*
    |--------------------------------------------------------------------------
    | Internals
    |--------------------------------------------------------------------------
    */

    /**
     * Turn the request-shaped item array into unsaved BookingItem models,
     * expanding a multi-night hotel stay into one row per night so the
     * availability query can count occupancy per date.
     *
     * @param  array<int, array<string, mixed>>  $definitions
     *
     * @return Collection<int, BookingItem>
     */
    private function persistItems(array $definitions): Collection
    {
        return collect($definitions)->flatMap(function (array $definition): array {
            $item = new BookingItem([
                'item_type' => $definition['item_type'] instanceof BookingType
                    ? $definition['item_type']->value
                    : $definition['item_type'],
                'itemable_type' => $definition['itemable']?->getMorphClass(),
                'itemable_id' => $definition['itemable']?->getKey(),
                'slot_id' => $definition['slot_id'] ?? null,
                'slot_table' => $definition['slot_table'] ?? null,
                'name' => $definition['name'],
                'description' => $definition['description'] ?? null,
                'image' => $definition['image'] ?? null,
                'service_date' => $definition['service_date'] ?? null,
                'start_time' => $definition['start_time'] ?? null,
                'end_time' => $definition['end_time'] ?? null,
                'quantity' => $definition['quantity'] ?? 1,
                'guests' => $definition['guests'] ?? 1,
                'unit_price' => $definition['unit_price'] ?? 0,
                'discount' => $definition['discount'] ?? 0,
                'options' => $definition['options'] ?? null,
                'visitor_details' => $definition['visitor_details'] ?? null,
            ]);

            if (! ($definition['per_night'] ?? false)) {
                return [$item];
            }

            // One row per night: check-in night included, check-out excluded.
            $rows = [];
            $nights = (int) ($definition['options']['nights'] ?? 1);
            $date = Carbon::parse($definition['service_date']);

            for ($i = 0; $i < $nights; $i++) {
                $night = $item->replicate();
                $night->service_date = $date->copy()->addDays($i)->toDateString();
                $night->quantity = $definition['quantity'] ?? 1;
                $night->start_time = $i === 0 ? ($definition['start_time'] ?? null) : null;
                $night->end_time = $i === $nights - 1 ? ($definition['end_time'] ?? null) : null;
                $rows[] = $night;
            }

            return $rows;
        });
    }

    /**
     * Subtotal → discount → tax, all in integer minor units.
     *
     * @param  Collection<int, BookingItem>  $items
     *
     * @return array{subtotal: int, discount: int, tax: int, final: int, coupon: ?Coupon}
     */
    private function calculateTotals(Collection $items, ?string $couponCode, User $user): array
    {
        $subtotal = (int) $items->sum(
            fn (BookingItem $i) => ((int) $i->getAttribute('line_total')) - (int) $i->discount
        );

        $coupon = null;
        $discount = 0;

        if (filled($couponCode)) {
            $coupon = $this->coupons->resolve($couponCode, $user, $subtotal, $items);

            if ($coupon === null) {
                throw new BookingException('That coupon code is not valid for this booking.', ['coupon' => $couponCode]);
            }

            $discount = $this->coupons->discountFor($coupon, $subtotal);
        }

        $taxable = max(0, $subtotal - $discount);
        $tax = Money::percentOf($taxable, $this->settings->taxPercent());

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'final' => max(0, $taxable + $tax),
            'coupon' => $coupon,
        ];
    }

    /**
     * Spread the header tax across line items proportionally so the printed
     * invoice lines always re-add to the header total.
     */
        /**
     * Push any rounding remainder onto the largest line.
     *
     * Proportional allocation with `round()` can leave the lines one or two
     * paise short of the header total; without this the printed invoice does
     * not add up, which is exactly the kind of bug a customer notices.
     */
    private function reconcileLineTotals(Booking $booking, Collection $items): void
    {
        $headerTotal = (int) $booking->subtotal - (int) $booking->discount + (int) $booking->tax;
        $lineSum = (int) $items->sum(fn (BookingItem $i) => (int) $i->total_amount);

        $drift = $headerTotal - $lineSum;

        if ($drift === 0 || $items->isEmpty()) {
            return;
        }

        $target = $items->sortByDesc(fn (BookingItem $i) => (int) $i->total_amount)->first();

        $target->total_amount = max(0, (int) $target->total_amount + $drift);
        $target->save();
    }

    private function allocateTax(int $totalTax, int $lineTotal, int $grossLineTotal): int
    {
        if ($totalTax <= 0 || $lineTotal <= 0 || $grossLineTotal <= 0) {
            return 0;
        }

        return (int) round($totalTax * ($lineTotal / $grossLineTotal));
    }

    private function recordCouponUsage(Coupon $coupon, User $user, Booking $booking, int $discountMinor): void
    {
        CouponUsage::create([
            'coupon_id' => $coupon->getKey(),
            'user_id' => $user->getKey(),
            'booking_id' => $booking->getKey(),
            'discount_amount' => Money::decimal(Booking::toRupees($discountMinor)),
        ]);

        $coupon->increment('used_count');
    }

    /**
     * Live quote for the checkout summary — identical maths to `create()` but
     * performs no writes, so the "order summary" panel matches the invoice.
     *
     * @param  array<int, array<string, mixed>>  $definitions
     *
     * @return array<string, mixed>
     */
    public function quote(array $definitions, ?string $couponCode = null, ?User $user = null): array
    {
        $items = $this->persistItems($definitions);

        $subtotal = (int) $items->sum(
            fn (BookingItem $i) => ((int) $i->getAttribute('line_total')) - (int) $i->discount
        );

        $coupon = null;
        $discount = 0;

        if (filled($couponCode)) {
            $coupon = $this->coupons->resolve($couponCode, $user, $subtotal, $items);
            $discount = $coupon ? $this->coupons->discountFor($coupon, $subtotal) : 0;
        }

        $taxable = max(0, $subtotal - $discount);
        $tax = Money::percentOf($taxable, $this->settings->taxPercent());

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'final' => max(0, $taxable + $tax),
            'coupon' => $coupon,
            'coupon_error' => filled($couponCode) && $coupon === null
                ? 'That coupon code is not valid for this booking.'
                : null,
        ];
    }
}
