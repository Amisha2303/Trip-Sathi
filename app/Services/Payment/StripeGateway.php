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
use Illuminate\Support\Str;

/**
 * Stripe adapter.
 *
 * Stripe's Checkout flow is server-created: we POST to `/checkout/sessions`
 * with our own `client_reference_id`, then confirm the session server-side on
 * return. The publishable key is safe for the browser; the secret key is not.
 */
class StripeGateway implements PaymentGatewayInterface
{
    /** @var array<string, mixed> */
    private array $lastRequest = [];

    public function gateway(): PaymentGateway
    {
        return PaymentGateway::Stripe;
    }

    public function publicKey(): ?string
    {
        return config('payment.stripe.key');
    }

    public function isConfigured(): bool
    {
        return filled(config('payment.stripe.key')) && filled(config('payment.stripe.secret'));
    }

    public function createOrder(Booking $booking): PaymentResult
    {
        if (! $this->isConfigured()) {
            return PaymentResult::failure('Stripe credentials are not configured.');
        }

        $payload = [
            'mode' => 'payment',
            'client_reference_id' => (string) $booking->getKey(),
            'success_url' => route('payments.callback', ['booking' => $booking->getKey()])
                .'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('bookings.show', $booking),
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => strtolower($booking->currency ?: 'inr'),
                    'unit_amount' => $booking->final_amount, // minor units
                    'product_data' => [
                        'name' => 'Trip Sathi booking '.$booking->booking_number,
                        'description' => $booking->type_label,
                    ],
                ],
            ]],
            'metadata' => [
                'booking_number' => $booking->booking_number,
                'booking_id' => (string) $booking->getKey(),
            ],
        ];

        $response = $this->request('POST', '/checkout/sessions', $payload);

        if ($response['failed'] ?? true) {
            return PaymentResult::failure($response['error'] ?? 'Unable to create Stripe checkout session.', $response['body'] ?? []);
        }

        $session = $response['body'];
        $sessionId = (string) ($session['id'] ?? '');

        return PaymentResult::awaiting(
            orderId: $sessionId,
            amount: (int) ($session['amount_total'] ?? $booking->final_amount),
            currency: strtoupper((string) ($session['currency'] ?? 'inr')),
            clientScript: 'https://js.stripe.com/v3/',
            clientOptions: [
                'publishableKey' => $this->publicKey(),
                'sessionId' => $sessionId,
                'redirect' => (string) ($session['url'] ?? ''),
            ],
            reference: $sessionId,
        );
    }

    public function verifyCallback(array $payload, string $secret): PaymentResult
    {
        $sessionId = (string) ($payload['session_id'] ?? $payload['checkout_session_id'] ?? '');

        if ($sessionId === '') {
            return PaymentResult::failure('Missing Stripe session id.');
        }

        // Stripe Checkout sessions are single-use and server-authoritative;
        // re-fetching is the strongest verification available.
        $session = $this->request('GET', '/checkout/sessions/'.rawurlencode($sessionId));

        if ($session['failed'] ?? true) {
            return PaymentResult::failure($session['error'] ?? 'Unable to verify Stripe session.', $session['body'] ?? []);
        }

        $body = $session['body'];

        if (($body['payment_status'] ?? null) !== 'paid') {
            return PaymentResult::failure('Stripe session is not paid.', $body);
        }

        return PaymentResult::success(
            'Payment verified.',
            (string) ($body['payment_intent'] ?? $sessionId),
            $body,
            $sessionId,
        );
    }

    public function fetchOrder(string $reference): PaymentResult
    {
        $session = $this->request('GET', '/checkout/sessions/'.rawurlencode($reference));

        if ($session['failed'] ?? true) {
            return PaymentResult::failure($session['error'] ?? 'Unable to fetch Stripe session.', $session['body'] ?? []);
        }

        $body = $session['body'];

        return ($body['payment_status'] ?? null) === 'paid'
            ? PaymentResult::success('Session paid.', (string) ($body['payment_intent'] ?? $reference), $body)
            : PaymentResult::failure('Session not paid.', $body);
    }

    public function capture(string $reference, int $amountMinor): PaymentResult
    {
        // Stripe Checkout captures automatically; explicit capture only applies
        // to manual-capture PaymentIntents.
        $response = $this->request('POST', '/payment_intents/'.rawurlencode($reference).'/capture', [
            'amount_to_capture' => $amountMinor,
        ]);

        return ($response['failed'] ?? true)
            ? PaymentResult::failure($response['error'] ?? 'Capture failed.', $response['body'] ?? [])
            : PaymentResult::success('Payment captured.', (string) ($response['body']['id'] ?? $reference), $response['body']);
    }

    public function refund(string $reference, int $amountMinor, ?string $reason = null): PaymentResult
    {
        $payload = ['payment_intent' => $reference];

        if ($amountMinor > 0) {
            $payload['amount'] = $amountMinor;
        }

        if ($reason !== null) {
            $payload['reason'] = Str::slug($reason);
        }

        $response = $this->request('POST', '/refunds', $payload);

        return ($response['failed'] ?? true)
            ? PaymentResult::failure($response['error'] ?? 'Refund failed.', $response['body'] ?? [])
            : PaymentResult::success('Refund initiated.', (string) ($response['body']['id'] ?? ''), $response['body']);
    }

    /**
     * Verify a Stripe webhook signature.
     *
     * Stripe signs `t=timestamp,v1=signature` over the raw request body, so this
     * must receive the untouched body rather than a re-encoded array.
     */
    public function verifyWebhook(string $payload, ?string $signatureHeader, ?string $secret, int $tolerance = 300): bool
    {
        if (blank($payload) || blank($signatureHeader) || blank($secret)) {
            return false;
        }

        $timestamp = null;
        $signatures = [];

        foreach (explode(',', $signatureHeader) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);

            if ($value === null) {
                continue;
            }

            match ($key) {
                't' => $timestamp = (int) $value,
                'v1' => $signatures[] = $value,
                default => null,
            };
        }

        if ($timestamp === null || $signatures === []) {
            return false;
        }

        // Replay protection: reject stale signatures.
        if (abs(time() - $timestamp) > $tolerance) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{failed: bool, body: array<string, mixed>, error?: string}
     */
    private function request(string $method, string $path, array $payload = []): array
    {
        $this->lastRequest = ['method' => $method, 'path' => $path, 'payload' => $payload];

        try {
            $request = Http::withToken((string) config('payment.stripe.secret'))
                ->asForm()
                ->acceptJson()
                ->timeout((int) config('payment.stripe.timeout', 30));

            // Stripe's form encoding uses bracket notation for nested values.
            $response = $method === 'GET'
                ? $request->get(rtrim((string) config('payment.stripe.api_base'), '/').$path)
                : $request->send($method, rtrim((string) config('payment.stripe.api_base'), '/').$path, [
                    'body' => $this->flatten($payload),
                ]);
        } catch (\Throwable $e) {
            Log::warning('Stripe request failed', ['path' => $path, 'message' => $e->getMessage()]);

            return ['failed' => true, 'body' => [], 'error' => 'Gateway unreachable: '.$e->getMessage()];
        }

        $body = (array) $response->json();

        if ($response->failed()) {
            return [
                'failed' => true,
                'body' => $this->redactPayload($body),
                'error' => $body['error']['message'] ?? 'Stripe returned HTTP '.$response->status(),
            ];
        }

        return ['failed' => false, 'body' => $this->redactPayload($body)];
    }

    /**
     * Convert nested arrays to Stripe's bracket notation.
     *
     * @param  array<array-key, mixed>  $data
     * @return array<string, mixed>
     */
    private function flatten(array $data, string $prefix = ''): array
    {
        $output = [];

        foreach ($data as $key => $value) {
            $name = $prefix === '' ? (string) $key : $prefix.'['.$key.']';

            if (is_array($value)) {
                $output += $this->flatten($value, $name);
            } elseif ($value !== null) {
                $output[$name] = is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;
            }
        }

        return $output;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function redactPayload(array $payload): array
    {
        $blocked = ['secret', 'signature', 'authorization', 'password', 'card', 'client_secret'];

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
