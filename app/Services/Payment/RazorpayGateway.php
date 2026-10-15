<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Enums\PaymentGateway;
use App\Enums\TransactionStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Razorpay adapter.
 *
 * Notes on the security model:
 *  - The key id is public and safe to ship to the browser; the key secret is not
 *    and only ever appears in a server-side HTTP call.
 *  - `verifyCallback()` recomputes the HMAC over the raw payload. Without that
 *    step an attacker could simply POST `{status: "captured"}`.
 *  - Payloads written to payment_transactions are filtered by `redactPayload()`
 *    so a secret can never leak into the admin transactions screen.
 */
class RazorpayGateway implements PaymentGatewayInterface
{
    /** @var array<string, mixed> */
    private array $lastRequest = [];

    public function gateway(): PaymentGateway
    {
        return PaymentGateway::Razorpay;
    }

    public function publicKey(): ?string
    {
        return config('payment.razorpay.key');
    }

    public function isConfigured(): bool
    {
        return filled(config('payment.razorpay.key')) && filled(config('payment.razorpay.secret'));
    }

    public function createOrder(Booking $booking): PaymentResult
    {
        if (! $this->isConfigured()) {
            return PaymentResult::failure('Razorpay credentials are not configured.');
        }

        $payload = [
            'amount' => $booking->final_amount,          // Razorpay expects minor units
            'currency' => $booking->currency ?: config('payment.currency', 'INR'),
            'receipt' => $booking->booking_number,
            'notes' => [
                'booking_number' => $booking->booking_number,
                'booking_id' => (string) $booking->getKey(),
            ],
        ];

        $response = $this->request('POST', '/orders', $payload);

        if ($response['failed'] ?? true) {
            return PaymentResult::failure($response['error'] ?? 'Unable to create Razorpay order.', $response['body'] ?? []);
        }

        $order = $response['body'];
        $orderId = (string) ($order['id'] ?? '');

        return PaymentResult::awaiting(
            orderId: $orderId,
            amount: (int) ($order['amount'] ?? $booking->final_amount),
            currency: (string) ($order['currency'] ?? 'INR'),
            clientScript: 'https://checkout.razorpay.com/v1/checkout.js',
            clientOptions: [
                'key' => $this->publicKey(),
                'amount' => (int) ($order['amount'] ?? $booking->final_amount),
                'currency' => (string) ($order['currency'] ?? 'INR'),
                'name' => config('tripsathi.name', 'Trip Sathi'),
                'description' => 'Booking ' . $booking->booking_number,
                'order_id' => $orderId,
                'prefill' => [
                    'name' => $booking->customer_name,
                    'email' => $booking->customer_email,
                    'contact' => $booking->customer_phone,
                ],
                'notes' => ['booking_number' => $booking->booking_number],
                'theme' => ['color' => '#0E7C7B'],
                'handler' => 'window.TripSathi.onRazorpaySuccess',
            ],
            reference: $orderId,
        );
    }

    /**
     * Recompute the Razorpay checkout signature server-side.
     *
     * Signed payload is `razorpay_order_id|razorpay_payment_id`; the HMAC must
     * equal `razorpay_signature` from the callback.
     */
    public function verifyCallback(array $payload, string $secret): PaymentResult
    {
        $orderId = (string) ($payload['razorpay_order_id'] ?? '');
        $paymentId = (string) ($payload['razorpay_payment_id'] ?? '');
        $signature = (string) ($payload['razorpay_signature'] ?? '');

        if ($orderId === '' || $paymentId === '' || $signature === '') {
            return PaymentResult::failure('Incomplete payment callback payload.');
        }

        if (! hash_equals($this->expectedSignature($orderId, $paymentId, $secret), $signature)) {
            return PaymentResult::failure('Payment signature verification failed.', [
                'razorpay_order_id' => $orderId,
            ]);
        }

        return PaymentResult::success('Payment verified.', $paymentId, [
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
        ], $orderId);
    }

    /** Exposed for tests: the expected HMAC for a set of identifiers. */
    public function expectedSignature(string $orderId, string $paymentId, string $secret): string
    {
        return hash_hmac('sha256', $orderId . '|' . $paymentId, $secret);
    }

    public function fetchOrder(string $reference): PaymentResult
    {
        if (! $this->isConfigured()) {
            return PaymentResult::failure('Razorpay credentials are not configured.');
        }

        $response = $this->request('GET', '/orders/' . rawurlencode($reference));

        if ($response['failed'] ?? true) {
            return PaymentResult::failure($response['error'] ?? 'Unable to fetch Razorpay order.', $response['body'] ?? []);
        }

        $order = $response['body'];
        $status = (string) ($order['status'] ?? '');

        // Razorpay reports 'paid' once the payment is captured.
        return match ($status) {
            'paid' => PaymentResult::success('Order paid.', (string) ($order['id'] ?? $reference), $order),
            'created', 'attempted' => PaymentResult::success('Order awaiting payment.', (string) ($order['id'] ?? $reference), $order),
            default => PaymentResult::failure('Order status: ' . ($status ?: 'unknown'), $order),
        };
    }

    public function capture(string $reference, int $amountMinor): PaymentResult
    {
        $response = $this->request('POST', '/payments/' . rawurlencode($reference) . '/capture', [
            'amount' => $amountMinor,
            'currency' => config('payment.currency', 'INR'),
        ]);

        return ($response['failed'] ?? true)
            ? PaymentResult::failure($response['error'] ?? 'Capture failed.', $response['body'] ?? [])
            : PaymentResult::success('Payment captured.', (string) ($response['body']['id'] ?? $reference), $response['body']);
    }

    public function refund(string $reference, int $amountMinor, ?string $reason = null): PaymentResult
    {
        $response = $this->request('POST', '/payments/' . rawurlencode($reference) . '/refund', array_filter([
            'amount' => $amountMinor,
            'speed' => 'normal',
            'notes' => $reason ? ['reason' => $reason] : null,
        ]));

        return ($response['failed'] ?? true)
            ? PaymentResult::failure($response['error'] ?? 'Refund failed.', $response['body'] ?? [])
            : PaymentResult::success('Refund initiated.', (string) ($response['body']['id'] ?? $reference), $response['body']);
    }

    /**
     * Authenticated JSON call against the Razorpay API.
     *
     * @return array{failed: bool, body: array<string, mixed>, error?: string}
     */
    private function request(string $method, string $path, array $payload = []): array
    {
        $this->lastRequest = ['method' => $method, 'path' => $path, 'payload' => $payload];

        try {
            $response = Http::withBasicAuth(
                (string) config('payment.razorpay.key'),
                (string) config('payment.razorpay.secret'),
            )
                ->acceptJson()
                ->timeout((int) config('payment.razorpay.timeout', 30))
                ->send($method, rtrim((string) config('payment.razorpay.api_base'), '/') . $path, [
                    'json' => $payload,
                ]);
        } catch (\Throwable $e) {
            Log::warning('Razorpay request failed', [
                'path' => $path,
                'message' => $e->getMessage(),
            ]);

            return ['failed' => true, 'body' => [], 'error' => 'Gateway unreachable: ' . $e->getMessage()];
        }

        $body = (array) $response->json();

        if ($response->failed()) {
            return [
                'failed' => true,
                'body' => $this->redactPayload($body),
                'error' => $body['error']['description'] ?? 'Razorpay returned HTTP ' . $response->status(),
            ];
        }

        return ['failed' => false, 'body' => $this->redactPayload($body)];
    }

    /**
     * Strip anything credential-shaped before a payload is persisted.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function redactPayload(array $payload): array
    {
        $blocked = ['key_secret', 'secret', 'signature', 'auth', 'authorization', 'password'];

        foreach ($blocked as $needle) {
            $payload = $this->stripRecursive($payload, $needle);
        }

        return $payload;
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<array-key, mixed>
     */
    private function stripRecursive(array $data, string $needle): array
    {
        foreach ($data as $key => $value) {
            if (is_string($key) && str_contains(strtolower($key), $needle)) {
                $data[$key] = '[redacted]';
            } elseif (is_array($value)) {
                $data[$key] = $this->stripRecursive($value, $needle);
            }
        }

        return $data;
    }

    /**
     * Persist a gateway interaction. Never store the secret.
     */
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
            'gateway' => $this->gateway()->value,
            'reference' => $reference,
            'type' => $type,
            'status' => $status->value,
            'amount' => $amount,
            'currency' => config('payment.currency', 'INR'),
            'request' => ['endpoint' => $this->lastRequest['path'] ?? null],
            'response' => $context ?: null,
            'error' => $error,
        ]);
    }
}
