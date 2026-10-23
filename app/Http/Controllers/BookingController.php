<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Exceptions\BookingException;
use App\Models\Adventure;
use App\Models\Booking;
use App\Models\Darshan;
use App\Models\Event;
use App\Models\FoodExperience;
use App\Models\Hotel;
use App\Models\Pooja;
use App\Models\Restaurant;
use App\Services\BookingInventoryService;
use App\Services\BookingService;
use App\Services\Payment\PaymentService;
use App\Services\SettingsService;
use App\Support\Catalogue;
use App\Support\Money;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    private const BOOKABLE_TYPES = [
        'hotel', 'event', 'adventure', 'darshan', 'pooja', 'restaurant', 'food',
    ];

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $statuses = [...BookingStatus::values(), 'upcoming'];

        $query = $request->user()->bookings()->with(['destination', 'items', 'payment']);

        if ($status === 'upcoming') {
            $query->upcoming();
        } elseif (in_array($status, BookingStatus::values(), true)) {
            $query->where('status', $status);
        } else {
            $status = null;
        }

        $bookings = $query->latest()->paginate(10)->withQueryString();

        $counts = [
            'all' => $request->user()->bookings()->count(),
            'upcoming' => $request->user()->bookings()->upcoming()->count(),
            'pending' => $request->user()->bookings()->where('status', 'pending')->count(),
            'completed' => $request->user()->bookings()->where('status', 'completed')->count(),
            'cancelled' => $request->user()->bookings()->where('status', 'cancelled')->count(),
        ];

        return view('account.bookings', [
            'bookings' => $bookings,
            'status' => $status,
            'statuses' => BookingStatus::cases(),
            'counts' => $counts,
        ]);
    }

    public function create(Request $request, string $type, string $slug, BookingInventoryService $inventory): View
    {
        $model = $this->resolveBookable($type, $slug);

        $inventory->ensure($model, $type);

        return view('bookings.create', $this->formData($type, $model, $inventory));
    }

    public function store(
        Request $request,
        string $type,
        string $slug,
        BookingInventoryService $inventory,
        BookingService $bookings,
        PaymentService $payments,
    ): RedirectResponse {
        $model = $this->resolveBookable($type, $slug);

        $inventory->ensure($model, $type);

        try {
            $booking = match ($type) {
                'hotel' => $this->storeHotel($request, $model, $bookings),
                'event' => $this->storeEvent($request, $model, $bookings),
                'adventure' => $this->storeAdventure($request, $model, $bookings),
                'darshan' => $this->storeDarshan($request, $model, $bookings),
                'pooja' => $this->storePooja($request, $model, $bookings),
                'restaurant' => $this->storeRestaurant($request, $model, $bookings),
                'food' => $this->storeFood($request, $model, $bookings),
            };
        } catch (BookingException $e) {
            return back()
                ->withInput()
                ->withErrors(['booking' => $e->getMessage()]);
        }

        $payments->openPending($booking);

        return redirect()
            ->route('bookings.pay', $booking)
            ->with('success', 'Your booking is held. Complete payment to confirm it.');
    }

    public function show(Request $request, Booking $booking): View
    {
        $this->authorizeBooking($request, $booking);

        $booking->load([
            'items',
            'destination:id,name,slug',
            'coupon:id,code,name',
            'payments.transactions',
            'refunds',
        ]);

        return view('bookings.show', [
            'booking' => $booking,
            'latestPayment' => $booking->payments->sortByDesc('id')->first(),
        ]);
    }

    public function invoice(Request $request, Booking $booking): View
    {
        $this->authorizeBooking($request, $booking);

        $booking->load(['items', 'destination:id,name', 'payments']);

        return view('bookings.invoice', [
            'booking' => $booking,
            'payment' => $booking->payments->sortByDesc('id')->first(),
        ]);
    }

    public function pay(Request $request, Booking $booking, PaymentService $payments): View|RedirectResponse
    {
        $this->authorizeBooking($request, $booking);

        if ($booking->payment_status->isSettled()) {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('success', 'This booking is already paid.');
        }

        $booking->load(['items', 'destination:id,name']);

        $gateway = $payments->gateway();

        return view('bookings.pay', [
            'booking' => $booking,
            'latestPayment' => $booking->payment()->first(),
            'gatewayName' => $gateway->gateway()->label(),
            'isOnline' => $gateway->gateway()->isOnline(),
            'isConfigured' => $gateway->isConfigured(),
            'currency' => $booking->currency,
            'taxPercent' => app(SettingsService::class)->taxPercent(),
        ]);
    }

    public function initiatePayment(Request $request, Booking $booking, PaymentService $payments): View|RedirectResponse
    {
        $this->authorizeBooking($request, $booking);

        if ($booking->payment_status->isSettled()) {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('success', 'This booking is already paid.');
        }

        $booking->load(['items', 'destination:id,name']);

        $result = $payments->initiate($booking);

        if ($result->failed()) {
            return redirect()
                ->route('bookings.pay', $booking)
                ->with('error', $result->failureReason ?? 'Payment could not be started. Please try again.');
        }

        if ($result->requiresCheckout()) {
            return view('bookings.checkout', [
                'booking' => $booking,
                'result' => $result,
            ]);
        }

        return redirect()
            ->route('bookings.pay', $booking)
            ->with('success', $result->message);
    }

    public function callback(Request $request, Booking $booking, PaymentService $payments): RedirectResponse
    {
        $this->authorizeBooking($request, $booking);

        if (! $payments->isOnline()) {
            return redirect()
                ->route('bookings.pay', $booking)
                ->with('error', 'Online payment is not enabled for this booking.');
        }

        $result = $payments->verifyAndCapture($booking, $request->all());

        if ($result->failed()) {
            return redirect()
                ->route('bookings.pay', $booking)
                ->with('error', $result->failureReason ?? 'Payment could not be verified.');
        }

        return redirect()
            ->route('bookings.show', $booking)
            ->with('success', 'Payment received. Your booking is confirmed.');
    }

    private function storeHotel(Request $request, Model $model, BookingService $bookings): Booking
    {
        $data = $request->validate([
            'room_id' => [
                'required', 'integer',
                Rule::exists('rooms', 'id')->where('hotel_id', $model->getKey()),
            ],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'adults' => ['required', 'integer', 'min:1', 'max:10'],
            'children' => ['nullable', 'integer', 'min:0', 'max:8'],
            'rooms' => ['required', 'integer', 'min:1', 'max:10'],
            'coupon_code' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $room = $model->rooms()->whereKey($data['room_id'])->firstOrFail();
        $room->load('hotel');

        return $bookings->bookHotel(
            $request->user(),
            $room,
            [
                'guests' => [
                    'adults' => (int) $data['adults'],
                    'children' => (int) ($data['children'] ?? 0),
                ],
                'coupon_code' => $data['coupon_code'] ?? null,
                'notes' => $data['notes'] ?? null,
            ],
            $data['check_in'],
            $data['check_out'],
            (int) $data['rooms'],
        );
    }

    private function storeEvent(Request $request, Model $model, BookingService $bookings): Booking
    {
        $data = $request->validate([
            'ticket_type_id' => [
                'required', 'integer',
                Rule::exists('event_ticket_types', 'id')->where('event_id', $model->getKey()),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'attendees' => ['nullable', 'array', 'max:20'],
            'attendees.*' => ['nullable', 'string', 'max:120'],
            'coupon_code' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $ticket = $model->ticketTypes()->whereKey($data['ticket_type_id'])->firstOrFail();

        return $bookings->bookEvent(
            $request->user(),
            $model,
            $ticket,
            [
                'guests' => array_values(array_filter($data['attendees'] ?? [])),
                'coupon_code' => $data['coupon_code'] ?? null,
                'notes' => $data['notes'] ?? null,
            ],
            (int) $data['quantity'],
        );
    }

    private function storeAdventure(Request $request, Model $model, BookingService $bookings): Booking
    {
        $max = max(1, (int) ($model->max_participants ?: 10));

        $data = $request->validate([
            'slot_id' => [
                'required', 'integer',
                Rule::exists('adventure_slots', 'id')->where('adventure_id', $model->getKey()),
            ],
            'participants' => ['required', 'array', 'min:1', 'max:'.$max],
            'participants.*.name' => ['required', 'string', 'max:120'],
            'participants.*.age' => ['required', 'integer', 'min:1', 'max:99'],
            'emergency_contact' => ['nullable', 'string', 'max:30'],
            'coupon_code' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $slot = $model->slots()->whereKey($data['slot_id'])->firstOrFail();
        $slot->load('adventure');

        return $bookings->bookAdventure($request->user(), $model, $slot, [
            'participants' => array_values($data['participants']),
            'emergency_contact' => $data['emergency_contact'] ?? null,
            'coupon_code' => $data['coupon_code'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    private function storeDarshan(Request $request, Model $model, BookingService $bookings): Booking
    {
        $requiresId = $model->requiresIdProof();

        $rules = [
            'slot_id' => [
                'required', 'integer',
                Rule::exists('darshan_slots', 'id')->where('darshan_id', $model->getKey()),
            ],
            'visitors' => ['required', 'array', 'min:1', 'max:'.max(1, (int) ($model->max_visitors_per_booking ?: 10))],
            'visitors.*.name' => ['required', 'string', 'max:120'],
            'coupon_code' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($requiresId) {
            $rules['visitors.*.id_number'] = ['required', 'string', 'max:60'];
        } else {
            $rules['visitors.*.id_number'] = ['nullable', 'string', 'max:60'];
        }

        $data = $request->validate($rules);

        $slot = $model->slots()->whereKey($data['slot_id'])->firstOrFail();
        $slot->load('darshan');

        return $bookings->bookDarshan($request->user(), $model, $slot, [
            'visitors' => array_values($data['visitors']),
            'coupon_code' => $data['coupon_code'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    private function storePooja(Request $request, Model $model, BookingService $bookings): Booking
    {
        $max = max(1, (int) ($model->max_participants ?: 20));

        $data = $request->validate([
            'slot_id' => [
                'required', 'integer',
                Rule::exists('pooja_slots', 'id')->where('pooja_id', $model->getKey()),
            ],
            'participants' => ['required', 'integer', 'min:1', 'max:'.$max],
            'devotee_name' => ['required', 'string', 'max:120'],
            'coupon_code' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $slot = $model->slots()->whereKey($data['slot_id'])->firstOrFail();
        $slot->load('pooja');

        return $bookings->bookPooja($request->user(), $model, $slot, [
            'participants' => (int) $data['participants'],
            'devotee_name' => $data['devotee_name'],
            'coupon_code' => $data['coupon_code'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    private function storeRestaurant(Request $request, Model $model, BookingService $bookings): Booking
    {
        $data = $request->validate([
            'slot_id' => [
                'required', 'integer',
                Rule::exists('restaurant_slots', 'id')
                    ->where('slottable_type', $model->getMorphClass())
                    ->where('slottable_id', $model->getKey()),
            ],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'guests' => ['required', 'integer', 'min:1', 'max:30'],
            'occasion' => ['nullable', 'string', 'max:60'],
            'seating' => ['nullable', 'string', 'max:60'],
            'coupon_code' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $slot = $model->slots()->whereKey($data['slot_id'])->firstOrFail();
        $slot->load('slottable');

        return $bookings->bookRestaurant($request->user(), $model, $slot, [
            'guests' => (int) $data['guests'],
            'occasion' => $data['occasion'] ?? null,
            'seating' => $data['seating'] ?? null,
            'coupon_code' => $data['coupon_code'] ?? null,
            'notes' => $data['notes'] ?? null,
        ], $data['date']);
    }

    private function storeFood(Request $request, Model $model, BookingService $bookings): Booking
    {
        $max = max(1, (int) ($model->max_guests ?: 10));

        $data = $request->validate([
            'slot_id' => [
                'required', 'integer',
                Rule::exists('restaurant_slots', 'id')
                    ->where('slottable_type', $model->getMorphClass())
                    ->where('slottable_id', $model->getKey()),
            ],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'guests' => ['required', 'integer', 'min:1', 'max:'.$max],
            'dietary' => ['nullable', 'string', 'max:120'],
            'coupon_code' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $slot = $model->slots()->whereKey($data['slot_id'])->firstOrFail();
        $slot->load('slottable');

        return $bookings->bookFoodExperience($request->user(), $model, $slot, [
            'guests' => (int) $data['guests'],
            'dietary' => $data['dietary'] ?? null,
            'coupon_code' => $data['coupon_code'] ?? null,
            'notes' => $data['notes'] ?? null,
        ], $data['date']);
    }

    private function resolveBookable(string $type, string $slug): Model
    {
        abort_unless(in_array($type, self::BOOKABLE_TYPES, true), 404);
        $class = match ($type) {
            'hotel' => Hotel::class,
            'event' => Event::class,
            'adventure' => Adventure::class,
            'darshan' => Darshan::class,
            'pooja' => Pooja::class,
            'restaurant' => Restaurant::class,
            'food' => FoodExperience::class,
        };

        return $class::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    private function formData(string $type, Model $model, BookingInventoryService $inventory): array
    {
        $detail = Catalogue::detail($model, $type);

        $slots = match ($type) {
            'adventure', 'darshan', 'pooja' => $inventory->upcomingSlots($model, $type),
            'restaurant', 'food' => $inventory->serviceSlots($model),
            default => collect(),
        };

        return [
            'type' => $type,
            'model' => $model,
            'detail' => $detail,
            'rooms' => $type === 'hotel' ? $inventory->rooms($model) : collect(),
            'tickets' => $type === 'event' ? $inventory->tickets($model) : collect(),
            'slots' => $slots,
            'today' => today()->toDateString(),
            'tomorrow' => today()->addDay()->toDateString(),
            'maxDate' => today()->addYear()->toDateString(),
            'requiresIdProof' => $type === 'darshan' && $model instanceof Darshan && $model->requiresIdProof(),
            'maxParticipants' => $type === 'adventure'
                ? max(1, (int) ($model->max_participants ?: 10))
                : ($type === 'pooja' ? max(1, (int) ($model->max_participants ?: 20)) : 10),
            'maxVisitors' => $type === 'darshan' ? max(1, (int) ($model->max_visitors_per_booking ?: 10)) : 10,
            'maxGuests' => $type === 'food' ? max(1, (int) ($model->max_guests ?: 10)) : 30,
            'taxPercent' => app(SettingsService::class)->taxPercent(),
            'currencySymbol' => Money::symbol(),
            'gatewayLabel' => PaymentService::configuredGateway()->label(),
        ];
    }

    private function authorizeBooking(Request $request, Booking $booking): void
    {
        abort_unless(
            (int) $booking->user_id === (int) $request->user()->id || $request->user()->isAdmin(),
            403,
        );
    }
}
