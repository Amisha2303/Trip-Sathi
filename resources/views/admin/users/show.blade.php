@extends('layouts.admin')

@section('title', $user->name)
@section('heading', 'User · ' . $user->name)

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="ts-card mb-4">
                <div class="ts-card-body text-center">
                    <span class="ts-list-thumb d-inline-flex align-items-center justify-content-center fw-bold text-primary mb-3"
                        style="width: 64px; height: 64px; font-size: 1.6rem;">
                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                    </span>
                    <h2 class="ts-title mb-1" style="font-size: 1.2rem;">{{ $user->name }}</h2>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <span class="ts-badge ts-badge-{{ $user->is_admin ? 'confirmed' : 'draft' }}">{{ $user->is_admin ? 'Admin' : 'Traveller' }}</span>
                        <span class="ts-badge ts-badge-{{ $user->is_active ? 'confirmed' : 'cancelled' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                </div>
            </div>

            <div class="ts-card">
                <div class="ts-card-body">
                    <div class="ts-card-title mb-3">Booking stats</div>
                    <div class="row g-3 text-center">
                        <div class="col-6"><div class="ts-kpi-value">{{ $stats['total'] }}</div><div class="small text-muted">Total</div></div>
                        <div class="col-6"><div class="ts-kpi-value">{{ $stats['upcoming'] }}</div><div class="small text-muted">Upcoming</div></div>
                        <div class="col-6"><div class="ts-kpi-value">{{ $stats['completed'] }}</div><div class="small text-muted">Completed</div></div>
                        <div class="col-6"><div class="ts-kpi-value">{{ $stats['cancelled'] }}</div><div class="small text-muted">Cancelled</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="ts-card mb-4">
                @csrf @method('PUT')
                <div class="ts-card-body">
                    <div class="ts-card-title mb-3">Account details</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Name</label>
                            <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="phone" class="form-label fw-semibold">Phone</label>
                            <input id="phone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="city" class="form-label fw-semibold">City</label>
                            <input id="city" name="city" class="form-control" value="{{ old('city', $user->city) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="state" class="form-label fw-semibold">State</label>
                            <input id="state" name="state" class="form-control" value="{{ old('state', $user->state) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="country" class="form-label fw-semibold">Country</label>
                            <input id="country" name="country" class="form-control" value="{{ old('country', $user->country) }}">
                        </div>

                        @unless ($user->is(auth()->user()))
                            <div class="col-12">
                                <div class="form-check form-switch mb-1">
                                    <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" value="1" @checked(old('is_admin', $user->is_admin))>
                                    <label class="form-check-label" for="is_admin">Admin access</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $user->is_active))>
                                    <label class="form-check-label" for="is_active">Account active</label>
                                </div>
                                <div class="form-text">Unchecking active signs the user out everywhere.</div>
                            </div>
                        @else
                            <div class="col-12"><div class="alert alert-info mb-0 small">You cannot change your own role or status here.</div></div>
                        @endunless

                        <div class="col-12">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg"></i> Save changes</button>
                            <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Back to list</a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="ts-card">
                <div class="ts-card-body">
                    <div class="ts-card-title mb-3">Recent bookings</div>
                    @if ($user->bookings->isEmpty())
                        <p class="text-muted mb-0">No bookings yet.</p>
                    @else
                        <div class="ts-table-card">
                            <div class="table-responsive">
                                <table class="table mb-0 align-middle">
                                    <thead><tr><th>Booking</th><th>Experience</th><th class="text-end">Amount</th><th>Status</th></tr></thead>
                                    <tbody>
                                        @foreach ($user->bookings as $booking)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="fw-semibold text-dark">{{ $booking->booking_number }}</a>
                                                    <div class="small text-muted">{{ $booking->booking_date?->format('d M Y') }}</div>
                                                </td>
                                                <td>
                                                    {{ $booking->type_label }}
                                                    <div class="small text-muted">{{ $booking->destination?->name ?? '—' }}</div>
                                                </td>
                                                <td class="text-end">{{ \App\Support\Money::format((int) $booking->final_amount) }}</td>
                                                <td><span class="ts-badge ts-badge-{{ $booking->status->value }}">{{ $booking->status_label }}</span></td>
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
    </div>
@endsection
