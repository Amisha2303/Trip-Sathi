@extends('layouts.admin')

@section('title', 'Bookings')
@section('heading', 'All bookings')

@section('content')
    <div class="ts-toolbar">
        <form class="d-flex flex-wrap align-items-center gap-2 flex-grow-1" method="get" action="{{ route('admin.bookings.index') }}">
            <input class="form-control form-control-sm" style="max-width: 200px;" type="search" name="q"
                value="{{ request('q') }}" placeholder="Number, name or email…">
            <select class="form-select form-select-sm" style="max-width: 150px;" name="status">
                <option value="">Any status</option>
                @foreach ($statuses as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select class="form-select form-select-sm" style="max-width: 150px;" name="payment_status">
                <option value="">Any payment</option>
                @foreach ($paymentStatuses as $key => $label)
                    <option value="{{ $key }}" @selected(request('payment_status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select class="form-select form-select-sm" style="max-width: 150px;" name="type">
                <option value="">Any type</option>
                @foreach ($types as $key => $label)
                    <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <input class="form-control form-control-sm" style="max-width: 150px;" type="date" name="from" value="{{ request('from') }}" title="From date">
            <input class="form-control form-control-sm" style="max-width: 150px;" type="date" name="to" value="{{ request('to') }}" title="To date">
            <button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
        </form>

        <p class="ts-result-count mb-0">{{ $bookings->total() }} bookings</p>
    </div>

    @if ($statusTotals->isNotEmpty())
        <div class="d-flex flex-wrap gap-2 mb-3">
            @foreach ($statusTotals as $status => $total)
                <a class="ts-badge ts-badge-{{ $status }} text-decoration-none"
                    href="{{ route('admin.bookings.index', ['status' => $status]) }}">{{ $statuses[$status] ?? ucfirst($status) }} · {{ $total }}</a>
            @endforeach
        </div>
    @endif

    <div class="ts-table-card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Booking</th>
                        <th>Customer</th>
                        <th>Experience</th>
                        <th class="text-end">Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td>
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="fw-semibold text-dark">{{ $booking->booking_number }}</a>
                                <div class="small text-muted">{{ $booking->booking_date?->format('d M Y') }}</div>
                            </td>
                            <td>
                                {{ $booking->customer_name }}
                                <div class="small text-muted">{{ $booking->customer_email }}</div>
                            </td>
                            <td>
                                {{ $booking->type_label }}
                                <div class="small text-muted">{{ $booking->destination?->name ?? '—' }}</div>
                            </td>
                            <td class="text-end">{{ \App\Support\Money::format((int) $booking->final_amount) }}</td>
                            <td><span class="ts-badge ts-badge-{{ $booking->payment_status->value }}">{{ $booking->payment_status->label() }}</span></td>
                            <td><span class="ts-badge ts-badge-{{ $booking->status->value }}">{{ $booking->status_label }}</span></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.bookings.show', $booking) }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No bookings match this filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $bookings->links() }}</div>
@endsection
