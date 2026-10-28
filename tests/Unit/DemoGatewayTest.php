<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\PaymentGateway;
use App\Models\Booking;
use App\Services\Payment\DemoGateway;
use App\Services\Payment\PaymentService;
use Tests\TestCase;

class DemoGatewayTest extends TestCase
{
    public function test_demo_gateway_is_online_and_configured(): void
    {
        $gateway = new DemoGateway();

        $this->assertSame(PaymentGateway::Demo, $gateway->gateway());
        $this->assertTrue($gateway->gateway()->isOnline());
        $this->assertTrue($gateway->isConfigured());
        $this->assertNotEmpty($gateway->publicKey());
    }

    public function test_payment_service_defaults_to_demo_gateway(): void
    {
        config(['payment.default' => 'demo']);

        $service = app(PaymentService::class);

        $this->assertSame(PaymentGateway::Demo, PaymentService::configuredGateway());
        $this->assertInstanceOf(DemoGateway::class, $service->gateway());
        $this->assertTrue($service->isOnline());
    }

    public function test_create_order_generates_valid_awaiting_result(): void
    {
        $gateway = new DemoGateway();

        $booking = new Booking();
        $booking->id = 123;
        $booking->booking_number = 'TS-TEST-9999';
        $booking->final_amount = 5400;
        $booking->currency = 'INR';
        $booking->customer_name = 'Demo User';
        $booking->customer_email = 'demo@tripsathi.test';
        $booking->customer_phone = '+919999999999';

        $result = $gateway->createOrder($booking);

        $this->assertTrue($result->successful);
        $this->assertTrue($result->requiresCheckout());
        $this->assertStringStartsWith('DEMO-ORD-TS-TEST-9999', $result->orderId);
        $this->assertSame(5400, $result->payload['amount']);
        $this->assertSame('INR', $result->payload['currency']);
        $this->assertSame('demo', $result->clientOptions['gateway']);
        $this->assertNotEmpty($result->clientOptions['auth_token']);
    }

    public function test_verify_callback_succeeds_with_valid_auth_token(): void
    {
        $gateway = new DemoGateway();
        $secret = 'test-demo-secret';

        $orderId = 'DEMO-ORD-TS-1234';
        $paymentId = 'DEMO-PAY-XYZ789';
        $amount = 3500;
        $authToken = $gateway->expectedAuthToken($orderId, $amount, $secret);

        $result = $gateway->verifyCallback([
            'demo_order_id' => $orderId,
            'demo_payment_id' => $paymentId,
            'auth_token' => $authToken,
            'amount' => $amount,
            'currency' => 'INR',
            'payment_method' => 'demo_upi',
            'status' => 'success',
        ], $secret);

        $this->assertTrue($result->successful);
        $this->assertSame($paymentId, $result->gatewayReference);
        $this->assertSame($amount, $result->payload['amount']);
        $this->assertSame('demo_upi', $result->payload['method']);
    }

    public function test_verify_callback_rejects_tampered_amount_or_invalid_signature(): void
    {
        $gateway = new DemoGateway();
        $secret = 'test-demo-secret';

        $orderId = 'DEMO-ORD-TS-1234';
        $paymentId = 'DEMO-PAY-XYZ789';
        $legitAmount = 5000;
        $authToken = $gateway->expectedAuthToken($orderId, $legitAmount, $secret);

        // Tamper with amount to pay 1 instead of 5000
        $result = $gateway->verifyCallback([
            'demo_order_id' => $orderId,
            'demo_payment_id' => $paymentId,
            'auth_token' => $authToken,
            'amount' => 1,
            'currency' => 'INR',
            'status' => 'success',
        ], $secret);

        $this->assertFalse($result->successful);
        $this->assertStringContainsString('signature verification failed', $result->failureReason);
    }

    public function test_verify_callback_handles_simulated_failure(): void
    {
        $gateway = new DemoGateway();
        $secret = 'test-demo-secret';

        $result = $gateway->verifyCallback([
            'demo_order_id' => 'DEMO-ORD-123',
            'demo_payment_id' => 'DEMO-FAIL-123',
            'status' => 'failed',
            'failure_reason' => 'Simulated test decline by user',
        ], $secret);

        $this->assertFalse($result->successful);
        $this->assertSame('Simulated test decline by user', $result->failureReason);
    }

    public function test_demo_refund_simulation(): void
    {
        $gateway = new DemoGateway();

        $result = $gateway->refund('DEMO-PAY-ORIGINAL-999', 2500, 'Customer requested cancellation');

        $this->assertTrue($result->successful);
        $this->assertStringStartsWith('DEMO-REF-', $result->gatewayReference);
        $this->assertSame(2500, $result->payload['amount']);
        $this->assertSame('succeeded', $result->payload['status']);
    }
}
