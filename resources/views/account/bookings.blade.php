@extends('layouts.account')

@section('title', 'My Bookings — Trip Sathi')

@section('account-content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1 class="ts-title" style="font-size: 1.4rem;">My bookings</h1>
            <p class="ts-subtitle">Every stay, ticket, darshan and activity you have booked.</p>
        </div>
        <a class="btn btn-accent btn-sm" href="{{ route('destinations.index') }}">
            <i class="bi bi-plus-lg"></i> Book something new
        </a>
    </div>

    <div class="ts-tabs">
        <a href="{{ route('bookings.index') }}" class="{{ $status === null ? 'is-active' : '' }}">
            All <span class="badge text-bg-light ms-1">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('bookings.index', ['status' => 'upcoming']) }}" class="{{ $status === 'upcoming' ? 'is-active' : '' }}">
            Upcoming <span class="badge text-bg-light ms-1">{{ $counts['upcoming'] }}</span>
        </a>
        <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="{{ $status === 'pending' ? 'is-active' : '' }}">
            Pending <span class="badge text-bg-light ms-1">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('bookings.index', ['status' => 'completed']) }}" class="{{ $status === 'completed' ? 'is-active' : '' }}">
            Completed <span class="badge text-bg-light ms-1">{{ $counts['completed'] }}</span>
        </a>
        <a href="{{ route('bookings.index', ['status' => 'cancelled']) }}" class="{{ $status === 'cancelled' ? 'is-active' : '' }}">
            Cancelled <span class="badge text-bg-light ms-1">{{ $counts['cancelled'] }}</span>
        </a>
    </div>

    @if ($bookings->isEmpty())
        @include('partials.empty-state', [
            'icon' => 'bi-ticket-perforated',
            'title' => $status ? 'No bookings in this view' : 'No bookings yet',
            'text' => 'Book a hotel, event, adventure or darshan and it will appear here with its status and receipts.',
            'actionUrl' => route('hotels.index'),
            'actionLabel' => 'Browse stays',
        ])
    @else
        <div class="ts-table-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Experience</th>
                            <th>Date</th>
                            <th>Guests</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            <tr>
                                <td>
                                    <strong class="text-dark">{{ $booking->booking_number }}</strong>
                                    <div class="small text-muted">{{ $booking->type_label }}</div>
                                </td>
                                <td>
                                    {{ $booking->title }}
                                    @if ($booking->destination)
                                        <div class="small text-muted"><i class="bi bi-geo-alt"></i>
                                            {{ $booking->destination->name }}</div>
                                    @endif
                                </td>
                                <td>
                                    {{ $booking->booking_date->format('d M Y') }}
                                    @if ($booking->end_date && ! $booking->end_date->isSameDay($booking->booking_date))
                                        <div class="small text-muted">to {{ $booking->end_date->format('d M Y') }}</div>
                                    @endif
                                </td>
                                <td>{{ $booking->guests }}</td>
                                <td>{{ $booking->final_amount_rupees > 0 ? \App\Support\Money::decimal($booking->final_amount_rupees) : '—' }}</td>
                                <td>
                                    <span class="ts-badge ts-badge-{{ str_replace('_', '-', $booking->payment_status->value) }}">
                                        {{ $booking->payment_status->label() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="ts-badge ts-badge-{{ $booking->status->value }}">{{ $booking->status_label }}</span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-square btn-soft" data-bs-toggle="modal"
                                        data-bs-target="#bookingModal"
                                        data-booking-number="{{ $booking->booking_number }}"
                                        data-booking-title="{{ $booking->title }}"
                                        data-booking-type="{{ $booking->type_label }}"
                                        data-booking-date="{{ $booking->booking_date->format('d M Y') }}"
                                        data-booking-end="{{ $booking->end_date?->format('d M Y') ?? '—' }}"
                                        data-booking-guests="{{ $booking->guests }}"
                                        data-booking-amount="{{ \App\Support\Money::decimal($booking->final_amount_rupees) }}"
                                        data-booking-subtotal="{{ \App\Support\Money::decimal($booking->subtotal_rupees) }}"
                                        data-booking-discount="{{ \App\Support\Money::decimal($booking->discount_rupees) }}"
                                        data-booking-tax="{{ \App\Support\Money::decimal($booking->tax_rupees) }}"
                                        data-booking-status="{{ $booking->status_label }}"
                                        data-booking-payment="{{ $booking->payment_status->label() }}"
                                        data-booking-destination="{{ $booking->destination?->name ?? '—' }}"
                                        aria-label="Booking details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($bookings->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $bookings->links() }}
            </div>
        @endif
    @endif

    <div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" data-booking-field="title">Booking</h5>
                        <span class="small text-muted" data-booking-field="number"></span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="ts-list">
                        <li>
                            <i class="bi bi-collection text-primary"></i>
                            <div>
                                <p class="ts-list-title" data-booking-field="type"></p>
                                <p class="ts-list-meta" data-booking-field="destination"></p>
                            </div>
                        </li>
                        <li>
                            <i class="bi bi-calendar3 text-primary"></i>
                            <div>
                                <p class="ts-list-title"><span data-booking-field="date"></span> → <span
                                        data-booking-field="end"></span></p>
                                <p class="ts-list-meta"><span data-booking-field="guests"></span> guest(s)</p>
                            </div>
                        </li>
                        <li>
                            <i class="bi bi-credit-card text-primary"></i>
                            <div>
                                <p class="ts-list-title" data-booking-field="amount"></p>
                                <p class="ts-list-meta">
                                    Subtotal <span data-booking-field="subtotal"></span> ·
                                    Discount <span data-booking-field="discount"></span> ·
                                    Tax <span data-booking-field="tax"></span>
                                </p>
                            </div>
                        </li>
                        <li>
                            <i class="bi bi-flag text-primary"></i>
                            <div>
                                <p class="ts-list-title" data-booking-field="status"></p>
                                <p class="ts-list-meta">Payment: <span data-booking-field="payment"></span></p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-soft" href="{{ route('contact') }}">
                        <i class="bi bi-life-preserver"></i> Need help?
                    </a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('bookingModal');
            if (!modal) return;

            modal.addEventListener('show.bs.modal', (event) => {
                const trigger = event.relatedTarget;
                if (!trigger) return;

                modal.querySelectorAll('[data-booking-field]').forEach((node => {
                    const key = node.dataset.bookingField;
                    const value = trigger.dataset['booking' + key.charAt(0).toUpperCase() + key.slice(1)];
                    if (value !== undefined) node.textContent = value;
                }));
            });
        });
    </script>
@endpush
