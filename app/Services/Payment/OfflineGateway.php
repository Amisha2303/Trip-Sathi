<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Enums\PaymentGateway;
use App\Enums\TransactionStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentTransaction;

/**
 * Offline / manual-payment adapter.
 *
 * The full booking lifecycle is preserved: a payment row is created as Pending
 * and only moves to Paid when an authorised staff member records the transfer.
 * This is what makes `PAYMENT_GATEWAY=offline` a genuine mode rather than a
 * bypass of the payment layer.
 */
class OfflineGateway implements PaymentGatewayInterface
{
    public function gateway(): PaymentGateway
    {
        return PaymentGateway::Offline;
    }

    public function publicKey(): ?string
    {
        return null;
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function createOrder(Booking $booking): PaymentResult
    {
        $reference = 'OFFLINE-'.strtoupper($booking->booking_number);

        return PaymentResult::offline(
            'Payment will be confirmed by the Trip Sathi team.',
            ['reference' => $reference],
            $reference,
        );
    }

    /**
     * There is no external signature to verify offline; instead the caller must
     * pass an authenticated admin/user session, which is checked by
     * PaymentService before delegating here.
     */
    public function verifyCallback(array $payload, string $secret): PaymentResult
    {
        return PaymentResult::failure('Offline payments cannot be verified from a browser callback.');
    }

    public function fetchOrder(string $reference): PaymentResult
    {
        return PaymentResult::success('Offline payment reference recorded.', $reference);
    }

    public function capture(string $reference, int $amountMinor): PaymentResult
    {
        return PaymentResult::success('Offline payment recorded manually.', $reference, [
            'amount' => $amountMinor,
        ]);
    }

    public function refund(string $reference, int $amountMinor, ?string $reason = null): PaymentResult
    {
        return PaymentResult::success('Offline refund recorded manually.', $reference, [
            'amount' => $amountMinor,
            'reason' => $reason,
        ]);
    }

    public function logTransaction(
        ?Payment $payment,
        ?Booking $booking,
        TransactionStatus $status,
        int $amount,
        ?string $reference,
        string $type = 'charge',
        ?string $error = null,
        array $context = [],
    ): PaymentTransaction {
        return PaymentTransaction::create([
            'payment_id' => $payment?->getKey(),
            'booking_id' => $booking?->getKey(),
            'gateway' => PaymentGateway::Offline->value,
            'reference' => $reference,
            'type' => $type,
            'status' => $status->value,
            'amount' => $amount,
            'currency' => config('payment.currency', 'INR'),
            'response' => $context ?: ['note' => 'Recorded manually in the admin panel'],
            'error' => $error,
        ]);
    }

    /** Convenience so controllers can assert the active mode. */
    public static function isActive(): bool
    {
        return PaymentService::configuredGateway() === PaymentGateway::Offline;
    }
}
