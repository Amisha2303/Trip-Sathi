<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Mail\PaymentFailedMail;
use App\Mail\PaymentRefundProcessedMail;
use App\Mail\PaymentSuccessfulMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\Refund;
use App\Notifications\AdminBookingPaymentFailed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Orchestrates the payment lifecycle.
 *
 * Security invariants enforced here:
 *  1. A booking is never marked paid from a browser response. `verifyAndCapture()`
 *     re-validates a gateway signature (or re-reads the gateway for Checkout)
 *     before any status change, and does so inside a transaction with a row lock
 *     so two simultaneous callbacks cannot both succeed.
 *  2. Refunds can never exceed the settled amount.
 *  3. Every attempt is recorded in payment_transactions for auditability.
 */
class PaymentService
{
    private ?PaymentGatewayInterface $gateway = null;

    /** The gateway named by PAYMENT_GATEWAY, falling back to offline. */
    public static function configuredGateway(): PaymentGateway
    {
        return PaymentGateway::tryFrom((string) config('payment.default', 'offline'))
            ?? PaymentGateway::Offline;
    }

    public function gateway(): PaymentGatewayInterface
    {
        return $this->gateway ??= match (self::configuredGateway()) {
            PaymentGateway::Razorpay => app(RazorpayGateway::class),
            PaymentGateway::Stripe => app(StripeGateway::class),
            PaymentGateway::Offline => app(OfflineGateway::class),
        };
    }

    public function isOnline(): bool
    {
        return $this->gateway()->gateway()->isOnline();
    }

    /*
    |--------------------------------------------------------------------------
    | Order creation
    |--------------------------------------------------------------------------
    */

    /**
     * Open (or reuse) a payment row and ask the gateway for an order.
     */
    public function initiate(Booking $booking): PaymentResult
    {
        $payment = $this->pendingPaymentFor($booking);

        $result = $this->gateway()->createOrder($booking);

        $payment->forceFill([
            'gateway' => $this->gateway()->gateway()->value,
            'transaction_reference' => $result->gatewayReference,
        ])->save();

        if ($result->successful) {
            $this->logTransaction(
                $payment,
                $booking,
                $result->requiresCheckout() ? TransactionStatus::Initiated : TransactionStatus::Pending,
                $booking->final_amount,
                $result->gatewayReference,
            );
        } else {
            $this->logTransaction(
                $payment,
                $booking,
                TransactionStatus::Failed,
                $booking->final_amount,
                null,
                'charge',
                $result->failureReason,
            );

            $this->markFailed($booking, $result->failureReason ?? 'Payment could not be started.');
        }

        return $result;
    }

    /**
     * A booking may only have one open payment; reusing it keeps the
     * "abandoned checkout then retry" path from littering the finance tables.
     */
    private function pendingPaymentFor(Booking $booking): Payment
    {
        return Payment::firstOrCreate(
            [
                'booking_id' => $booking->getKey(),
                'status' => PaymentStatus::Pending->value,
            ],
            [
                'user_id' => $booking->user_id,
                'gateway' => $this->gateway()->gateway()->value,
                'amount' => $booking->final_amount,
                'currency' => $booking->currency,
            ],
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Verification & capture
    |--------------------------------------------------------------------------
    */

    /**
     * Verify a customer callback server-side and settle the booking.
     *
     * The booking row is locked for the duration so a replayed or duplicated
     * callback is a no-op rather than a double credit.
     */
    public function verifyAndCapture(Booking $booking, array $callbackPayload): PaymentResult
    {
        if (! $this->isOnline()) {
            return PaymentResult::failure('This booking is not payable online.');
        }

        $gateway = $this->gateway();

        $result = $gateway->verifyCallback($callbackPayload, $this->gatewaySecret());

        if ($result->failed()) {
            $this->logTransaction(
                null,
                $booking,
                TransactionStatus::Failed,
                $booking->final_amount,
                $callbackPayload['razorpay_payment_id'] ?? $callbackPayload['session_id'] ?? null,
                'verify',
                $result->failureReason,
            );

            $this->markFailed($booking, $result->failureReason ?? 'Payment verification failed.');

            return $result;
        }

        // Amount must match exactly; a tampered client cannot pay less.
        $expected = $this->expectedAmount($result, $booking);

        if ($expected !== null && $expected !== $booking->final_amount) {
            $this->logTransaction(
                null,
                $booking,
                TransactionStatus::Failed,
                $booking->final_amount,
                $result->gatewayReference,
                'verify',
                'Amount mismatch: gateway reported ' . $expected . ', booking expects ' . $booking->final_amount,
            );

            $this->markFailed($booking, 'Amount mismatch detected.');

            return PaymentResult::failure('Amount mismatch detected.');
        }

        return $this->settle($booking, $result, $callbackPayload);
    }

    /** Mark a booking Paid inside a locked transaction. */
    public function settle(Booking $booking, PaymentResult $result, array $context = []): PaymentResult
    {
        $settled = DB::transaction(function () use ($booking, $result, $context): PaymentResult {
            /** @var Booking $locked */
            $locked = Booking::query()->lockForUpdate()->findOrFail($booking->getKey());

            // Idempotent: a replayed callback must not credit twice.
            if ($locked->payment_status === PaymentStatus::Paid) {
                return PaymentResult::success('Payment already recorded.', $result->gatewayReference);
            }

            $payment = $this->pendingPaymentFor($locked);

            $payment->forceFill([
                'status' => PaymentStatus::Paid->value,
                'transaction_reference' => $result->gatewayReference,
                'amount' => $locked->final_amount,
                'paid_at' => now(),
                'payload' => ActivityLog::redact($result->payload),
            ])->save();

            $locked->forceFill([
                'payment_status' => PaymentStatus::Paid->value,
                'status' => $locked->status === \App\Enums\BookingStatus::Pending
                    ? \App\Enums\BookingStatus::Confirmed->value
                    : $locked->status->value,
                'confirmed_at' => $locked->confirmed_at ?? now(),
            ])->save();

            $this->logTransaction(
                $payment,
                $locked,
                TransactionStatus::Captured,
                $locked->final_amount,
                $result->gatewayReference,
                'capture',
                null,
                ActivityLog::redact($context),
            );

            return PaymentResult::success('Payment captured successfully.', $result->gatewayReference);
        });

        if ($settled->successful) {
            $this->afterSuccessfulPayment($booking->fresh(), $result);
        }

        return $settled;
    }

    /**
     * Record an offline/manual payment. Only reachable from authenticated admin
     * actions — there is no public route into this method.
     */
    public function recordManualPayment(Booking $booking, ?string $reference, ?int $amount = null, ?string $note = null): PaymentResult
    {
        $amount = $amount ?? $booking->final_amount;

        $payment = Payment::updateOrCreate(
            ['booking_id' => $booking->getKey()],
            [
                'user_id' => $booking->user_id,
                'gateway' => PaymentGateway::Offline->value,
                'status' => PaymentStatus::Paid->value,
                'amount' => $amount,
                'currency' => $booking->currency,
                'transaction_reference' => $reference ?: 'MANUAL-' . strtoupper($booking->booking_number),
                'paid_at' => now(),
                'payload' => ['note' => $note, 'recorded_by' => auth()->id()],
            ],
        );

        DB::transaction(function () use ($booking, $payment, $amount, $reference): void {
            $locked = Booking::query()->lockForUpdate()->findOrFail($booking->getKey());

            $locked->forceFill([
                'payment_status' => PaymentStatus::Paid->value,
                'status' => $locked->status === \App\Enums\BookingStatus::Pending
                    ? \App\Enums\BookingStatus::Confirmed->value
                    : $locked->status->value,
                'confirmed_at' => $locked->confirmed_at ?? now(),
            ])->save();
        });

        $this->logTransaction(
            $payment,
            $booking,
            TransactionStatus::Captured,
            $amount,
            $payment->transaction_reference,
            'charge',
            null,
            ['note' => $note],
        );

        $this->afterSuccessfulPayment($booking->fresh(), PaymentResult::success('Payment recorded.', $payment->transaction_reference));

        return PaymentResult::success('Payment recorded manually.', $payment->transaction_reference);
    }

    public function markFailed(Booking $booking, string $reason): void
    {
        $payment = Payment::where('booking_id', $booking->getKey())
            ->where('status', PaymentStatus::Pending->value)
            ->latest('id')
            ->first();

        $payment?->forceFill([
            'status' => PaymentStatus::Failed->value,
            'failure_reason' => $reason,
        ])->save();

        $booking->forceFill([
            'payment_status' => PaymentStatus::Failed->value,
            'status' => \App\Enums\BookingStatus::Failed->value,
        ])->save();

        Mail::to($booking->customer_email)->queue(new PaymentFailedMail($booking, $reason));

        foreach (User::activeAdmins() as $admin) {
            $admin->notify(new AdminBookingPaymentFailed($booking, $reason));
        }

        $booking->logActivity(
            \App\Enums\ActivityAction::PaymentUpdate,
            'Payment failed: ' . $reason,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Refunds
    |--------------------------------------------------------------------------
    */

    /**
     * Refund all or part of a settled booking.
     *
     * Over-refunding is impossible: the amount is clamped to what is still
     * refundable while the booking row is locked.
     */
    public function refund(Booking $booking, ?int $amountMinor = null, ?string $reason = null): Refund
    {
        return DB::transaction(function () use ($booking, $amountMinor, $reason): Refund {
            /** @var Booking $locked */
            $locked = Booking::query()->lockForUpdate()->findOrFail($booking->getKey());

            $payment = $locked->payment()->lockForUpdate()->first();

            if ($payment === null || ! in_array($payment->status, [
                PaymentStatus::Paid->value,
                PaymentStatus::PartiallyRefunded->value,
            ], true)) {
                throw new \RuntimeException('Only a settled booking can be refunded.');
            }

            $refundable = max(0, (int) $payment->amount - (int) $payment->amount_refunded);
            $amount = $amountMinor === null ? $refundable : min(max(0, $amountMinor), $refundable);

            if ($amount <= 0) {
                throw new \RuntimeException('Nothing left to refund on this booking.');
            }

            $reference = $payment->transaction_reference;

            if ($this->isOnline() && filled($reference)) {
                // The gateway is the source of truth for money movement.
                $result = $this->gateway()->refund($reference, $amount, $reason);

                if ($result->failed()) {
                    $this->logTransaction($payment, $locked, TransactionStatus::Failed, $amount, $reference, 'refund', $result->failureReason);

                    throw new \RuntimeException('Gateway refused the refund: ' . ($result->failureReason ?? 'unknown error'));
                }

                $reference = $result->gatewayReference ?? $reference;
            }

            $refund = Refund::create([
                'booking_id' => $locked->getKey(),
                'payment_id' => $payment->getKey(),
                'processed_by' => auth()->id(),
                'gateway' => $payment->gateway,
                'reference' => $reference,
                'amount' => $amount,
                'currency' => $payment->currency,
                'status' => 'succeeded',
                'reason' => $reason ?? 'Customer cancellation',
                'processed_at' => now(),
            ]);

            $payment->forceFill([
                'amount_refunded' => (int) $payment->amount_refunded + $amount,
                'status' => ((int) $payment->amount_refunded + $amount) >= (int) $payment->amount
                    ? PaymentStatus::Refunded->value
                    : PaymentStatus::PartiallyRefunded->value,
            ])->save();

            $totalRefunded = (int) $locked->refunded_amount + $amount;

            $locked->forceFill([
                'refunded_amount' => $totalRefunded,
                'payment_status' => $totalRefunded >= (int) $locked->final_amount
                    ? PaymentStatus::Refunded->value
                    : PaymentStatus::PartiallyRefunded->value,
                'status' => $locked->status === \App\Enums\BookingStatus::Confirmed->value
                    ? \App\Enums\BookingStatus::Refunded->value
                    : $locked->status->value,
            ])->save();

            $this->logTransaction($payment, $locked, TransactionStatus::Refunded, $amount, $reference, 'refund');

            $locked->logActivity(
                \App\Enums\ActivityAction::Refund,
                'Refunded ' . \App\Support\Money::format($amount) . ' · ' . ($reason ?? 'no reason given'),
            );

            return $refund->load('payment');
        })->tap(function (Refund $refund) use ($booking, $amountMinor, $reason): void {
            $fresh = $booking->fresh();

            Mail::to($fresh->customer_email)->queue(new PaymentRefundProcessedMail($fresh, $refund));
            $fresh->notifyBookingCancelled($fresh, $reason);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /** Side effects that must happen only after the transaction commits. */
    private function afterSuccessfulPayment(Booking $booking, PaymentResult $result): void
    {
        Mail::to($booking->customer_email)->queue(new PaymentSuccessfulMail($booking));

        $booking->user?->notifyBookingConfirmed($booking);

        foreach (User::activeAdmins() as $admin) {
            $admin->notifyAdminNewBooking($booking);
        }
    }

    private function logTransaction(
        ?Payment $payment,
        ?Booking $booking,
        TransactionStatus $status,
        int $amount,
        ?string $reference,
        string $type = 'charge',
        ?string $error = null,
        array $context = [],
    ): void {
        $gateway = $this->gateway();

        if (method_exists($gateway, 'logTransaction')) {
            $gateway->logTransaction($payment, $booking, $status, $amount, $reference, $type, $error, $context);

            return;
        }

        PaymentTransaction::create([
            'payment_id' => $payment?->getKey(),
            'booking_id' => $booking?->getKey(),
            'gateway' => $gateway->gateway()->value,
            'reference' => $reference,
            'type' => $type,
            'status' => $status->value,
            'amount' => $amount,
            'currency' => config('payment.currency', 'INR'),
            'response' => $context ?: null,
            'error' => $error,
        ]);
    }

    /** Amount reported by the gateway, when it exposes one. */
    private function expectedAmount(PaymentResult $result, Booking $booking): ?int
    {
        $amount = $result->payload['amount'] ?? null;

        return is_numeric($amount) ? (int) $amount : null;
    }

    private function gatewaySecret(): string
    {
        return (string) match ($this->gateway()->gateway()) {
            PaymentGateway::Razorpay => config('payment.razorpay.secret'),
            PaymentGateway::Stripe => config('payment.stripe.secret'),
            PaymentGateway::Offline => '',
        };
    }

    /** Public publishable key for the checkout widget. */
    public function clientKey(): ?string
    {
        return $this->gateway()->publicKey();
    }
}
