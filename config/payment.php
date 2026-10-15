<?php

declare(strict_types=1);

use App\Enums\PaymentGateway;

return [

    /*
    |--------------------------------------------------------------------------
    | Default gateway
    |--------------------------------------------------------------------------
    | Set PAYMENT_GATEWAY=razorpay, stripe or offline. "offline" keeps the whole
    | payment lifecycle (pending -> paid/refunded) intact without contacting an
    | external provider, which is what local development and CI use.
    */

    'default' => env('PAYMENT_GATEWAY', PaymentGateway::Offline->value),

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
