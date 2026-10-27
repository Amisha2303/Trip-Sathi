import React from 'react';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function Pay({
    booking,
    latestPayment,
    gatewayName,
    isOnline,
    isConfigured,
    currency,
    taxPercent,
}) {
    const { appSettings, flash } = usePage().props;
    const { post, processing } = useForm();

    const formatMoney = (amount) => {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: currency || 'INR',
            maximumFractionDigits: 0,
        }).format(amount || 0);
    };

    const handleInitiate = (e) => {
        e.preventDefault();
        post(`/bookings/${booking.id}/pay`);
    };

    return (
        <AppLayout>
            <Head title={`Pay for ${booking.booking_number} — Trip Sathi`} />

            <section className="ts-booking-head py-4 bg-light border-bottom">
                <div className="container">
                    <nav aria-label="breadcrumb" className="mb-2">
                        <ol className="breadcrumb mb-0">
                            <li className="breadcrumb-item"><Link href="/account/bookings">My bookings</Link></li>
                            <li className="breadcrumb-item"><Link href={`/bookings/${booking.id}`}>{booking.booking_number}</Link></li>
                            <li className="breadcrumb-item active" aria-current="page">Payment</li>
                        </ol>
                    </nav>

                    <span className="ts-eyebrow text-primary text-uppercase fw-bold small">Almost there</span>
                    <h1 className="ts-title h3 mb-1">Complete your payment</h1>
                    <p className="text-muted mb-0">Booking {booking.booking_number} is held while you pay.</p>
                </div>
            </section>

            <section className="ts-section py-5">
                <div className="container">
                    {flash?.error && (
                        <div className="alert alert-danger mb-4" role="alert">{flash.error}</div>
                    )}
                    {flash?.success && (
                        <div className="alert alert-success mb-4" role="alert">{flash.success}</div>
                    )}

                    <div className="row g-4">
                        <div className="col-lg-7">
                            <div className="card shadow-sm border-0 mb-4">
                                <div className="card-header bg-white border-bottom p-3 d-flex align-items-center gap-3">
                                    <span className="badge bg-primary rounded-circle p-2 px-3 fs-6">
                                        <i className="bi bi-credit-card"></i>
                                    </span>
                                    <div>
                                        <h2 className="h5 mb-0 fw-bold">Pay {formatMoney(booking.final_amount)}</h2>
                                        <p className="text-muted small mb-0">
                                            {gatewayName} {!isConfigured && '· not configured yet'}
                                        </p>
                                    </div>
                                </div>

                                <div className="card-body p-4">
                                    {!isConfigured ? (
                                        <div className="alert alert-warning">
                                            Online card payments are not configured on this site yet. Your booking is safe and
                                            our team can collect payment directly. Quote <strong>{booking.booking_number}</strong> when you contact us.
                                        </div>
                                    ) : isOnline ? (
                                        <p className="text-muted">
                                            You will be redirected to {gatewayName} to pay securely. Your card details never touch our servers.
                                        </p>
                                    ) : (
                                        <p className="text-muted mb-3">
                                            Pay offline by bank transfer or in person. Generate a payment reference below, then share it with our team so we can mark the booking confirmed.
                                        </p>
                                    )}

                                    <form onSubmit={handleInitiate}>
                                        <button
                                            className="btn btn-accent btn-lg w-100 shadow-sm"
                                            type="submit"
                                            disabled={processing}
                                        >
                                            {isOnline && isConfigured ? (
                                                <>
                                                    <i className="bi bi-lock-fill me-1"></i> Pay securely with {gatewayName}
                                                </>
                                            ) : isOnline ? (
                                                <>
                                                    <i className="bi bi-arrow-repeat me-1"></i> Try {gatewayName} payment
                                                </>
                                            ) : (
                                                <>
                                                    <i className="bi bi-receipt me-1"></i> Generate offline payment reference
                                                </>
                                            )}
                                        </button>
                                    </form>

                                    {latestPayment && (
                                        <div className="mt-4 p-3 bg-light rounded d-flex justify-content-between align-items-center">
                                            <div>
                                                <span className="small text-muted d-block">Payment reference</span>
                                                <strong>{latestPayment.transaction_reference || 'Pending generation'}</strong>
                                            </div>
                                            <span className="badge bg-secondary">
                                                {latestPayment.status?.label || latestPayment.status}
                                            </span>
                                        </div>
                                    )}

                                    <div className="mt-4 small text-muted border-top pt-3">
                                        <p className="mb-1"><i className="bi bi-telephone me-1"></i> {appSettings?.contact_phone || '+91 9904530641'}</p>
                                        <p className="mb-0"><i className="bi bi-envelope me-1"></i> {appSettings?.contact_email || 'hello@tripsathi.com'}</p>
                                    </div>
                                </div>
                            </div>

                            <div className="card shadow-sm border-0">
                                <div className="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div>
                                        <h3 className="h6 fw-bold mb-1">Need to change something?</h3>
                                        <p className="text-muted small mb-0">Contact us before paying and we will adjust the booking.</p>
                                    </div>
                                    <Link className="btn btn-soft" href={`/contact?subject=Booking%20${encodeURIComponent(booking.booking_number)}`}>
                                        <i className="bi bi-chat-dots me-1"></i> Message support
                                    </Link>
                                </div>
                            </div>
                        </div>

                        {/* Price summary */}
                        <div className="col-lg-5">
                            <aside className="card shadow-sm border-0 sticky-top" style={{ top: '2rem' }}>
                                <div className="card-body p-4">
                                    <span className="text-primary text-uppercase fw-bold small">{booking.type_label}</span>
                                    <h3 className="h5 fw-bold mb-1">{booking.title}</h3>
                                    <p className="text-muted small mb-3">
                                        {booking.booking_date ? new Date(booking.booking_date).toLocaleDateString('en-IN', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' }) : ''}
                                        {booking.destination ? ` · ${booking.destination.name}` : ''}
                                    </p>

                                    <div className="border-top pt-3 mb-3 d-flex flex-column gap-2">
                                        {(booking.items || []).map((item) => (
                                            <div key={item.id} className="d-flex justify-content-between">
                                                <span>{item.name} <span className="text-muted">× {item.quantity}</span></span>
                                                <span className="fw-semibold">{formatMoney(item.total_amount)}</span>
                                            </div>
                                        ))}

                                        <div className="d-flex justify-content-between border-top pt-2">
                                            <span className="text-muted">Subtotal</span>
                                            <span>{formatMoney(booking.subtotal)}</span>
                                        </div>

                                        {parseInt(booking.discount, 10) > 0 && (
                                            <div className="d-flex justify-content-between text-success">
                                                <span>Discount</span>
                                                <span>-{formatMoney(booking.discount)}</span>
                                            </div>
                                        )}

                                        <div className="d-flex justify-content-between">
                                            <span className="text-muted">Taxes &amp; fees</span>
                                            <span>{formatMoney(booking.tax)}</span>
                                        </div>

                                        <div className="d-flex justify-content-between border-top border-bottom py-2 my-1 fw-bold fs-5">
                                            <span>Total due</span>
                                            <span className="text-primary">{formatMoney(booking.final_amount)}</span>
                                        </div>
                                    </div>

                                    <ul className="list-unstyled text-muted small d-flex flex-column gap-2 mb-0">
                                        <li className="d-flex align-items-center gap-2">
                                            <i className="bi bi-shield-check text-success"></i>
                                            <span>Your booking is held until payment</span>
                                        </li>
                                        <li className="d-flex align-items-center gap-2">
                                            <i className="bi bi-envelope-check text-info"></i>
                                            <span>Receipt emailed after confirmation</span>
                                        </li>
                                        <li className="d-flex align-items-center gap-2">
                                            <i className="bi bi-arrow-counterclockwise text-secondary"></i>
                                            <span>Clear cancellation terms</span>
                                        </li>
                                    </ul>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}
