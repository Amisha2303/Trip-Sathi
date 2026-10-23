<?php

declare(strict_types=1);

namespace App\Services\Payment;

/**
 * Result object returned by every gateway call.
 *
 * Carrying failures as values rather than exceptions keeps the booking flow
 * explicit: the caller decides whether an unverified payment leaves the booking
 * Pending or marks it Failed.
 */
final class PaymentResult
{
    private function __construct(
        public readonly bool $successful,
        public readonly string $message,
        public readonly ?string $gatewayReference = null,
        public readonly ?string $orderId = null,
        public readonly ?string $signature = null,
        public readonly array $payload = [],
        public readonly ?string $failureReason = null,
        public readonly ?string $clientScript = null,
        public readonly array $clientOptions = [],
    ) {}

    public static function success(
        string $message,
        ?string $reference = null,
        array $payload = [],
        ?string $orderId = null,
    ): self {
        return new self(
            successful: true,
            message: $message,
            gatewayReference: $reference,
            orderId: $orderId,
            payload: $payload,
        );
    }

    public static function failure(string $reason, array $payload = []): self
    {
        return new self(
            successful: false,
            message: 'Payment could not be processed.',
            failureReason: $reason,
            payload: $payload,
        );
    }

    /**
     * Payment order created and awaiting the customer's browser redirect.
     * `clientScript`/`clientOptions` drive the checkout widget.
     */
    public static function awaiting(
        string $orderId,
        int $amount,
        string $currency,
        string $clientScript,
        array $clientOptions,
        ?string $reference = null,
    ): self {
        return new self(
            successful: true,
            message: 'Payment order created.',
            gatewayReference: $reference ?? $orderId,
            orderId: $orderId,
            payload: ['amount' => $amount, 'currency' => $currency],
            clientScript: $clientScript,
            clientOptions: $clientOptions,
        );
    }

    public static function offline(string $message, array $payload = [], ?string $reference = null): self
    {
        return new self(
            successful: true,
            message: $message,
            gatewayReference: $reference,
            payload: $payload,
        );
    }

    public function failed(): bool
    {
        return ! $this->successful;
    }

    public function requiresCheckout(): bool
    {
        return $this->clientScript !== null;
    }
}
