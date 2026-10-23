@extends('layouts.app')

@section('title', 'Book ' . $detail['name'] . ' — Trip Sathi')
@section('meta-description', 'Secure your ' . strtolower($detail['eyebrow']) . ' booking with Trip Sathi.')

@section('content')
    <section class="ts-booking-head">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route(\App\Support\Catalogue::indexRoute($type)) }}">{{ ucfirst($detail['eyebrow']) }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route(\App\Support\Catalogue::showRoute($type), ['slug' => $model->slug]) }}">{{ $detail['name'] }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Book</li>
                </ol>
            </nav>

            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
                <div>
                    <span class="ts-eyebrow">Secure booking</span>
                    <h1 class="ts-title mb-2">{{ $detail['name'] }}</h1>
                    <div class="ts-card-meta">
                        @if ($detail['location'])
                            <span><i class="bi bi-geo-alt"></i> {{ $detail['location'] }}</span>
                        @endif
                        @if ($detail['rating'] !== null)
                            <span><i class="bi bi-star-fill text-warning"></i> {{ number_format($detail['rating'], 1) }}
                                ({{ $detail['reviewCount'] }})</span>
                        @endif
                    </div>
                </div>
                @if ($detail['price'] !== null)
                    <div class="ts-price fs-4">
                        {{ \App\Support\Money::decimal($detail['price']) }}
                        <small>{{ $detail['priceNote'] }}</small>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="ts-section ts-section-tight">
        <div class="container">
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>We could not create this booking.</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('bookings.store', ['type' => $type, 'slug' => $model->slug]) }}"
                id="booking-form"
                data-type="{{ $type }}"
                data-tax="{{ $taxPercent }}"
                data-currency="{{ $currencySymbol }}"
                data-unit-price="{{ (float) ($detail['price'] ?? 0) }}">
                @csrf

                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="ts-booking-card">
                            <div class="ts-booking-card-head">
                                <span class="ts-booking-step">1</span>
                                <div>
                                    <h2 class="ts-title h5 mb-1">Choose your details</h2>
                                    <p class="text-muted small mb-0">Availability is confirmed when you submit.</p>
                                </div>
                            </div>

                            <div class="p-3 p-lg-4">
                            @if ($type === 'hotel')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Room</label>
                                    <div class="ts-booking-options">
                                        @foreach ($rooms as $room)
                                            <label class="ts-booking-option">
                                                <input class="form-check-input" type="radio" name="room_id"
                                                    value="{{ $room->id }}" data-price="{{ $room->effective_price }}"
                                                    data-room-max="{{ $room->max_occupancy }}"
                                                    @checked((string) old('room_id') === (string) $room->id || ($loop->first && ! old('room_id')))>
                                                <span>
                                                    <strong>{{ $room->name }}</strong>
                                                    <span class="d-block small text-muted">
                                                        Sleeps {{ $room->max_occupancy }}
                                                        @if ($room->breakfast_included) · Breakfast included @endif
                                                        · {{ $room->total_rooms }} room(s) available
                                                    </span>
                                                </span>
                                                <span class="ts-booking-option-price">
                                                    {{ \App\Support\Money::decimal($room->effective_price) }}
                                                    <small>per night</small>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="check_in">Check-in</label>
                                        <input class="form-control @error('check_in') is-invalid @enderror" type="date"
                                            id="check_in" name="check_in" min="{{ $today }}"
                                            value="{{ old('check_in', $tomorrow) }}" required>
                                        @error('check_in')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="check_out">Check-out</label>
                                        <input class="form-control @error('check_out') is-invalid @enderror" type="date"
                                            id="check_out" name="check_out" min="{{ $tomorrow }}"
                                            value="{{ old('check_out', today()->addDays(2)->toDateString()) }}" required>
                                        @error('check_out')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label" for="adults">Adults</label>
                                        <input class="form-control" type="number" id="adults" name="adults" min="1" max="10"
                                            value="{{ old('adults', 2) }}" required>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label" for="children">Children</label>
                                        <input class="form-control" type="number" id="children" name="children" min="0" max="8"
                                            value="{{ old('children', 0) }}">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label" for="rooms">Rooms</label>
                                        <input class="form-control" type="number" id="rooms" name="rooms" min="1" max="10"
                                            value="{{ old('rooms', 1) }}" required>
                                    </div>
                                </div>
                            @endif

                            @if ($type === 'event')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Ticket type</label>
                                    <div class="ts-booking-options">
                                        @foreach ($tickets as $ticket)
                                            <label class="ts-booking-option">
                                                <input class="form-check-input" type="radio" name="ticket_type_id"
                                                    value="{{ $ticket->id }}" data-price="{{ $ticket->effective_price }}"
                                                    @checked((string) old('ticket_type_id') === (string) $ticket->id || ($loop->first && ! old('ticket_type_id')))>
                                                <span>
                                                    <strong>{{ $ticket->name }}</strong>
                                                    <span class="d-block small text-muted">{{ $ticket->remaining() }} left · up to {{ $ticket->per_order_limit }} per order</span>
                                                </span>
                                                <span class="ts-booking-option-price">
                                                    {{ \App\Support\Money::decimal($ticket->effective_price) }}
                                                    <small>per ticket</small>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label">Event date</label>
                                        <input class="form-control" type="text"
                                            value="{{ $model->event_date?->format('D, d M Y') }}"
                                            @if ($model->starts_at) data-time="{{ $model->starts_at->format('g:i A') }}" @endif readonly>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="quantity">Tickets</label>
                                        <input class="form-control @error('quantity') is-invalid @enderror" type="number"
                                            id="quantity" name="quantity" min="1" max="20"
                                            value="{{ old('quantity', 1) }}" required>
                                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            @endif

                            @if ($type === 'adventure')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Departure</label>
                                    <select class="form-select @error('slot_id') is-invalid @enderror" id="slot_id" name="slot_id" required>
                                        <option value="">Choose a date and time</option>
                                        @foreach ($slots as $slot)
                                            <option value="{{ $slot->id }}" @selected((string) old('slot_id') === (string) $slot->id)>
                                                {{ $slot->label }} · {{ $slot->remaining() }} place(s) left
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('slot_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-semibold mb-0">Participants</label>
                                    <button class="btn btn-sm btn-soft" type="button" data-add-row="participant">
                                        <i class="bi bi-plus-lg"></i> Add person
                                    </button>
                                </div>
                                <div data-rows="participant">
                                    @php
                                        $participantRows = old('participants', [['name' => '', 'age' => '']]);
                                    @endphp
                                    @foreach ($participantRows as $index => $participant)
                                        <div class="row g-2 align-items-end ts-booking-row" data-row>
                                            <div class="col-7">
                                                <label class="form-label small">Full name</label>
                                                <input class="form-control" type="text" name="participants[{{ $index }}][name]"
                                                    value="{{ $participant['name'] ?? '' }}" required>
                                            </div>
                                            <div class="col-3">
                                                <label class="form-label small">Age</label>
                                                <input class="form-control" type="number" name="participants[{{ $index }}][age]"
                                                    min="1" max="99" value="{{ $participant['age'] ?? '' }}" required>
                                            </div>
                                            <div class="col-2">
                                                <button class="btn btn-outline-secondary w-100" type="button" data-remove-row aria-label="Remove participant">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <label class="form-label" for="emergency_contact">Emergency contact</label>
                                    <input class="form-control" type="text" id="emergency_contact" name="emergency_contact"
                                        value="{{ old('emergency_contact') }}" placeholder="Name and phone number">
                                </div>
                            @endif

                            @if ($type === 'darshan')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Darshan slot</label>
                                    <select class="form-select @error('slot_id') is-invalid @enderror" id="slot_id" name="slot_id" required>
                                        <option value="">Choose a date and time</option>
                                        @foreach ($slots as $slot)
                                            <option value="{{ $slot->id }}" @selected((string) old('slot_id') === (string) $slot->id)>
                                                {{ $slot->label }} · {{ $slot->remaining() }} place(s) left
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('slot_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-semibold mb-0">Visitors</label>
                                    <button class="btn btn-sm btn-soft" type="button" data-add-row="visitor">
                                        <i class="bi bi-plus-lg"></i> Add visitor
                                    </button>
                                </div>
                                @if ($requiresIdProof)
                                    <p class="small text-muted">ID details are required for every visitor and checked at the temple.</p>
                                @endif
                                <div data-rows="visitor">
                                    @php
                                        $visitorRows = old('visitors', [['name' => '', 'id_number' => '']]);
                                    @endphp
                                    @foreach ($visitorRows as $index => $visitor)
                                        <div class="row g-2 align-items-end ts-booking-row" data-row>
                                            <div class="col-5">
                                                <label class="form-label small">Visitor name</label>
                                                <input class="form-control" type="text" name="visitors[{{ $index }}][name]"
                                                    value="{{ $visitor['name'] ?? '' }}" required>
                                            </div>
                                            <div class="col-5">
                                                <label class="form-label small">ID number {{ $requiresIdProof ? '' : '(optional)' }}</label>
                                                <input class="form-control" type="text" name="visitors[{{ $index }}][id_number]"
                                                    value="{{ $visitor['id_number'] ?? '' }}" @required($requiresIdProof)>
                                            </div>
                                            <div class="col-2">
                                                <button class="btn btn-outline-secondary w-100" type="button" data-remove-row aria-label="Remove visitor">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if ($type === 'pooja')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Pooja slot</label>
                                    <select class="form-select @error('slot_id') is-invalid @enderror" id="slot_id" name="slot_id" required>
                                        <option value="">Choose a date and time</option>
                                        @foreach ($slots as $slot)
                                            <option value="{{ $slot->id }}" @selected((string) old('slot_id') === (string) $slot->id)>
                                                {{ $slot->label }} · {{ $slot->remaining() }} place(s) left
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('slot_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="devotee_name">Devotee name</label>
                                        <input class="form-control" type="text" id="devotee_name" name="devotee_name"
                                            value="{{ old('devotee_name', $currentUser?->name) }}" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="participants">Participants</label>
                                        <input class="form-control" type="number" id="participants" name="participants"
                                            min="1" max="{{ $maxParticipants }}" value="{{ old('participants', 1) }}" required>
                                    </div>
                                </div>
                            @endif

                            @if ($type === 'restaurant')
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="date">Reservation date</label>
                                        <input class="form-control" type="date" id="date" name="date"
                                            min="{{ $today }}" value="{{ old('date', $today) }}" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="guests">Guests</label>
                                        <input class="form-control" type="number" id="guests" name="guests"
                                            min="1" max="30" value="{{ old('guests', 2) }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Time</label>
                                        <div class="ts-booking-options">
                                            @foreach ($slots as $slot)
                                                <label class="ts-booking-option">
                                                    <input class="form-check-input" type="radio" name="slot_id" value="{{ $slot->id }}"
                                                        @checked((string) old('slot_id') === (string) $slot->id || ($loop->first && ! old('slot_id')))>
                                                    <span><strong>{{ $slot->label }}</strong>
                                                        <span class="d-block small text-muted">{{ $slot->tables }} table(s) · seats are confirmed on submit</span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="occasion">Occasion (optional)</label>
                                        <select class="form-select" id="occasion" name="occasion">
                                            <option value="">Just dining</option>
                                            @foreach (['Birthday', 'Anniversary', 'Business meal', 'Family gathering'] as $occasion)
                                                <option value="{{ $occasion }}" @selected(old('occasion') === $occasion)>{{ $occasion }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="seating">Seating preference</label>
                                        <select class="form-select" id="seating" name="seating">
                                            <option value="">No preference</option>
                                            @foreach (['Indoor', 'Outdoor', 'Window', 'Private dining'] as $seating)
                                                <option value="{{ $seating }}" @selected(old('seating') === $seating)>{{ $seating }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            @if ($type === 'food')
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label" for="date">Experience date</label>
                                        <input class="form-control" type="date" id="date" name="date"
                                            min="{{ $today }}" value="{{ old('date', $today) }}" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label" for="guests">Guests</label>
                                        <input class="form-control" type="number" id="guests" name="guests"
                                            min="1" max="{{ $maxGuests }}" value="{{ old('guests', 2) }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Session</label>
                                        <div class="ts-booking-options">
                                            @foreach ($slots as $slot)
                                                <label class="ts-booking-option">
                                                    <input class="form-check-input" type="radio" name="slot_id" value="{{ $slot->id }}"
                                                        @checked((string) old('slot_id') === (string) $slot->id || ($loop->first && ! old('slot_id')))>
                                                    <span><strong>{{ $slot->label }}</strong>
                                                        <span class="d-block small text-muted">{{ $slot->capacity }} seats per session</span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="dietary">Dietary notes (optional)</label>
                                        <input class="form-control" type="text" id="dietary" name="dietary"
                                            value="{{ old('dietary') }}" placeholder="Vegetarian, allergies, spice level…">
                                    </div>
                                </div>
                            @endif

                            <hr class="my-4">

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label" for="coupon_code">Coupon code (optional)</label>
                                    <input class="form-control" type="text" id="coupon_code" name="coupon_code"
                                        value="{{ old('coupon_code') }}" placeholder="Enter code">
                                    <div class="form-text">Validated when you continue.</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="notes">Special requests</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                        placeholder="Anything we should know?">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <aside class="ts-booking-summary">
                            <div class="ts-booking-card">
                                <div class="ts-booking-media">
                                    <img src="{{ $detail['mainImage'] }}" alt="{{ $detail['name'] }}" loading="lazy">
                                </div>
                                <div class="p-3 p-lg-4">
                                    <span class="ts-eyebrow">{{ $detail['eyebrow'] }}</span>
                                    <h2 class="ts-title h5 mb-1">{{ $detail['name'] }}</h2>
                                    <p class="text-muted small mb-3">{{ $detail['location'] }}</p>

                                    <dl class="ts-booking-lines">
                                        <div>
                                            <dt data-summary-label>Rate</dt>
                                            <dd data-summary-rate>—</dd>
                                        </div>
                                        <div data-summary-qty-row>
                                            <dt data-summary-qty-label>Quantity</dt>
                                            <dd data-summary-qty>—</dd>
                                        </div>
                                        <div>
                                            <dt>Subtotal</dt>
                                            <dd data-summary-subtotal>—</dd>
                                        </div>
                                        <div data-summary-discount-row hidden>
                                            <dt>Coupon discount</dt>
                                            <dd data-summary-discount>—</dd>
                                        </div>
                                        <div>
                                            <dt>Taxes &amp; fees ({{ rtrim(rtrim(number_format($taxPercent, 2), '0'), '.') }}%)</dt>
                                            <dd data-summary-tax>—</dd>
                                        </div>
                                        <div class="ts-booking-total">
                                            <dt>Total due</dt>
                                            <dd data-summary-total>—</dd>
                                        </div>
                                    </dl>

                                    <button class="btn btn-accent w-100 btn-lg mt-3" type="submit">
                                        Continue to payment <i class="bi bi-arrow-right"></i>
                                    </button>

                                    <ul class="ts-booking-trust">
                                        <li><i class="bi bi-shield-check"></i> Availability re-checked at checkout</li>
                                        <li><i class="bi bi-credit-card"></i> {{ $gatewayLabel }} payment</li>
                                        <li><i class="bi bi-headset"></i> Human support if plans change</li>
                                    </ul>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <template id="participant-row-template">
        <div class="row g-2 align-items-end ts-booking-row" data-row>
            <div class="col-7">
                <label class="form-label small">Full name</label>
                <input class="form-control" type="text" name="participants[__INDEX__][name]" required>
            </div>
            <div class="col-3">
                <label class="form-label small">Age</label>
                <input class="form-control" type="number" name="participants[__INDEX__][age]" min="1" max="99" required>
            </div>
            <div class="col-2">
                <button class="btn btn-outline-secondary w-100" type="button" data-remove-row aria-label="Remove participant">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </template>

    <template id="visitor-row-template">
        <div class="row g-2 align-items-end ts-booking-row" data-row>
            <div class="col-5">
                <label class="form-label small">Visitor name</label>
                <input class="form-control" type="text" name="visitors[__INDEX__][name]" required>
            </div>
            <div class="col-5">
                <label class="form-label small">ID number</label>
                <input class="form-control" type="text" name="visitors[__INDEX__][id_number]" @if ($requiresIdProof) required @endif>
            </div>
            <div class="col-2">
                <button class="btn btn-outline-secondary w-100" type="button" data-remove-row aria-label="Remove visitor">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('booking-form');
            if (!form) return;

            const type = form.dataset.type;
            const currency = form.dataset.currency || '₹';
            const taxPercent = parseFloat(form.dataset.tax || '0');
            const fallbackUnit = parseFloat(form.dataset.unitPrice || '0');

            const money = (value) => currency + Number(value || 0).toLocaleString('en-IN', {
                maximumFractionDigits: 2,
            });

            const selectedPrice = (name) => {
                const checked = form.querySelector(`input[name="${name}"]:checked`);
                return checked ? parseFloat(checked.dataset.price || '0') : 0;
            };

            const numberValue = (selector, fallback = 1) => {
                const input = form.querySelector(selector);
                const value = input ? parseInt(input.value || '0', 10) : fallback;
                return Number.isFinite(value) ? value : fallback;
            };

            const nights = () => {
                const inn = form.querySelector('#check_in');
                const out = form.querySelector('#check_out');
                if (!inn || !out || !inn.value || !out.value) return 0;
                const diff = (new Date(out.value) - new Date(inn.value)) / 86400000;
                return Math.max(0, Math.round(diff));
            };

            const update = () => {
                let rate = fallbackUnit;
                let quantity = 1;
                let rateLabel = 'Rate';
                let qtyLabel = 'Quantity';

                if (type === 'hotel') {
                    rate = selectedPrice('room_id');
                    quantity = Math.max(1, numberValue('#rooms')) * Math.max(1, nights());
                    rateLabel = 'Room rate × nights';
                    qtyLabel = 'Rooms × nights';
                } else if (type === 'event') {
                    rate = selectedPrice('ticket_type_id');
                    quantity = Math.max(1, numberValue('#quantity'));
                    rateLabel = 'Ticket price';
                    qtyLabel = 'Tickets';
                } else if (type === 'adventure') {
                    quantity = form.querySelectorAll('[data-rows="participant"] [data-row]').length;
                    rateLabel = 'Price per person';
                    qtyLabel = 'Participants';
                } else if (type === 'darshan') {
                    quantity = form.querySelectorAll('[data-rows="visitor"] [data-row]').length;
                    rateLabel = 'Price per visitor';
                    qtyLabel = 'Visitors';
                } else if (type === 'pooja') {
                    quantity = Math.max(1, numberValue('#participants'));
                    rateLabel = 'Price per participant';
                    qtyLabel = 'Participants';
                } else if (type === 'restaurant') {
                    quantity = Math.max(1, numberValue('#guests'));
                    rateLabel = 'Average spend per guest';
                    qtyLabel = 'Guests';
                } else if (type === 'food') {
                    quantity = Math.max(1, numberValue('#guests'));
                    rateLabel = 'Price per guest';
                    qtyLabel = 'Guests';
                }

                const subtotal = Math.max(0, rate * quantity);
                const tax = subtotal * (taxPercent / 100);
                const total = subtotal + tax;

                form.querySelector('[data-summary-rate]').textContent = money(rate) + (type === 'hotel' ? ' / night' : '');
                form.querySelector('[data-summary-label]').textContent = rateLabel;
                form.querySelector('[data-summary-qty-label]').textContent = qtyLabel;
                form.querySelector('[data-summary-qty]').textContent = quantity || '—';
                form.querySelector('[data-summary-subtotal]').textContent = money(subtotal);
                form.querySelector('[data-summary-tax]').textContent = money(tax);
                form.querySelector('[data-summary-total]').textContent = money(total);
            };

            form.addEventListener('change', update);
            form.addEventListener('input', update);

            form.querySelectorAll('[data-add-row]').forEach((button) => {
                button.addEventListener('click', () => {
                    const kind = button.dataset.addRow;
                    const container = form.querySelector(`[data-rows="${kind}"]`);
                    const template = document.getElementById(`${kind}-row-template`);
                    if (!container || !template) return;

                    const index = Date.now();
                    const html = template.innerHTML.replaceAll('__INDEX__', String(index));
                    container.insertAdjacentHTML('beforeend', html);
                    update();
                });
            });

            form.addEventListener('click', (event) => {
                const remove = event.target.closest('[data-remove-row]');
                if (!remove) return;

                const rows = remove.closest('[data-rows]');
                if (rows && rows.querySelectorAll('[data-row]').length <= 1) return;

                remove.closest('[data-row]').remove();
                update();
            });

            const checkIn = form.querySelector('#check_in');
            const checkOut = form.querySelector('#check_out');
            if (checkIn && checkOut) {
                checkIn.addEventListener('change', () => {
                    if (checkOut.value && checkOut.value <= checkIn.value) {
                        const next = new Date(checkIn.value);
                        next.setDate(next.getDate() + 1);
                        checkOut.value = next.toISOString().slice(0, 10);
                    }
                    checkOut.min = checkIn.value;
                    update();
                });
            }

            update();
        });
    </script>
@endpush
