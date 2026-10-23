@extends('layouts.app')

@section('title', 'Booking ' . $booking->booking_number . ' — Trip Sathi')

@section('content')
    <section class="ts-booking-head">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('bookings.index') }}">My bookings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $booking->booking_number }}</li>
                </ol>
            </nav>

            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                <div>
                    <span class="ts-eyebrow">Booking</span>
                    <h1 class="ts-title mb-2">{{ $booking->title }}</h1>
                    <p class="text-muted mb-0">{{ $booking->booking_number }} · {{ $booking->type_label }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="ts-badge ts-badge-{{ $booking->status->value }}">{{ $booking->status_label }}</span>
                    <span class="ts-badge ts-badge-{{ $booking->payment_status->value }}">{{ $booking->payment_status->label() }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="ts-section ts-section-tight">
        <div class="container">
            @if (! $booking->payment_status->isSettled())
                <div class="ts-booking-alert">
                    <div>
                        <strong>Payment due · {{ \App\Support\Money::format((int) $booking->final_amount) }}</strong>
                        <p class="mb-0 small">Complete payment to confirm this booking. We will hold it while you pay.</p>
                    </div>
                    <a class="btn btn-accent" href="{{ route('bookings.pay', $booking) }}">
                        Pay now <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            @elseif ($booking->status === \App\Enums\BookingStatus::Confirmed)
                <div class="ts-booking-alert is-success">
                    <div>
                        <strong>You are confirmed</strong>
                        <p class="mb-0 small">A receipt has been emailed to {{ $booking->customer_email }}.</p>
                    </div>
                    <a class="btn btn-soft" href="{{ route('bookings.invoice', $booking) }}" target="_blank" rel="noopener">
                        <i class="bi bi-receipt"></i> View receipt
                    </a>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="ts-booking-card mb-4">
                        <div class="ts-booking-card-head">
                            <span class="ts-booking-step"><i class="bi bi-calendar-event"></i></span>
                            <div>
                                <h2 class="ts-title h5 mb-1">Booking details</h2>
                                <p class="text-muted small mb-0">Everything you booked, in one place.</p>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6 col-lg-3">
                                <div class="small text-muted">Date</div>
                                <div class="fw-semibold">{{ $booking->booking_date?->format('d M Y') }}</div>
                            </div>
                            @if ($booking->end_date)
                                <div class="col-sm-6 col-lg-3">
                                    <div class="small text-muted">Until</div>
                                    <div class="fw-semibold">{{ $booking->end_date->format('d M Y') }}</div>
                                </div>
                            @endif
                            <div class="col-sm-6 col-lg-3">
                                <div class="small text-muted">Guests</div>
                                <div class="fw-semibold">{{ $booking->guests }}</div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="small text-muted">Destination</div>
                                <div class="fw-semibold">{{ $booking->destination?->name ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="ts-booking-items">
                            @foreach ($booking->items as $item)
                                @php
                                    $image = $item->image;
                                    $imageSrc = $image
                                        ? (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])
                                            ? $image
                                            : asset('storage/' . ltrim($image, '/')))
                                        : asset('images/placeholder.svg');
                                @endphp
                                <div class="ts-booking-item">
                                    <img src="{{ $imageSrc }}" alt="{{ $item->name }}"
                                        onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}';">
                                    <div>
                                        <strong>{{ $item->name }}</strong>
                                        <div class="small text-muted">
                                            {{ $item->service_date?->format('D, d M Y') }}
                                            @if ($item->time_range) · {{ $item->time_range }} @endif
                                            @if ($item->quantity > 1) · {{ $item->quantity }} @endif
                                        </div>
                                        @php
                                            $summary = collect($item->options ?? [])
                                                ->filter(fn ($value) => is_scalar($value) && filled($value))
                                                ->map(fn ($value, $key) => ucwords(str_replace('_', ' ', (string) $key)) . ': ' . $value)
                                                ->take(3)
                                                ->implode(' · ');
                                        @endphp
                                        @if ($summary)
                                            <div class="small text-muted mt-1">{{ $summary }}</div>
                                        @endif
                                    </div>
                                    <div class="ts-booking-item-price">
                                        {{ \App\Support\Money::format((int) $item->total_amount) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($booking->notes)
                            <div class="ts-booking-note">
                                <strong>Your note</strong>
                                <p class="mb-0 small">{{ $booking->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="ts-booking-card">
                        <div class="ts-booking-card-head">
                            <span class="ts-booking-step"><i class="bi bi-wallet2"></i></span>
                            <div>
                                <h2 class="ts-title h5 mb-1">Payments</h2>
                                <p class="text-muted small mb-0">Every attempt is recorded for support and refunds.</p>
                            </div>
                        </div>

                        @if ($booking->payments->isEmpty())
                            <p class="text-muted mb-0">No payment attempts yet.</p>
                        @else
                            <div class="ts-table-card">
                                <div class="table-responsive">
                                    <table class="table mb-0 align-middle">
                                        <thead>
                                            <tr>
                                                <th>Reference</th>
                                                <th>Method</th>
                                                <th class="text-end">Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($booking->payments->sortByDesc('id') as $payment)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $payment->transaction_reference ?: '—' }}</strong>
                                                        <div class="small text-muted">{{ $payment->created_at?->format('d M Y, g:i A') }}</div>
                                                    </td>
                                                    <td>{{ $payment->method_label }}</td>
                                                    <td class="text-end">{{ \App\Support\Money::format((int) $payment->amount) }}</td>
                                                    <td>
                                                        <span class="ts-badge ts-badge-{{ $payment->status->value }}">
                                                            {{ $payment->status->label() }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    <aside class="ts-booking-summary">
                        <div class="ts-booking-card mb-4">
                            <div class="p-3 p-lg-4">
                                <span class="ts-eyebrow">Price summary</span>
                                <dl class="ts-booking-lines mt-3">
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
                                        <dt>Total</dt>
                                        <dd>{{ \App\Support\Money::format((int) $booking->final_amount) }}</dd>
                                    </div>
                                </dl>

                                @if (! $booking->payment_status->isSettled())
                                    <a class="btn btn-accent w-100 mt-3" href="{{ route('bookings.pay', $booking) }}">
                                        <i class="bi bi-credit-card"></i> Pay now
                                    </a>
                                @endif
                                <a class="btn btn-soft w-100 mt-2" href="{{ route('bookings.invoice', $booking) }}" target="_blank" rel="noopener">
                                    <i class="bi bi-download"></i> Download receipt
                                </a>
                                <a class="btn btn-outline-secondary w-100 mt-2" href="{{ route('contact', ['subject' => 'Booking ' . $booking->booking_number]) }}">
                                    <i class="bi bi-life-preserver"></i> Get help
                                </a>
                            </div>
                        </div>

                        <div class="ts-booking-card">
                            <div class="p-3 p-lg-4">
                                <span class="ts-eyebrow">Booked by</span>
                                <p class="fw-semibold mb-1 mt-2">{{ $booking->customer_name }}</p>
                                <p class="small text-muted mb-1">{{ $booking->customer_email }}</p>
                                <p class="small text-muted mb-0">{{ $booking->customer_phone }}</p>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
