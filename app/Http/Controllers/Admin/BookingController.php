<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookingUpdateRequest;
use App\Mail\BookingCancelledMail;
use App\Mail\BookingConfirmedMail;
use App\Models\Booking;
use App\Services\Payment\PaymentService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function index(Request $request): Response
    {
        $bookings = Booking::query()
            ->with(['user:id,name,email', 'destination:id,name'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.addcslashes($request->string('q')->toString(), '%_\\').'%';
                $q->where(fn ($inner) => $inner
                    ->where('booking_number', 'LIKE', $term)
                    ->orWhere('customer_name', 'LIKE', $term)
                    ->orWhere('customer_email', 'LIKE', $term));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->toString()))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->string('payment_status')->toString()))
            ->when($request->filled('type'), fn ($q) => $q->where('booking_type', $request->string('type')->toString()))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('booking_date', '>=', $request->string('from')->toString()))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('booking_date', '<=', $request->string('to')->toString()))
            ->orderByDesc('booking_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $statusTotals = Booking::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $bookings,
            'statusTotals' => $statusTotals,
            'statuses' => BookingStatus::options(),
            'paymentStatuses' => PaymentStatus::options(),
            'types' => BookingType::options(),
            'filters' => $request->only(['q', 'status', 'payment_status', 'type', 'from', 'to']),
        ]);
    }

    public function show(Booking $booking): Response
    {
        $booking->load([
            'user',
            'destination:id,name',
            'trip:id,name',
            'coupon:id,code,name',
            'items',
            'payments',
            'payments.transactions',
        ]);

        return Inertia::render('Admin/Bookings/Show', [
            'booking' => $booking,
            'statuses' => BookingStatus::options(),
            'paymentStatuses' => PaymentStatus::options(),
            'transitions' => $this->transitionMap($booking),
        ]);
    }

    public function update(BookingUpdateRequest $request, Booking $booking): RedirectResponse
    {
        $status = BookingStatus::from($request->string('status')->toString());
        $paymentStatus = PaymentStatus::from($request->string('payment_status')->toString());

        $previousStatus = $booking->status;

        $booking->forceFill([
            'payment_status' => $paymentStatus->value,
            'admin_notes' => $request->string('admin_notes')->toString() ?: null,
            'cancellation_reason' => $request->string('cancellation_reason')->toString() ?: $booking->cancellation_reason,
        ])->save();

        if ($status !== $previousStatus && $booking->canTransitionTo($status)) {
            $booking->transitionTo($status, $request->string('cancellation_reason')->toString() ?: null);
        } elseif ($status !== $previousStatus) {
            // Direct assignment when the transition map would block it (e.g. an
            // admin correcting a bad state); still stamps the lifecycle columns.
            $booking->forceFill([
                'status' => $status->value,
                'cancelled' => $status === BookingStatus::Cancelled,
                'cancelled_at' => $status === BookingStatus::Cancelled ? ($booking->cancelled_at ?? now()) : $booking->cancelled_at,
                'confirmed_at' => $status === BookingStatus::Confirmed ? ($booking->confirmed_at ?? now()) : $booking->confirmed_at,
                'completed_at' => $status === BookingStatus::Completed ? ($booking->completed_at ?? now()) : $booking->completed_at,
            ])->save();
        }

        // Notify customer on cancellation or confirmation
        if ($status !== $previousStatus && $booking->customer_email) {
            try {
                if ($status === BookingStatus::Cancelled) {
                    Mail::to($booking->customer_email)->send(
                        new BookingCancelledMail($booking, $request->string('cancellation_reason')->toString() ?: $booking->cancellation_reason)
                    );
                } elseif ($status === BookingStatus::Confirmed) {
                    Mail::to($booking->customer_email)->send(new BookingConfirmedMail($booking));
                }
            } catch (\Throwable $e) {
                Log::warning('Booking status change email could not be sent: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking updated.');
    }

    public function recordPayment(Request $request, Booking $booking, PaymentService $payments): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['nullable', 'numeric', 'min:0.01', 'max:10000000'],
            'reference' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $amount = filled($data['amount'] ?? null) ? Booking::toMinor($data['amount']) : null;

        try {
            $result = $payments->recordManualPayment(
                $booking,
                $data['reference'] ?? null,
                $amount,
                $data['note'] ?? null,
            );
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Payment could not be recorded: '.$e->getMessage());
        }

        return back()->with('success', $result->message);
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted.');
    }

    /** @return array<string, string> */
    private function transitionMap(Booking $booking): array
    {
        $map = [];

        foreach ($booking->allowedTransitions() as $status) {
            $map[$status->value] = $status->label();
        }

        return $map + [$booking->status->value => $booking->status->label()];
    }
}
