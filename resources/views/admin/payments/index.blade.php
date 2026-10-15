@extends('layouts.admin')

@section('title', 'Payments')
@section('heading', 'Payments')

@section('content')
    <div class="ts-toolbar">
        <form class="d-flex flex-wrap align-items-center gap-2 flex-grow-1" method="get" action="{{ route('admin.payments.index') }}">
            <input class="form-control form-control-sm" style="max-width: 220px;" type="search" name="q"
                value="{{ request('q') }}" placeholder="Reference or booking number…">
            <select class="form-select form-select-sm" style="max-width: 150px;" name="status">
                <option value="">Any status</option>
                @foreach ($statuses as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select class="form-select form-select-sm" style="max-width: 140px;" name="gateway">
                <option value="">Any gateway</option>
                @foreach ($gateways as $key => $label)
                    <option value="{{ $key }}" @selected(request('gateway') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
        </form>

        <p class="ts-result-count mb-0">{{ $payments->total() }} payments</p>
    </div>

    @if ($totals->isNotEmpty())
        <div class="row g-3 mb-4">
            @foreach ($totals as $status => $row)
                <div class="col-6 col-xl-2">
                    <div class="ts-stat">
                        <div class="ts-stat-label">{{ $statuses[$status] ?? ucfirst($status) }}</div>
                        <div class="ts-stat-value">{{ \App\Support\Money::format((int) $row->amount) }}</div>
                        <div class="ts-stat-note">{{ $row->total }} payment(s)</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="ts-table-card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Booking</th>
                        <th>Gateway</th>
                        <th class="text-end">Amount</th>
                        <th>Status</th>
                        <th>Paid at</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td><span class="fw-semibold text-dark">{{ $payment->transaction_reference ?: '—' }}</span></td>
                            <td>
                                @if ($payment->booking)
                                    <a href="{{ route('admin.bookings.show', $payment->booking) }}" class="fw-semibold text-dark">{{ $payment->booking->booking_number }}</a>
                                    <div class="small text-muted">{{ $payment->booking->customer_name }}</div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($payment->gateway) }}</td>
                            <td class="text-end">{{ \App\Support\Money::format((int) $payment->amount) }}</td>
                            <td><span class="ts-badge ts-badge-{{ $payment->status->value }}">{{ $payment->status->label() }}</span></td>
                            <td>{{ $payment->paid_at?->format('d M Y') ?: '—' }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.payments.show', $payment) }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No payments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $payments->links() }}</div>
@endsection
