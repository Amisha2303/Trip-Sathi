@extends('layouts.app')

@section('title', 'Secure payment — Trip Sathi')

@section('content')
    <section class="ts-section">
        <div class="container" style="max-width: 640px;">
            <div class="ts-booking-card text-center">
                <div class="p-4 p-lg-5">
                    <div class="ts-booking-spinner mb-3"><i class="bi bi-shield-lock"></i></div>
                    <h1 class="ts-title h4 mb-2">Taking you to secure payment</h1>
                    <p class="text-muted mb-4">
                        Booking {{ $booking->booking_number }} · {{ \App\Support\Money::format((int) $booking->final_amount) }}
                    </p>

                    @if ($result->clientScript === null)
                        <a class="btn btn-accent" href="{{ route('bookings.pay', $booking) }}">Return to payment</a>
                    @elseif (str_contains($result->clientScript, 'checkout.razorpay.com'))
                        <p class="small text-muted">If the payment window does not open, check that pop-ups are allowed for this site.</p>
                        <button class="btn btn-accent" type="button" id="razorpay-open">Open Razorpay checkout</button>
                    @elseif (str_contains($result->clientScript, 'js.stripe.com'))
                        <p class="small text-muted">Redirecting to Stripe…</p>
                        <a class="btn btn-accent" id="stripe-open"
                            href="{{ $result->clientOptions['redirect'] ?? route('bookings.pay', $booking) }}">
                            Continue to Stripe
                        </a>
                    @else
                        <a class="btn btn-accent" href="{{ route('bookings.pay', $booking) }}">Return to payment</a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('payments.callback', $booking) }}" id="payment-callback" class="d-none">
        @csrf
        <div data-payment-fields></div>
    </form>
@endsection

@push('scripts')
    @if ($result->clientScript !== null)
        <script src="{{ $result->clientScript }}"></script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const callback = document.getElementById('payment-callback');
            const fields = callback?.querySelector('[data-payment-fields]');
            const options = @json($result->clientOptions);

            const submitCallback = (payload) => {
                if (!callback || !fields) return;

                fields.innerHTML = '';
                Object.entries(payload).forEach(([key, value]) => {
                    if (value === null || value === undefined) return;
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = String(value);
                    fields.appendChild(input);
                });

                callback.submit();
            };

            if (typeof Razorpay !== 'undefined') {
                window.TripSathi = window.TripSathi || {};
                window.TripSathi.onRazorpaySuccess = (response) => submitCallback({
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_signature: response.razorpay_signature,
                });

                const checkout = new Razorpay(options);
                checkout.on('payment.failed', (response) => {
                    submitCallback({
                        razorpay_payment_id: response.error?.metadata?.payment_id,
                        razorpay_order_id: response.error?.metadata?.order_id,
                        razorpay_signature: '',
                        failure_reason: response.error?.description || 'Payment failed.',
                    });
                });
                checkout.open();

                document.getElementById('razorpay-open')?.addEventListener('click', () => checkout.open());
            }

            const stripe = document.getElementById('stripe-open');
            if (stripe && '{{ $result->clientOptions['redirect'] ?? '' }}' !== '') {
                window.location.replace(stripe.href);
            }
        });
    </script>
@endpush
