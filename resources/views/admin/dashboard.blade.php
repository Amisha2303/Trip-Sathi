@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Admin dashboard')

@section('content')
    <div class="row g-3 mb-4">
        @foreach ($kpis as $kpi)
            <div class="col-6 col-xl-3">
                <div class="ts-kpi">
                    <span class="ts-kpi-icon"><i class="bi {{ $kpi['icon'] }}"></i></span>
                    <div>
                        <div class="ts-kpi-label">{{ $kpi['label'] }}</div>
                        <div class="ts-kpi-value">{{ $kpi['value'] }}</div>
                        <div class="small text-muted">{{ $kpi['note'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="ts-card">
                <div class="ts-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="ts-title" style="font-size: 1.1rem;">Booking status</h2>
                        <span class="small text-muted">All time</span>
                    </div>

                    @if ($statusBreakdown->isEmpty())
                        <p class="text-muted mb-0">No bookings have been created yet.</p>
                    @else
                        <div class="ts-table-card">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th class="text-end">Bookings</th>
                                            <th class="text-end">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($statusBreakdown as $row)
                                            <tr>
                                                <td>
                                                    <span class="ts-badge ts-badge-{{ $row->status->value }}">
                                                        {{ $statusLabels[$row->status->value] ?? $row->status->label() }}
                                                    </span>
                                                </td>
                                                <td class="text-end">{{ $row->total }}</td>
                                                <td class="text-end">{{ \App\Support\Money::format((int) $row->amount) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="ts-card h-100">
                <div class="ts-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="ts-title" style="font-size: 1.1rem;">Top destinations</h2>
                        <span class="small text-muted">By bookings</span>
                    </div>

                    @if ($topDestinations->isEmpty())
                        <p class="text-muted mb-0">Catalogue is still empty.</p>
                    @else
                        <ul class="ts-list">
                            @foreach ($topDestinations as $destination)
                                <li>
                                    <img class="ts-list-thumb" src="{{ $destination->image_url }}"
                                        alt="{{ $destination->name }}">
                                    <div class="flex-grow-1">
                                        <p class="ts-list-title">{{ $destination->name }}</p>
                                        <p class="ts-list-meta">
                                            {{ $destination->bookings_count }} booking{{ $destination->bookings_count === 1 ? '' : 's' }}
                                            · {{ $destination->hotels_count }} stay{{ $destination->hotels_count === 1 ? '' : 's' }}
                                        </p>
                                    </div>
                                    <span class="ts-badge ts-badge-plain ts-badge-draft">
                                        {{ $destination->popularity }} pts
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="ts-card">
                <div class="ts-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="ts-title" style="font-size: 1.1rem;">Recent bookings</h2>
                    </div>

                    @if ($recentBookings->isEmpty())
                        <p class="text-muted mb-0">Nothing booked yet.</p>
                    @else
                        <div class="ts-table-card">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Booking</th>
                                            <th>Customer</th>
                                            <th>Experience</th>
                                            <th class="text-end">Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentBookings as $booking)
                                            <tr>
                                                <td>
                                                    <strong class="text-dark">{{ $booking->booking_number }}</strong>
                                                    <div class="small text-muted">{{ $booking->booking_date->format('d M Y') }}</div>
                                                </td>
                                                <td>
                                                    {{ $booking->customer_name }}
                                                    <div class="small text-muted">{{ $booking->customer_email }}</div>
                                                </td>
                                                <td>
                                                    {{ $booking->type_label }}
                                                    <div class="small text-muted">
                                                        {{ $booking->destination?->name ?? '—' }}
                                                    </div>
                                                </td>
                                                <td class="text-end">{{ \App\Support\Money::format((int) $booking->final_amount) }}</td>
                                                <td>
                                                    <span class="ts-badge ts-badge-{{ $booking->status->value }}">
                                                        {{ $booking->status_label }}
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
        </div>

        <div class="col-lg-5">
            <div class="ts-card mb-4">
                <div class="ts-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="ts-title" style="font-size: 1.1rem;">Contact inbox</h2>
                        <span class="ts-badge ts-badge-pending">{{ $unreadMessages }} unread</span>
                    </div>

                    @if ($pendingMessages->isEmpty())
                        <p class="text-muted mb-0">No unread messages.</p>
                    @else
                        <ul class="ts-list">
                            @foreach ($pendingMessages as $message)
                                <li>
                                    <i class="bi bi-envelope-exclamation text-warning"></i>
                                    <div>
                                        <p class="ts-list-title">{{ $message->subject ?: 'General enquiry' }}</p>
                                        <p class="ts-list-meta">
                                            {{ $message->name }} · {{ $message->email }} ·
                                            {{ $message->created_at->diffForHumans() }}
                                        </p>
                                        <p class="ts-list-meta fst-italic">
                                            "{{ \Illuminate\Support\Str::limit($message->message, 90) }}"
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="ts-card">
                <div class="ts-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="ts-title" style="font-size: 1.1rem;">Newest accounts</h2>
                    </div>

                    @if ($recentUsers->isEmpty())
                        <p class="text-muted mb-0">No accounts yet.</p>
                    @else
                        <ul class="ts-list">
                            @foreach ($recentUsers as $user)
                                <li>
                                    <span class="ts-list-thumb d-inline-flex align-items-center justify-content-center fw-bold text-primary">
                                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                                    </span>
                                    <div class="flex-grow-1">
                                        <p class="ts-list-title">{{ $user->name }}</p>
                                        <p class="ts-list-meta">{{ $user->email }}</p>
                                    </div>
                                    <span class="ts-badge ts-badge-{{ $user->is_admin ? 'confirmed' : 'draft' }}">
                                        {{ $user->is_admin ? 'Admin' : 'Traveller' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
