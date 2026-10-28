import React, { useEffect, useRef, useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function Checkout({ booking, result }) {
    const isRazorpay = result?.clientScript?.includes('checkout.razorpay.com');
    const isStripe = result?.clientScript?.includes('js.stripe.com');
    const isDemo = result?.clientOptions?.gateway === 'demo' || result?.clientScript?.includes('demo');
    const razorpayInstance = useRef(null);

    // Demo payment state
    const [activeTab, setActiveTab] = useState('instant'); // 'instant' | 'upi' | 'card' | 'netbanking'
    const [upiId, setUpiId] = useState('traveller@okaxis');
    const [selectedUpiApp, setSelectedUpiApp] = useState('GPay');
    const [selectedBank, setSelectedBank] = useState('HDFC');
    const [cardNumber, setCardNumber] = useState('4242 •••• •••• 4242');
    const [cardHolder, setCardHolder] = useState(booking?.customer_name || 'Traveler');
    const [cardExpiry, setCardExpiry] = useState('12/28');
    const [cardCvv, setCardCvv] = useState('123');
    const [isSimulating, setIsSimulating] = useState(false);
    const [simulationStep, setSimulationStep] = useState('');

    const submitCallback = (payload) => {
        router.post(`/bookings/${booking.id}/callback`, payload);
    };

    useEffect(() => {
        if (!result?.clientScript || isDemo) return;

        // Load external script dynamically for Razorpay / Stripe
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
    }, [result, isRazorpay, isStripe, isDemo, booking]);

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

    const handleDemoPayment = (method = 'instant') => {
        if (isSimulating) return;

        setIsSimulating(true);
        setSimulationStep('Connecting to Demo Gateway…');

        const timer1 = setTimeout(() => {
            setSimulationStep(`Simulating ${method.toUpperCase()} authorization…`);
        }, 350);

        const timer2 = setTimeout(() => {
            setSimulationStep('Verifying cryptographic HMAC signature…');
        }, 700);

        const timer3 = setTimeout(() => {
            const randomCode = Math.random().toString(36).substring(2, 6).toUpperCase();
            const paymentId = `DEMO-PAY-${Date.now().toString(36).toUpperCase()}-${randomCode}`;

            submitCallback({
                demo_order_id: result.clientOptions.order_id,
                demo_payment_id: paymentId,
                auth_token: result.clientOptions.auth_token,
                amount: result.clientOptions.amount || booking.final_amount,
                currency: result.clientOptions.currency || booking.currency || 'INR',
                payment_method: `demo_${method}`,
                status: 'success',
            });
        }, 1100);

        return () => {
            clearTimeout(timer1);
            clearTimeout(timer2);
            clearTimeout(timer3);
        };
    };

    const handleDemoFailure = () => {
        if (isSimulating) return;

        setIsSimulating(true);
        setSimulationStep('Simulating bank decline test…');

        setTimeout(() => {
            const randomCode = Math.random().toString(36).substring(2, 6).toUpperCase();
            submitCallback({
                demo_order_id: result.clientOptions.order_id,
                demo_payment_id: `DEMO-FAIL-${Date.now().toString(36).toUpperCase()}-${randomCode}`,
                auth_token: result.clientOptions.auth_token,
                amount: result.clientOptions.amount || booking.final_amount,
                currency: result.clientOptions.currency || booking.currency || 'INR',
                payment_method: 'demo_test',
                status: 'failed',
                failure_reason: 'Simulated decline: Bank authorization declined for demonstration.',
            });
        }, 800);
    };

    const banks = [
        { id: 'HDFC', name: 'HDFC Bank', icon: 'bi-bank' },
        { id: 'SBI', name: 'State Bank of India', icon: 'bi-bank2' },
        { id: 'ICICI', name: 'ICICI Bank', icon: 'bi-building' },
        { id: 'AXIS', name: 'Axis Bank', icon: 'bi-bank' },
        { id: 'KOTAK', name: 'Kotak Mahindra', icon: 'bi-bank2' },
        { id: 'PNB', name: 'Punjab National Bank', icon: 'bi-building' },
    ];

    const upiApps = [
        { name: 'GPay', icon: 'bi-google', color: '#4285F4' },
        { name: 'PhonePe', icon: 'bi-phone', color: '#5F259F' },
        { name: 'Paytm', icon: 'bi-wallet2', color: '#00BAF2' },
        { name: 'BHIM', icon: 'bi-upc-scan', color: '#FF7A2F' },
    ];

    return (
        <AppLayout>
            <Head title="Secure Payment — Trip Sathi" />

            <section className="ts-section py-4 py-md-5">
                <div className="container" style={{ maxWidth: isDemo ? '760px' : '640px' }}>
                    {isDemo ? (
                        /* Apple Glass Interactive Demo Gateway Simulator */
                        <div
                            className="card border-0 shadow-lg rounded-4 overflow-hidden position-relative"
                            style={{
                                background: 'rgba(255, 255, 255, 0.92)',
                                backdropFilter: 'blur(20px)',
                                WebkitBackdropFilter: 'blur(20px)',
                                border: '1px solid rgba(255, 122, 47, 0.2)',
                            }}
                        >
                            {/* Processing Overlay */}
                            {isSimulating && (
                                <div
                                    className="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center p-4"
                                    style={{
                                        background: 'rgba(255, 255, 255, 0.94)',
                                        backdropFilter: 'blur(16px)',
                                        zIndex: 1050,
                                    }}
                                >
                                    <div
                                        className="spinner-border text-primary mb-3"
                                        role="status"
                                        style={{ width: '3.5rem', height: '3.5rem' }}
                                    >
                                        <span className="visually-hidden">Authorizing…</span>
                                    </div>
                                    <h4 className="fw-bold mb-2">Simulating Demo Payment</h4>
                                    <p className="text-muted small mb-0">{simulationStep}</p>
                                    <div className="mt-3 badge bg-light text-dark border px-3 py-2 small">
                                        <i className="bi bi-shield-lock-fill text-success me-1"></i> Testing Cryptographic Callback Verification
                                    </div>
                                </div>
                            )}

                            {/* Header */}
                            <div
                                className="p-4 text-white position-relative"
                                style={{
                                    background: 'linear-gradient(135deg, #FF7A2F 0%, #0F4C46 100%)',
                                }}
                            >
                                <div className="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                    <span className="badge rounded-pill bg-white text-dark px-3 py-2 fw-bold text-uppercase small shadow-sm">
                                        <i className="bi bi-shield-check text-success me-1"></i> Demo Sandbox · 100% Free
                                    </span>
                                    <span className="small text-white-50">
                                        Order: <code className="text-white fw-bold">{result.clientOptions?.order_id || booking.booking_number}</code>
                                    </span>
                                </div>

                                <div className="d-flex justify-content-between align-items-end flex-wrap gap-3 mt-3">
                                    <div>
                                        <span className="small text-white-50 d-block text-uppercase fw-semibold">Amount to Pay</span>
                                        <h2 className="display-6 fw-bold mb-0">{formatMoney(booking.final_amount)}</h2>
                                    </div>
                                    <div className="text-end">
                                        <span className="small text-white-50 d-block">Booking Reference</span>
                                        <span className="fw-bold fs-5">{booking.booking_number}</span>
                                        <div className="small text-white-50">{booking.customer_name}</div>
                                    </div>
                                </div>
                            </div>

                            {/* Payment Methods Navigation */}
                            <div className="bg-light border-bottom p-2 px-3">
                                <ul className="nav nav-pills nav-fill gap-1" role="tablist">
                                    <li className="nav-item">
                                        <button
                                            type="button"
                                            className={`nav-link py-2 px-2 small fw-semibold rounded-3 ${activeTab === 'instant' ? 'active bg-primary text-white shadow-sm' : 'text-dark'}`}
                                            onClick={() => setActiveTab('instant')}
                                        >
                                            <i className="bi bi-lightning-charge-fill me-1"></i> 1-Click Pay
                                        </button>
                                    </li>
                                    <li className="nav-item">
                                        <button
                                            type="button"
                                            className={`nav-link py-2 px-2 small fw-semibold rounded-3 ${activeTab === 'upi' ? 'active bg-primary text-white shadow-sm' : 'text-dark'}`}
                                            onClick={() => setActiveTab('upi')}
                                        >
                                            <i className="bi bi-qr-code me-1"></i> Demo UPI
                                        </button>
                                    </li>
                                    <li className="nav-item">
                                        <button
                                            type="button"
                                            className={`nav-link py-2 px-2 small fw-semibold rounded-3 ${activeTab === 'card' ? 'active bg-primary text-white shadow-sm' : 'text-dark'}`}
                                            onClick={() => setActiveTab('card')}
                                        >
                                            <i className="bi bi-credit-card-2-front me-1"></i> Test Card
                                        </button>
                                    </li>
                                    <li className="nav-item">
                                        <button
                                            type="button"
                                            className={`nav-link py-2 px-2 small fw-semibold rounded-3 ${activeTab === 'netbanking' ? 'active bg-primary text-white shadow-sm' : 'text-dark'}`}
                                            onClick={() => setActiveTab('netbanking')}
                                        >
                                            <i className="bi bi-bank me-1"></i> Net Banking
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            {/* Tab Body */}
                            <div className="card-body p-4 p-md-5">
                                {/* TAB 1: 1-Click Instant Pay */}
                                {activeTab === 'instant' && (
                                    <div className="text-center py-2">
                                        <div
                                            className="d-inline-flex align-items-center justify-content-center rounded-circle p-3 mb-3 shadow-sm"
                                            style={{
                                                width: '4.5rem',
                                                height: '4.5rem',
                                                background: 'linear-gradient(135deg, rgba(255,122,47,0.15) 0%, rgba(15,76,70,0.15) 100%)',
                                                color: '#FF7A2F',
                                            }}
                                        >
                                            <i className="bi bi-lightning-charge-fill fs-1"></i>
                                        </div>

                                        <h3 className="h5 fw-bold mb-2">Simulate Instant Payment</h3>
                                        <p className="text-muted small mb-4" style={{ maxWidth: '440px', margin: '0 auto' }}>
                                            One-click testing mode. Automatically creates a simulated payment record, performs HMAC cryptographic verification, confirms the booking, and queues confirmation emails.
                                        </p>

                                        <div className="p-3 mb-4 rounded-3 border bg-light text-start small">
                                            <div className="d-flex justify-content-between mb-1">
                                                <span className="text-muted">Total Due:</span>
                                                <strong className="text-primary">{formatMoney(booking.final_amount)}</strong>
                                            </div>
                                            <div className="d-flex justify-content-between mb-1">
                                                <span className="text-muted">Customer:</span>
                                                <span>{booking.customer_name}</span>
                                            </div>
                                            <div className="d-flex justify-content-between">
                                                <span className="text-muted">Sandbox Mode:</span>
                                                <span className="text-success fw-semibold"><i className="bi bi-check-circle-fill me-1"></i> Active</span>
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            className="btn btn-accent btn-lg w-100 py-3 fw-bold shadow-sm"
                                            onClick={() => handleDemoPayment('instant')}
                                            disabled={isSimulating}
                                        >
                                            <i className="bi bi-check-circle-fill me-2"></i> Confirm Demo Payment ({formatMoney(booking.final_amount)})
                                        </button>
                                    </div>
                                )}

                                {/* TAB 2: Demo UPI */}
                                {activeTab === 'upi' && (
                                    <div>
                                        <div className="row g-4 align-items-center mb-4">
                                            <div className="col-sm-5 text-center">
                                                {/* Mock QR Code */}
                                                <div
                                                    className="p-3 bg-white rounded-3 border shadow-sm d-inline-block position-relative"
                                                    style={{ width: '170px', height: '170px' }}
                                                >
                                                    <div className="w-100 h-100 d-flex flex-column align-items-center justify-content-center border border-dashed rounded-2 p-2">
                                                        <i className="bi bi-qr-code text-dark" style={{ fontSize: '5rem' }}></i>
                                                        <span className="badge bg-primary text-white" style={{ fontSize: '0.65rem' }}>
                                                            SCAN WITH ANY APP
                                                        </span>
                                                    </div>
                                                </div>
                                                <div className="small text-muted mt-2">Simulated UPI QR Code</div>
                                            </div>

                                            <div className="col-sm-7">
                                                <label className="form-label small fw-semibold text-muted mb-2">Select UPI App to Simulate</label>
                                                <div className="row g-2 mb-3">
                                                    {upiApps.map((app) => (
                                                        <div key={app.name} className="col-6">
                                                            <button
                                                                type="button"
                                                                className={`btn btn-sm w-100 p-2 text-start d-flex align-items-center gap-2 border rounded-3 ${selectedUpiApp === app.name ? 'border-primary bg-primary-subtle text-primary fw-bold' : 'bg-white'}`}
                                                                onClick={() => setSelectedUpiApp(app.name)}
                                                            >
                                                                <i className={`bi ${app.icon}`} style={{ color: app.color }}></i>
                                                                <span>{app.name}</span>
                                                            </button>
                                                        </div>
                                                    ))}
                                                </div>

                                                <label className="form-label small fw-semibold text-muted mb-1">Simulated UPI ID</label>
                                                <div className="input-group input-group-sm mb-3">
                                                    <span className="input-group-text bg-light"><i className="bi bi-at"></i></span>
                                                    <input
                                                        type="text"
                                                        className="form-control"
                                                        value={upiId}
                                                        onChange={(e) => setUpiId(e.target.value)}
                                                        placeholder="username@upi"
                                                    />
                                                </div>
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            className="btn btn-accent btn-lg w-100 py-3 fw-bold shadow-sm"
                                            onClick={() => handleDemoPayment('upi')}
                                            disabled={isSimulating}
                                        >
                                            <i className="bi bi-phone-fill me-2"></i> Approve {selectedUpiApp} Payment ({formatMoney(booking.final_amount)})
                                        </button>
                                    </div>
                                )}

                                {/* TAB 3: Test Card */}
                                {activeTab === 'card' && (
                                    <div>
                                        {/* Card Visual */}
                                        <div
                                            className="p-4 rounded-4 text-white mb-4 shadow-sm position-relative overflow-hidden"
                                            style={{
                                                background: 'linear-gradient(135deg, #0F4C46 0%, #166E65 50%, #FF7A2F 100%)',
                                            }}
                                        >
                                            <div className="d-flex justify-content-between align-items-center mb-4">
                                                <span className="badge bg-white text-dark px-2 py-1 small fw-bold">TEST CARD</span>
                                                <i className="bi bi-wifi fs-4 opacity-75"></i>
                                            </div>
                                            <div className="fs-4 fw-mono letter-spacing-2 mb-3 text-white fw-bold">
                                                {cardNumber}
                                            </div>
                                            <div className="d-flex justify-content-between align-items-end small">
                                                <div>
                                                    <span className="d-block text-white-50 text-uppercase" style={{ fontSize: '0.65rem' }}>Cardholder</span>
                                                    <strong className="text-uppercase">{cardHolder}</strong>
                                                </div>
                                                <div className="text-end">
                                                    <span className="d-block text-white-50 text-uppercase" style={{ fontSize: '0.65rem' }}>Expires</span>
                                                    <strong>{cardExpiry}</strong>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Card Inputs */}
                                        <div className="row g-3 mb-4">
                                            <div className="col-12">
                                                <label className="form-label small fw-semibold text-muted mb-1">Card Number</label>
                                                <div className="input-group">
                                                    <span className="input-group-text bg-light"><i className="bi bi-credit-card"></i></span>
                                                    <input
                                                        type="text"
                                                        className="form-control"
                                                        value={cardNumber}
                                                        onChange={(e) => setCardNumber(e.target.value)}
                                                    />
                                                </div>
                                            </div>
                                            <div className="col-md-6">
                                                <label className="form-label small fw-semibold text-muted mb-1">Cardholder Name</label>
                                                <input
                                                    type="text"
                                                    className="form-control"
                                                    value={cardHolder}
                                                    onChange={(e) => setCardHolder(e.target.value)}
                                                />
                                            </div>
                                            <div className="col-6 col-md-3">
                                                <label className="form-label small fw-semibold text-muted mb-1">Expiry</label>
                                                <input
                                                    type="text"
                                                    className="form-control"
                                                    value={cardExpiry}
                                                    onChange={(e) => setCardExpiry(e.target.value)}
                                                />
                                            </div>
                                            <div className="col-6 col-md-3">
                                                <label className="form-label small fw-semibold text-muted mb-1">CVV</label>
                                                <input
                                                    type="text"
                                                    className="form-control"
                                                    value={cardCvv}
                                                    onChange={(e) => setCardCvv(e.target.value)}
                                                />
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            className="btn btn-accent btn-lg w-100 py-3 fw-bold shadow-sm"
                                            onClick={() => handleDemoPayment('card')}
                                            disabled={isSimulating}
                                        >
                                            <i className="bi bi-credit-card-fill me-2"></i> Pay {formatMoney(booking.final_amount)} with Test Card
                                        </button>
                                    </div>
                                )}

                                {/* TAB 4: Net Banking */}
                                {activeTab === 'netbanking' && (
                                    <div>
                                        <p className="small text-muted mb-3">Select a bank to simulate demo net banking transfer:</p>
                                        <div className="row g-2 mb-4">
                                            {banks.map((bank) => (
                                                <div key={bank.id} className="col-sm-6">
                                                    <button
                                                        type="button"
                                                        className={`btn w-100 p-3 text-start d-flex align-items-center gap-3 border rounded-3 ${selectedBank === bank.id ? 'border-primary bg-primary-subtle text-primary fw-bold shadow-sm' : 'bg-white'}`}
                                                        onClick={() => setSelectedBank(bank.id)}
                                                    >
                                                        <i className={`bi ${bank.icon} fs-5 text-primary`}></i>
                                                        <div>
                                                            <div className="fw-semibold small">{bank.name}</div>
                                                            <div className="text-muted" style={{ fontSize: '0.7rem' }}>Demo Retail Banking</div>
                                                        </div>
                                                    </button>
                                                </div>
                                            ))}
                                        </div>

                                        <button
                                            type="button"
                                            className="btn btn-accent btn-lg w-100 py-3 fw-bold shadow-sm"
                                            onClick={() => handleDemoPayment('netbanking')}
                                            disabled={isSimulating}
                                        >
                                            <i className="bi bi-bank2 me-2"></i> Authorize via {banks.find((b) => b.id === selectedBank)?.name || 'Net Banking'}
                                        </button>
                                    </div>
                                )}

                                {/* Testing Decline Simulation & Return */}
                                <div className="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <Link className="btn btn-link text-muted p-0 small text-decoration-none" href={`/bookings/${booking.id}/pay`}>
                                        <i className="bi bi-arrow-left me-1"></i> Cancel and return
                                    </Link>

                                    <button
                                        type="button"
                                        className="btn btn-outline-danger btn-sm"
                                        onClick={handleDemoFailure}
                                        disabled={isSimulating}
                                    >
                                        <i className="bi bi-x-circle me-1"></i> Test Payment Failure / Decline
                                    </button>
                                </div>
                            </div>
                        </div>
                    ) : (
                        /* Standard Razorpay / Stripe Redirect Container */
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
                    )}
                </div>
            </section>
        </AppLayout>
    );
}
