import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

export default function Show({ booking, latestPayment }) {
    const isSettled = booking.payment_status === 'paid' || booking.payment_status?.value === 'paid';
    const isConfirmed = booking.status === 'confirmed' || booking.status?.value === 'confirmed';

    const formatMoney = (amount) => {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: booking.currency || 'INR',
            maximumFractionDigits: 0,
        }).format(amount || 0);
    };

    const getStatusBadge = (st) => {
        const val = typeof st === 'object' ? st.value : st;
        const badges = {
            confirmed: 'bg-success',
            completed: 'bg-primary',
            pending: 'bg-warning text-dark',
            cancelled: 'bg-secondary',
            failed: 'bg-danger',
        };
        return badges[val] || 'bg-light text-dark';
    };

    const getPaymentBadge = (pst) => {
        const val = typeof pst === 'object' ? pst.value : pst;
        const badges = {
            paid: 'bg-success',
            pending: 'bg-warning text-dark',
            failed: 'bg-danger',
            refunded: 'bg-info text-dark',
            partially_refunded: 'bg-info text-dark',
        };
        return badges[val] || 'bg-light text-dark';
    };

    return (
        <AppLayout>
            <Head title={`Booking ${booking.booking_number} — Trip Sathi`} />

            <section className="ts-booking-head py-4 bg-light border-bottom">
                <div className="container">
                    <nav aria-label="breadcrumb" className="mb-2">
                        <ol className="breadcrumb mb-0">
                            <li className="breadcrumb-item"><Link href="/account/bookings">My bookings</Link></li>
                            <li className="breadcrumb-item active" aria-current="page">{booking.booking_number}</li>
                        </ol>
                    </nav>

                    <div className="d-flex flex-wrap justify-content-between align-items-end gap-3">
                        <div>
                            <span className="ts-eyebrow text-primary text-uppercase fw-bold small">Booking</span>
                            <h1 className="ts-title h3 mb-1">{booking.title}</h1>
                            <p className="text-muted mb-0">{booking.booking_number} · {booking.type_label}</p>
                        </div>
                        <div className="d-flex flex-wrap gap-2">
                            <span className={`badge ${getStatusBadge(booking.status)}`}>
                                {booking.status_label || booking.status}
                            </span>
                            <span className={`badge ${getPaymentBadge(booking.payment_status)}`}>
                                {booking.payment_status?.label || booking.payment_status}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section className="ts-section py-5">
                <div className="container">
                    {!isSettled ? (
                        <div className="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-3 border-0 shadow-sm">
                            <div>
                                <strong className="d-block">Payment due · {formatMoney(booking.final_amount)}</strong>
                                <span className="small text-muted">Complete payment to confirm this booking. We will hold it while you pay.</span>
                            </div>
                            <Link className="btn btn-accent btn-sm" href={`/bookings/${booking.id}/pay`}>
                                Pay now <i className="bi bi-arrow-right ms-1"></i>
                            </Link>
                        </div>
                    ) : isConfirmed ? (
                        <div className="alert alert-success d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 p-3 border-0 shadow-sm">
                            <div>
                                <strong className="d-block">You are confirmed</strong>
                                <span className="small text-muted">A receipt has been emailed to {booking.customer_email}.</span>
                            </div>
                            <a className="btn btn-soft btn-sm" href={`/bookings/${booking.id}/invoice`} target="_blank" rel="noopener noreferrer">
                                <i className="bi bi-receipt me-1"></i> View receipt
                            </a>
                        </div>
                    ) : null}

                    <div className="row g-4">
                        <div className="col-lg-8">
                            {/* Booking Details Card */}
                            <div className="card shadow-sm border-0 mb-4">
                                <div className="card-header bg-white border-bottom p-3 d-flex align-items-center gap-3">
                                    <span className="badge bg-primary rounded-circle p-2 px-3 fs-6">
                                        <i className="bi bi-calendar-event"></i>
                                    </span>
                                    <div>
                                        <h2 className="h5 mb-0 fw-bold">Booking Details</h2>
                                        <p className="text-muted small mb-0">Everything you booked, in one place.</p>
                                    </div>
                                </div>

                                <div className="card-body p-4">
                                    <div className="row g-3 mb-4 pb-3 border-bottom">
                                        <div className="col-sm-6 col-lg-3">
                                            <div className="small text-muted">Date</div>
                                            <div className="fw-semibold">
                                                {booking.booking_date ? new Date(booking.booking_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : '—'}
                                            </div>
                                        </div>
                                        {booking.end_date && (
                                            <div className="col-sm-6 col-lg-3">
                                                <div className="small text-muted">Until</div>
                                                <div className="fw-semibold">
                                                    {new Date(booking.end_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' })}
                                                </div>
                                            </div>
                                        )}
                                        <div className="col-sm-6 col-lg-3">
                                            <div className="small text-muted">Guests</div>
                                            <div className="fw-semibold">{booking.guests || 1}</div>
                                        </div>
                                        <div className="col-sm-6 col-lg-3">
                                            <div className="small text-muted">Destination</div>
                                            <div className="fw-semibold">{booking.destination?.name || '—'}</div>
                                        </div>
                                    </div>

                                    {/* Items */}
                                    <div className="d-flex flex-column gap-3 mb-4">
                                        {(booking.items || []).map((item) => {
                                            const imageSrc = item.image
                                                ? (item.image.startsWith('http') ? item.image : `/storage/${item.image.replace(/^\//, '')}`)
                                                : '/images/placeholder.svg';

                                            return (
                                                <div key={item.id} className="p-3 border rounded d-flex align-items-center gap-3">
                                                    <img
                                                        src={imageSrc}
                                                        alt={item.name}
                                                        className="rounded object-fit-cover"
                                                        style={{ width: '64px', height: '64px' }}
                                                        onError={(e) => {
                                                            e.target.onerror = null;
                                                            e.target.src = '/images/placeholder.svg';
                                                        }}
                                                    />
                                                    <div className="flex-grow-1">
                                                        <strong className="d-block">{item.name}</strong>
                                                        <div className="small text-muted">
                                                            {item.service_date ? new Date(item.service_date).toLocaleDateString('en-IN', { weekday: 'short', day: 'numeric', month: 'short' }) : ''}
                                                            {item.time_range ? ` · ${item.time_range}` : ''}
                                                            {item.quantity > 1 ? ` · Qty ${item.quantity}` : ''}
                                                        </div>
                                                    </div>
                                                    <div className="fw-bold text-end">
                                                        {formatMoney(item.total_amount)}
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>

                                    {booking.notes && (
                                        <div className="p-3 bg-light rounded">
                                            <strong className="small text-muted d-block">Your note</strong>
                                            <p className="mb-0 small">{booking.notes}</p>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Payments Table Card */}
                            <div className="card shadow-sm border-0">
                                <div className="card-header bg-white border-bottom p-3 d-flex align-items-center gap-3">
                                    <span className="badge bg-primary rounded-circle p-2 px-3 fs-6">
                                        <i className="bi bi-wallet2"></i>
                                    </span>
                                    <div>
                                        <h2 className="h5 mb-0 fw-bold">Payments</h2>
                                        <p className="text-muted small mb-0">Every attempt is recorded for support and refunds.</p>
                                    </div>
                                </div>

                                <div className="card-body p-0">
                                    {(!booking.payments || booking.payments.length === 0) ? (
                                        <p className="text-muted p-4 mb-0">No payment attempts yet.</p>
                                    ) : (
                                        <div className="table-responsive">
                                            <table className="table mb-0 align-middle">
                                                <thead className="table-light">
                                                    <tr>
                                                        <th>Reference</th>
                                                        <th>Method</th>
                                                        <th className="text-end">Amount</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {booking.payments.map((p) => (
                                                        <tr key={p.id}>
                                                            <td>
                                                                <strong>{p.transaction_reference || '—'}</strong>
                                                                <div className="small text-muted">
                                                                    {p.created_at ? new Date(p.created_at).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : ''}
                                                                </div>
                                                            </td>
                                                            <td>{p.method_label || p.payment_method}</td>
                                                            <td className="text-end fw-semibold">{formatMoney(p.amount)}</td>
                                                            <td>
                                                                <span className={`badge ${getPaymentBadge(p.status)}`}>
                                                                    {p.status?.label || p.status}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Right Column Summary */}
                        <div className="col-lg-4">
                            <aside className="sticky-top" style={{ top: '2rem' }}>
                                <div className="card shadow-sm border-0 mb-4">
                                    <div className="card-body p-4">
                                        <span className="text-primary text-uppercase fw-bold small">Price Summary</span>

                                        <div className="d-flex flex-column gap-2 mt-3 mb-3">
                                            <div className="d-flex justify-content-between">
                                                <span className="text-muted">Subtotal</span>
                                                <span className="fw-semibold">{formatMoney(booking.subtotal)}</span>
                                            </div>

                                            {parseInt(booking.discount, 10) > 0 && (
                                                <div className="d-flex justify-content-between text-success">
                                                    <span>Discount</span>
                                                    <span>-{formatMoney(booking.discount)}</span>
                                                </div>
                                            )}

                                            <div className="d-flex justify-content-between">
                                                <span className="text-muted">Taxes &amp; fees</span>
                                                <span className="fw-semibold">{formatMoney(booking.tax)}</span>
                                            </div>

                                            <div className="d-flex justify-content-between border-top border-bottom py-2 my-1 fw-bold fs-5">
                                                <span>Total</span>
                                                <span className="text-primary">{formatMoney(booking.final_amount)}</span>
                                            </div>
                                        </div>

                                        {!isSettled && (
                                            <Link className="btn btn-accent w-100 mb-2" href={`/bookings/${booking.id}/pay`}>
                                                <i className="bi bi-credit-card me-1"></i> Pay now
                                            </Link>
                                        )}

                                        <a className="btn btn-soft w-100 mb-2" href={`/bookings/${booking.id}/invoice`} target="_blank" rel="noopener noreferrer">
                                            <i className="bi bi-download me-1"></i> View / print receipt
                                        </a>

                                        <Link className="btn btn-outline-secondary w-100" href={`/contact?subject=Booking%20${encodeURIComponent(booking.booking_number)}`}>
                                            <i className="bi bi-life-preserver me-1"></i> Get help
                                        </Link>
                                    </div>
                                </div>

                                <div className="card shadow-sm border-0">
                                    <div className="card-body p-4">
                                        <span className="text-primary text-uppercase fw-bold small">Booked by</span>
                                        <p className="fw-semibold mb-1 mt-2">{booking.customer_name}</p>
                                        <p className="small text-muted mb-1">{booking.customer_email}</p>
                                        <p className="small text-muted mb-0">{booking.customer_phone || '—'}</p>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}
