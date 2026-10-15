<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Enums\PaymentGateway;
use App\Models\Booking;

/**
 * Contract every payment adapter implements.
 *
 * The booking engine only ever talks to this interface, which is what allows
 * PAYMENT_GATEWAY to switch between Razorpay, Stripe and the offline mode
 * without a single change to BookingService.
 */
interface PaymentGatewayInterface
{
    public function gateway(): PaymentGateway;

    /** Public key / publishable key safe to expose to the browser. */
    public function publicKey(): ?string;

    /** True when the adapter has usable credentials configured. */
    public function isConfigured(): bool;

    /**
     * Create a payment order for a booking.
     * The returned result carries either a gateway reference (offline) or the
     * checkout script + options the browser needs.
     */
    public function createOrder(Booking $booking): PaymentResult;

    /**
     * Verify the customer's callback.
     *
     * MUST validate a server-side signature — a browser response is never
     * trusted on its own.
     */
    public function verifyCallback(array $payload, string $secret): PaymentResult;

    /** Fetch the authoritative order state straight from the gateway. */
    public function fetchOrder(string $reference): PaymentResult;

    public function capture(string $reference, int $amountMinor): PaymentResult;

    public function refund(string $reference, int $amountMinor, ?string $reason = null): PaymentResult;
}
