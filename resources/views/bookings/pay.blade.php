@extends('layouts.app')

@section('title', 'Pay for ' . $booking->booking_number . ' — Trip Sathi')

@section('content')
    <section class="ts-booking-head">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('bookings.index') }}">My bookings</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('bookings.show', $booking) }}">{{ $booking->booking_number }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Payment</li>
                </ol>
            </nav>

            <span class="ts-eyebrow">Almost there</span>
            <h1 class="ts-title mb-2">Complete your payment</h1>
            <p class="text-muted mb-0">Booking {{ $booking->booking_number }} is held while you pay.</p>
        </div>
    </section>

    <section class="ts-section ts-section-tight">
        <div class="container">
            @if (session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success" role="alert">{{ session('success') }}</div>
            @endif

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="ts-booking-card mb-4">
                        <div class="ts-booking-card-head">
                            <span class="ts-booking-step"><i class="bi bi-credit-card"></i></span>
                            <div>
                                <h2 class="ts-title h5 mb-1">Pay {{ \App\Support\Money::format((int) $booking->final_amount) }}</h2>
                                <p class="text-muted small mb-0">
                                    {{ $gatewayName }}
                                    @if (! $isConfigured)
                                        · not configured yet
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if (! $isConfigured)
                            <div class="alert alert-warning">
                                Online card payments are not configured on this site yet. Your booking is safe and
                                our team can collect payment directly. Quote <strong>{{ $booking->booking_number }}</strong>
                                when you contact us.
                            </div>
                        @elseif ($isOnline)
                            <p class="text-muted">You will be redirected to {{ $gatewayName }} to pay securely. Your card details never touch our servers.</p>
                        @else
                            <p class="text-muted mb-3">
                                Pay offline by bank transfer or in person. Generate a payment reference below, then
                                share it with our team so we can mark the booking confirmed.
                            </p>
                        @endif

                        <form method="POST" action="{{ route('bookings.pay.initiate', $booking) }}">
                            @csrf
                            <button class="btn btn-accent btn-lg w-100" type="submit">
                                @if ($isOnline && $isConfigured)
                                    <i class="bi bi-lock-fill"></i> Pay securely with {{ $gatewayName }}
                                @elseif ($isOnline)
                                    <i class="bi bi-arrow-repeat"></i> Try {{ $gatewayName }} payment
                                @else
                                    <i class="bi bi-receipt"></i> Generate offline payment reference
                                @endif
                            </button>
                        </form>

                        @if ($latestPayment)
                            <div class="ts-booking-reference mt-4">
                                <div>
                                    <span class="small text-muted d-block">Payment reference</span>
                                    <strong>{{ $latestPayment->transaction_reference ?: 'Pending generation' }}</strong>
                                </div>
                                <span class="ts-badge ts-badge-{{ $latestPayment->status->value }}">
                                    {{ $latestPayment->status->label() }}
                                </span>
                            </div>
                        @endif

                        <div class="mt-4 small text-muted">
                            <p class="mb-1"><i class="bi bi-telephone me-1"></i> {{ $appSettings['contact_phone'] ?? '+91 9904530641' }}</p>
                            <p class="mb-0"><i class="bi bi-envelope me-1"></i> {{ $appSettings['contact_email'] ?? 'hello@tripsathi.com' }}</p>
                        </div>
                    </div>

                    <div class="ts-booking-card">
                        <div class="ts-booking-card-head">
                            <span class="ts-booking-step"><i class="bi bi-2-circle"></i></span>
                            <div>
                                <h2 class="ts-title h5 mb-1">Need to change something?</h2>
                                <p class="text-muted small mb-0">Contact us before paying and we will adjust the booking.</p>
                            </div>
                        </div>
                        <a class="btn btn-soft" href="{{ route('contact', ['subject' => 'Booking ' . $booking->booking_number]) }}">
                            <i class="bi bi-chat-dots"></i> Message support
                        </a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <aside class="ts-booking-summary">
                        <div class="ts-booking-card">
                            <div class="p-3 p-lg-4">
                                <span class="ts-eyebrow">{{ $booking->type_label }}</span>
                                <h2 class="ts-title h5 mb-1">{{ $booking->title }}</h2>
                                <p class="text-muted small mb-3">
                                    {{ $booking->booking_date?->format('D, d M Y') }}
                                    @if ($booking->destination) · {{ $booking->destination->name }} @endif
                                </p>

                                <dl class="ts-booking-lines">
                                    @foreach ($booking->items as $item)
                                        <div>
                                            <dt>{{ $item->name }} <span class="text-muted">× {{ $item->quantity }}</span></dt>
                                            <dd>{{ \App\Support\Money::format((int) $item->total_amount) }}</dd>
                                        </div>
                                    @endforeach
                                    <div>
                                        <dt>Subtotal</dt>
                                        <dd>{{ \App\Support\Money::format((int) $booking->subtotal) }}</dd>
                                    </div>
                                    @if ((int) $booking->discount > 0)
                                        <div>
                                            <dt>Discount</dt>
                                            <dd>-{{ \App\Support\Money::format((int) $booking->discount) }}</dd>
                                        </div>
                                    @endif
                                    <div>
                                        <dt>Taxes &amp; fees</dt>
                                        <dd>{{ \App\Support\Money::format((int) $booking->tax) }}</dd>
                                    </div>
                                    <div class="ts-booking-total">
                                        <dt>Total due</dt>
                                        <dd>{{ \App\Support\Money::format((int) $booking->final_amount) }}</dd>
                                    </div>
                                </dl>

                                <ul class="ts-booking-trust">
                                    <li><i class="bi bi-shield-check"></i> Your booking is held until payment</li>
                                    <li><i class="bi bi-envelope-check"></i> Receipt emailed after confirmation</li>
                                    <li><i class="bi bi-arrow-counterclockwise"></i> Clear cancellation terms</li>
                                </ul>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
