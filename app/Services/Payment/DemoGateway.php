<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Enums\PaymentGateway;
use App\Enums\TransactionStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use Illuminate\Support\Str;

/**
 * Demo online payment adapter (Free Sandbox).
 *
 * Provides a 100% free, interactive online payment gateway simulation with:
 *  - Simulated UPI (Google Pay, PhonePe, Paytm, QR code)
 *  - Simulated Credit / Debit Cards
 *  - Simulated Net Banking (HDFC, SBI, ICICI, Axis)
 *  - 1-click instant approval
 *  - Server-side cryptographic HMAC token and signature validation
 *  - Simulated refund and audit logging
 *
 * This replaces the manual, non-interactive offline default so users can
 * experience the full interactive online checkout flow out-of-the-box.
 */
class DemoGateway implements PaymentGatewayInterface
{
    public function gateway(): PaymentGateway
    {
        return PaymentGateway::Demo;
    }

    public function publicKey(): ?string
    {
        return (string) config('payment.demo.key', 'demo_key_tripsathi_free');
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function createOrder(Booking $booking): PaymentResult
    {
        $orderId = 'DEMO-ORD-'.strtoupper($booking->booking_number).'-'.strtoupper(Str::random(6));
        $secret = (string) config('payment.demo.secret', 'demo_secret_tripsathi_free_sandbox');
        $authToken = $this->expectedAuthToken($orderId, (int) $booking->final_amount, $secret);

        return PaymentResult::awaiting(
            orderId: $orderId,
            amount: (int) $booking->final_amount,
            currency: (string) ($booking->currency ?: config('payment.currency', 'INR')),
            clientScript: '/demo-gateway',
            clientOptions: [
                'gateway' => 'demo',
                'order_id' => $orderId,
                'booking_number' => $booking->booking_number,
                'booking_id' => $booking->getKey(),
                'amount' => (int) $booking->final_amount,
                'currency' => (string) ($booking->currency ?: 'INR'),
                'customer_name' => $booking->customer_name,
                'customer_email' => $booking->customer_email,
                'customer_phone' => $booking->customer_phone,
                'auth_token' => $authToken,
                'description' => 'Trip Sathi Booking #'.$booking->booking_number,
            ],
            reference: $orderId,
        );
    }

    /**
     * Verify the customer callback from the interactive Demo Checkout simulator.
     * Validates HMAC authorization token and order/payment integrity.
     */
    public function verifyCallback(array $payload, string $secret): PaymentResult
    {
        $status = (string) ($payload['status'] ?? 'success');

        if ($status === 'failed') {
            return PaymentResult::failure(
                (string) ($payload['failure_reason'] ?? 'Simulated demo payment was declined.')
            );
        }

        $orderId = (string) ($payload['demo_order_id'] ?? $payload['order_id'] ?? '');
        $paymentId = (string) ($payload['demo_payment_id'] ?? $payload['payment_id'] ?? '');
        $authToken = (string) ($payload['auth_token'] ?? '');
        $signature = (string) ($payload['demo_signature'] ?? $payload['signature'] ?? '');
        $amount = isset($payload['amount']) ? (int) $payload['amount'] : null;

        if ($orderId === '' || $paymentId === '') {
            return PaymentResult::failure('Incomplete demo payment callback payload.');
        }

        // Validate cryptographic signature/token to ensure order and amount authenticity
        $tokenValid = false;
        if ($authToken !== '' && $amount !== null) {
            $expectedToken = $this->expectedAuthToken($orderId, $amount, $secret);
            $tokenValid = hash_equals($expectedToken, $authToken);
        }

        $sigValid = false;
        if ($signature !== '') {
            $expectedSig = $this->expectedSignature($orderId, $paymentId, $secret);
            $sigValid = hash_equals($expectedSig, $signature);
        }

        // Accept if either the HMAC auth token (with matching amount) or HMAC signature is verified
        if (! $tokenValid && ! $sigValid) {
            return PaymentResult::failure('Demo payment cryptographic signature verification failed.', [
                'order_id' => $orderId,
                'payment_id' => $paymentId,
            ]);
        }

        $method = (string) ($payload['payment_method'] ?? 'demo_instant');

        return PaymentResult::success(
            message: 'Demo payment verified and authorized successfully.',
            reference: $paymentId,
            payload: [
                'amount' => $amount,
                'currency' => (string) ($payload['currency'] ?? config('payment.currency', 'INR')),
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'method' => $method,
                'simulated' => true,
                'processed_at' => now()->toIso8601String(),
            ],
            orderId: $orderId,
        );
    }

    public function expectedAuthToken(string $orderId, int $amount, string $secret): string
    {
        return hash_hmac('sha256', $orderId.'|'.$amount, $secret);
    }

    public function expectedSignature(string $orderId, string $paymentId, string $secret): string
    {
        return hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);
    }

    public function fetchOrder(string $reference): PaymentResult
    {
        return PaymentResult::success('Demo order fetched.', $reference, [
            'order_id' => $reference,
            'status' => 'paid',
        ]);
    }

    public function capture(string $reference, int $amountMinor): PaymentResult
    {
        return PaymentResult::success('Demo payment captured.', $reference, [
            'reference' => $reference,
            'amount' => $amountMinor,
            'status' => 'captured',
        ]);
    }

    public function refund(string $reference, int $amountMinor, ?string $reason = null): PaymentResult
    {
        $refundId = 'DEMO-REF-'.strtoupper(Str::random(10));

        return PaymentResult::success(
            message: 'Demo refund simulated successfully.',
            reference: $refundId,
            payload: [
                'refund_id' => $refundId,
                'original_reference' => $reference,
                'amount' => $amountMinor,
                'currency' => config('payment.currency', 'INR'),
                'reason' => $reason ?? 'Demo simulated refund',
                'status' => 'succeeded',
            ],
        );
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
            'gateway' => PaymentGateway::Demo->value,
            'reference' => $reference,
            'type' => $type,
            'status' => $status->value,
            'amount' => $amount,
            'currency' => config('payment.currency', 'INR'),
            'response' => array_merge([
                'gateway' => 'demo',
                'mode' => 'sandbox',
                'simulated' => true,
            ], $context),
            'error' => $error,
        ]);
    }

    public static function isActive(): bool
    {
        return PaymentService::configuredGateway() === PaymentGateway::Demo;
    }
}
