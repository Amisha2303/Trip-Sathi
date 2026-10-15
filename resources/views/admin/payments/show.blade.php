@extends('layouts.admin')

@section('title', 'Payment ' . ($payment->transaction_reference ?: '#' . $payment->id))
@section('heading', 'Payment · ' . ($payment->transaction_reference ?: '#' . $payment->id))

@section('content')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ts-card mb-4">
                <div class="ts-card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h2 class="ts-title mb-1" style="font-size: 1.2rem;">{{ \App\Support\Money::format((int) $payment->amount) }}</h2>
                            <p class="text-muted mb-0">{{ ucfirst($payment->gateway) }} · {{ $payment->currency }}</p>
                        </div>
                        <span class="ts-badge ts-badge-{{ $payment->status->value }}">{{ $payment->status->label() }}</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-4"><div class="small text-muted">Reference</div><div class="fw-semibold">{{ $payment->transaction_reference ?: '—' }}</div></div>
                        <div class="col-sm-4"><div class="small text-muted">Paid at</div><div class="fw-semibold">{{ $payment->paid_at?->format('d M Y H:i') ?: '—' }}</div></div>
                        <div class="col-sm-4"><div class="small text-muted">Refunded</div><div class="fw-semibold">{{ \App\Support\Money::format((int) $payment->amount_refunded) }}</div></div>
                        @if ($payment->failure_reason)
                            <div class="col-12"><div class="small text-muted">Failure reason</div><div class="text-danger">{{ $payment->failure_reason }}</div></div>
                        @endif
                    </div>
                </div>
            </div>

            @if ($payment->transactions->isNotEmpty())
                <div class="ts-card mb-4">
                    <div class="ts-card-body">
                        <div class="ts-card-title mb-3">Gateway transactions</div>
                        <div class="ts-table-card">
                            <div class="table-responsive">
                                <table class="table mb-0 align-middle">
                                    <thead><tr><th>Reference</th><th>Type</th><th class="text-end">Amount</th><th>Status</th><th>At</th></tr></thead>
                                    <tbody>
                                        @foreach ($payment->transactions as $tx)
                                            <tr>
                                                <td>{{ $tx->reference ?: '—' }}</td>
                                                <td>{{ ucfirst($tx->type) }}</td>
                                                <td class="text-end">{{ \App\Support\Money::format((int) $tx->amount) }}</td>
                                                <td>{{ ucfirst($tx->status->value ?? (string) $tx->status) }}</td>
                                                <td>{{ $tx->created_at?->format('d M Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($payment->refunds->isNotEmpty())
                <div class="ts-card">
                    <div class="ts-card-body">
                        <div class="ts-card-title mb-3">Refunds</div>
                        <div class="ts-table-card">
                            <div class="table-responsive">
                                <table class="table mb-0 align-middle">
                                    <thead><tr><th>Reference</th><th class="text-end">Amount</th><th>Status</th><th>Reason</th><th>Processed</th></tr></thead>
                                    <tbody>
                                        @foreach ($payment->refunds as $refund)
                                            <tr>
                                                <td>{{ $refund->reference ?: '—' }}</td>
                                                <td class="text-end">{{ \App\Support\Money::format((int) $refund->amount) }}</td>
                                                <td>{{ ucfirst($refund->status) }}</td>
                                                <td>{{ $refund->reason ?: '—' }}</td>
                                                <td>{{ $refund->processed_at?->format('d M Y') ?: '—' }}</td>
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
            <div class="ts-card mb-4">
                <div class="ts-card-body">
                    <div class="ts-card-title mb-3">Booking</div>
                    @if ($payment->booking)
                        <p class="mb-2"><strong>{{ $payment->booking->booking_number }}</strong></p>
                        <p class="text-muted small mb-2">{{ $payment->booking->customer_name }} · {{ $payment->booking->customer_email }}</p>
                        <p class="small mb-3">Status: <span class="ts-badge ts-badge-{{ $payment->booking->status->value }}">{{ $payment->booking->status_label }}</span></p>
                        <a class="btn btn-sm btn-outline-secondary w-100" href="{{ route('admin.bookings.show', $payment->booking) }}">
                            <i class="bi bi-eye"></i> View booking
                        </a>
                    @else
                        <p class="text-muted mb-0">No booking linked.</p>
                    @endif
                </div>
            </div>

            @if ($payment->status !== \App\Enums\PaymentStatus::Paid)
                <form method="POST" action="{{ route('admin.payments.mark-paid', $payment) }}" class="ts-card mb-4">
                    @csrf
                    <div class="ts-card-body">
                        <div class="ts-card-title mb-3">Mark as paid</div>
                        <div class="mb-3">
                            <label for="reference" class="form-label fw-semibold">Reference (optional)</label>
                            <input id="reference" name="reference" class="form-control" placeholder="Bank / UTR reference">
                        </div>
                        <button class="btn btn-primary w-100" type="submit"><i class="bi bi-check2-circle"></i> Mark paid</button>
                        <div class="form-text">Records an offline settlement and syncs the booking.</div>
                    </div>
                </form>
            @endif

            @if ($payment->user)
                <div class="ts-card">
                    <div class="ts-card-body">
                        <div class="ts-card-title mb-3">Customer</div>
                        <p class="mb-1 fw-semibold">{{ $payment->user->name }}</p>
                        <p class="text-muted small mb-3">{{ $payment->user->email }} {{ $payment->user->phone ? '· ' . $payment->user->phone : '' }}</p>
                        <a class="btn btn-sm btn-outline-secondary w-100" href="{{ route('admin.users.show', $payment->user) }}">
                            <i class="bi bi-person"></i> View account
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
