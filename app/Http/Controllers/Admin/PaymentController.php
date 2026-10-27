<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $payments = Payment::query()
            ->with(['booking:id,booking_number,customer_name', 'user:id,name,email'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.addcslashes($request->string('q')->toString(), '%_\\').'%';
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
            ->select('status', DB::raw('COUNT(*) as total'), DB::raw('COALESCE(SUM(amount),0) as amount'))
            ->groupBy('status')
            ->get()
            ->keyBy(fn (Payment $row) => $row->status->value);

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $payments,
            'totals' => $totals,
            'statuses' => PaymentStatus::options(),
            'gateways' => ['razorpay' => 'Razorpay', 'stripe' => 'Stripe', 'offline' => 'Offline'],
            'filters' => $request->only(['q', 'status', 'gateway']),
        ]);
    }

    public function show(Payment $payment): Response
    {
        $payment->load([
            'booking',
            'booking.destination:id,name',
            'user:id,name,email,phone',
            'transactions',
            'refunds',
        ]);

        return Inertia::render('Admin/Payments/Show', [
            'payment' => $payment,
        ]);
    }

    /** Record an offline payment as settled. */
    public function markPaid(Request $request, Payment $payment, PaymentService $payments): RedirectResponse
    {
        if ($payment->status === PaymentStatus::Paid) {
            return back()->with('error', 'This payment is already marked as paid.');
        }

        $booking = $payment->booking;

        if ($booking === null) {
            return back()->with('error', 'This payment is not linked to a booking.');
        }

        $reference = $request->string('reference')->toString() ?: $payment->transaction_reference;

        try {
            $result = $payments->recordManualPayment($booking, $reference, (int) $payment->amount);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Payment could not be recorded: '.$e->getMessage());
        }

        return back()->with('success', $result->message);
    }
}
