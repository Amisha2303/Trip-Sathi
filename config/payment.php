<?php

declare(strict_types=1);

use App\Enums\PaymentGateway;

return [

    /*
    |--------------------------------------------------------------------------
    | Default gateway
    |--------------------------------------------------------------------------
    | Set PAYMENT_GATEWAY=demo, razorpay, stripe or offline. "demo" provides a
    | free, interactive online payment sandbox (simulated UPI, Card, NetBanking)
    | with zero configuration needed.
    */

    'default' => env('PAYMENT_GATEWAY', PaymentGateway::Demo->value),

    /*
    |--------------------------------------------------------------------------
    | Demo Pay (Free Interactive Sandbox)
    |--------------------------------------------------------------------------
    | 100% free sandbox simulation. Does not require external keys or merchant accounts.
    */

    'demo' => [
        'key' => env('DEMO_PAY_KEY', 'demo_key_tripsathi_free'),
        'secret' => env('DEMO_PAY_SECRET', 'demo_secret_tripsathi_free_sandbox'),
        'auto_settle' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Razorpay
    |--------------------------------------------------------------------------
    */

    'razorpay' => [
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
        'api_base' => 'https://api.razorpay.com/v1',
        'timeout' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe
    |--------------------------------------------------------------------------
    */

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'api_base' => 'https://api.stripe.com/v1',
        'timeout' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency used when talking to gateways
    |--------------------------------------------------------------------------
    */

    'currency' => env('TRIPSATHI_CURRENCY', 'INR'),

    /*
    |--------------------------------------------------------------------------
    | Signature verification tolerance (seconds) for replay protection
    |--------------------------------------------------------------------------
    */

    'signature_tolerance' => 300,
];
