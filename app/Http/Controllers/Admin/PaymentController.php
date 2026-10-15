<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $payments = Payment::query()
            ->with(['booking:id,booking_number,customer_name', 'user:id,name,email'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . addcslashes($request->string('q')->toString(), '%_\\') . '%';
                $q->where(fn ($inner) => $inner
                    ->where('transaction_reference', 'LIKE', $term)
                    ->orWhereHas('booking', fn ($b) => $b
                        ->where('booking_number', 'LIKE', $term)
                        ->orWhere('customer_name', 'LIKE', $term)));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('gateway'), fn ($q) => $q->where('gateway', $request->string('gateway')->toString()))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $totals = Payment::query()
            ->select('status', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'), \Illuminate\Support\Facades\DB::raw('COALESCE(SUM(amount),0) as amount'))
            ->groupBy('status')
            ->get()
            ->keyBy(fn (Payment $row) => $row->status->value);

        return view('admin.payments.index', [
            'payments' => $payments,
            'totals' => $totals,
            'statuses' => PaymentStatus::options(),
            'gateways' => ['razorpay' => 'Razorpay', 'stripe' => 'Stripe', 'offline' => 'Offline'],
        ]);
    }

    public function show(Payment $payment): View
    {
        $payment->load([
            'booking',
            'booking.destination:id,name',
            'user:id,name,email,phone',
            'transactions',
            'refunds',
        ]);

        return view('admin.payments.show', [
            'payment' => $payment,
        ]);
    }

    /** Record an offline payment as settled. */
    public function markPaid(Request $request, Payment $payment): RedirectResponse
    {
        if ($payment->status === PaymentStatus::Paid) {
            return back()->with('error', 'This payment is already marked as paid.');
        }

        $payment->forceFill([
            'status' => PaymentStatus::Paid->value,
            'paid_at' => $payment->paid_at ?? now(),
            'transaction_reference' => $payment->transaction_reference
                ?? $request->string('reference')->toString()
                ?: 'OFF-' . strtoupper(substr(uniqid(), -8)),
        ])->save();

        // Keep the booking's payment status in step with the payment record.
        $booking = $payment->booking;
        if ($booking && $booking->payment_status !== PaymentStatus::Paid) {
            $booking->forceFill(['payment_status' => PaymentStatus::Paid->value])->save();
        }

        return back()->with('success', 'Payment marked as paid.');
    }
}
