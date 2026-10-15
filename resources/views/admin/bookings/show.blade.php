@extends('layouts.admin')

@section('title', $booking->booking_number)
@section('heading', 'Booking · ' . $booking->booking_number)

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ts-card mb-4">
                <div class="ts-card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h2 class="ts-title mb-1" style="font-size: 1.2rem;">{{ $booking->title }}</h2>
                            <p class="text-muted mb-0">{{ $booking->type_label }} · {{ $booking->destination?->name ?? '—' }}</p>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="ts-badge ts-badge-{{ $booking->status->value }}">{{ $booking->status_label }}</span>
                            <span class="ts-badge ts-badge-{{ $booking->payment_status->value }}">{{ $booking->payment_status->label() }}</span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-3">
                            <div class="small text-muted">Booking date</div>
                            <div class="fw-semibold">{{ $booking->booking_date?->format('d M Y') }}</div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="small text-muted">Guests</div>
                            <div class="fw-semibold">{{ $booking->guests }}</div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="small text-muted">Quantity</div>
                            <div class="fw-semibold">{{ $booking->quantity }}</div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="small text-muted">Source</div>
                            <div class="fw-semibold">{{ ucfirst($booking->source) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ts-card mb-4">
                <div class="ts-card-body">
                    <div class="ts-card-title mb-3">Customer</div>
                    <div class="row g-3">
                        <div class="col-sm-4"><div class="small text-muted">Name</div><div class="fw-semibold">{{ $booking->customer_name }}</div></div>
                        <div class="col-sm-4"><div class="small text-muted">Email</div><div class="fw-semibold">{{ $booking->customer_email }}</div></div>
                        <div class="col-sm-4"><div class="small text-muted">Phone</div><div class="fw-semibold">{{ $booking->customer_phone ?: '—' }}</div></div>
                        @if ($booking->user)
                            <div class="col-12">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.users.show', $booking->user) }}">
                                    <i class="bi bi-person"></i> View account ({{ $booking->user->name }})
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if ($booking->items->isNotEmpty())
                <div class="ts-card mb-4">
                    <div class="ts-card-body">
                        <div class="ts-card-title mb-3">Line items</div>
                        <div class="ts-table-card">
                            <div class="table-responsive">
                                <table class="table mb-0 align-middle">
                                    <thead><tr><th>Item</th><th>Date</th><th class="text-end">Qty</th><th class="text-end">Total</th></tr></thead>
                                    <tbody>
                                        @foreach ($booking->items as $item)
                                            <tr>
                                                <td>
                                                    {{ $item->name }}
                                                    @if ($item->description)<div class="small text-muted">{{ \Illuminate\Support\Str::limit($item->description, 70) }}</div>@endif
                                                </td>
                                                <td>{{ $item->service_date?->format('d M Y') ?: '—' }}</td>
                                                <td class="text-end">{{ $item->quantity }}</td>
                                                <td class="text-end">{{ \App\Support\Money::format((int) $item->total_amount) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($booking->payments->isNotEmpty())
                <div class="ts-card">
                    <div class="ts-card-body">
                        <div class="ts-card-title mb-3">Payments</div>
                        <div class="ts-table-card">
                            <div class="table-responsive">
                                <table class="table mb-0 align-middle">
                                    <thead><tr><th>Reference</th><th>Gateway</th><th class="text-end">Amount</th><th>Status</th><th>Paid</th><th></th></tr></thead>
                                    <tbody>
                                        @foreach ($booking->payments as $payment)
                                            <tr>
                                                <td>{{ $payment->transaction_reference ?: '—' }}</td>
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
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <form method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="ts-card mb-4">
                @csrf @method('PUT')
                <div class="ts-card-body">
                    <div class="ts-card-title mb-3">Update booking</div>

                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Booking status</label>
                        <select id="status" name="status" class="form-select">
                            @foreach ($transitions as $key => $label)
                                <option value="{{ $key }}" @selected(old('status', $booking->status->value) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Only valid next steps are listed.</div>
                    </div>

                    <div class="mb-3">
                        <label for="payment_status" class="form-label fw-semibold">Payment status</label>
                        <select id="payment_status" name="payment_status" class="form-select">
                            @foreach ($paymentStatuses as $key => $label)
                                <option value="{{ $key }}" @selected(old('payment_status', $booking->payment_status->value) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="cancel-group">
                        <label for="cancellation_reason" class="form-label fw-semibold">Cancellation reason</label>
                        <textarea id="cancellation_reason" name="cancellation_reason" rows="2"
                            class="form-control">{{ old('cancellation_reason', $booking->cancellation_reason) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="admin_notes" class="form-label fw-semibold">Admin notes</label>
                        <textarea id="admin_notes" name="admin_notes" rows="3"
                            class="form-control">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                    </div>

                    <button class="btn btn-primary w-100" type="submit"><i class="bi bi-check-lg"></i> Save changes</button>
                </div>
            </form>

            <div class="ts-card mb-4">
                <div class="ts-card-body">
                    <div class="ts-card-title mb-3">Amounts</div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-1"><span class="text-muted">Subtotal</span><span>{{ \App\Support\Money::format((int) $booking->subtotal) }}</span></li>
                        <li class="d-flex justify-content-between py-1"><span class="text-muted">Discount</span><span>-{{ \App\Support\Money::format((int) $booking->discount) }}</span></li>
                        <li class="d-flex justify-content-between py-1"><span class="text-muted">Tax</span><span>{{ \App\Support\Money::format((int) $booking->tax) }}</span></li>
                        <li class="d-flex justify-content-between py-1 border-top mt-1 pt-2 fw-bold"><span>Final</span><span>{{ \App\Support\Money::format((int) $booking->final_amount) }}</span></li>
                        @if ($booking->refunded_amount)
                            <li class="d-flex justify-content-between py-1"><span class="text-muted">Refunded</span><span>{{ \App\Support\Money::format((int) $booking->refunded_amount) }}</span></li>
                        @endif
                    </ul>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}"
                onsubmit="return confirm('Soft-delete this booking? It stays recoverable in the database.')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger w-100" type="submit"><i class="bi bi-trash"></i> Delete booking</button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const status = document.getElementById('status');
                const group = document.getElementById('cancel-group');
                const toggle = () => group.classList.toggle('d-none', status.value !== 'cancelled');
                status.addEventListener('change', toggle);
                toggle();
            })();
        </script>
    @endpush
@endsection
