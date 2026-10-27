import React, { useEffect, useRef } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function Checkout({ booking, result }) {
    const isRazorpay = result?.clientScript?.includes('checkout.razorpay.com');
    const isStripe = result?.clientScript?.includes('js.stripe.com');
    const razorpayInstance = useRef(null);

    const submitCallback = (payload) => {
        router.post(`/payments/${booking.id}/callback`, payload);
    };

    useEffect(() => {
        if (!result?.clientScript) return;

        // Load script dynamically
        const script = document.createElement('script');
        script.src = result.clientScript;
        script.async = true;

        script.onload = () => {
            if (isRazorpay && window.Razorpay) {
                const options = {
                    ...result.clientOptions,
                    handler: function (response) {
                        submitCallback({
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_signature: response.razorpay_signature,
                        });
                    },
                    modal: {
                        ondismiss: function () {
                            // User closed modal
                        },
                    },
                };

                const rzp = new window.Razorpay(options);
                rzp.on('payment.failed', function (response) {
                    submitCallback({
                        razorpay_payment_id: response.error?.metadata?.payment_id,
                        razorpay_order_id: response.error?.metadata?.order_id,
                        razorpay_signature: '',
                        failure_reason: response.error?.description || 'Payment failed.',
                    });
                });

                razorpayInstance.current = rzp;
                rzp.open();
            } else if (isStripe && result.clientOptions?.redirect) {
                window.location.replace(result.clientOptions.redirect);
            }
        };

        document.body.appendChild(script);

        return () => {
            if (script.parentNode) {
                script.parentNode.removeChild(script);
            }
        };
    }, [result, isRazorpay, isStripe, booking]);

    const openRazorpay = () => {
        if (razorpayInstance.current) {
            razorpayInstance.current.open();
        }
    };

    const formatMoney = (amount) => {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: booking.currency || 'INR',
            maximumFractionDigits: 0,
        }).format(amount || 0);
    };

    return (
        <AppLayout>
            <Head title="Secure Payment — Trip Sathi" />

            <section className="ts-section py-5">
                <div className="container" style={{ maxWidth: '640px' }}>
                    <div className="card shadow-sm border-0 text-center">
                        <div className="card-body p-4 p-lg-5">
                            <div className="fs-1 text-primary mb-3">
                                <i className="bi bi-shield-lock"></i>
                            </div>
                            <h1 className="h4 fw-bold mb-2">Taking you to secure payment</h1>
                            <p className="text-muted mb-4">
                                Booking {booking.booking_number} · {formatMoney(booking.final_amount)}
                            </p>

                            {!result?.clientScript ? (
                                <Link className="btn btn-accent" href={`/bookings/${booking.id}/pay`}>
                                    Return to payment
                                </Link>
                            ) : isRazorpay ? (
                                <div>
                                    <p className="small text-muted mb-3">
                                        If the payment window does not open automatically, click the button below.
                                    </p>
                                    <button className="btn btn-accent px-4 py-2" type="button" onClick={openRazorpay}>
                                        Open Razorpay checkout
                                    </button>
                                </div>
                            ) : isStripe ? (
                                <div>
                                    <p className="small text-muted mb-3">Redirecting to Stripe…</p>
                                    <a
                                        className="btn btn-accent px-4 py-2"
                                        href={result.clientOptions?.redirect || `/bookings/${booking.id}/pay`}
                                    >
                                        Continue to Stripe
                                    </a>
                                </div>
                            ) : (
                                <Link className="btn btn-accent" href={`/bookings/${booking.id}/pay`}>
                                    Return to payment
                                </Link>
                            )}
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}
