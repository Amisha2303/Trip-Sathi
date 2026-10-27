import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Show({
    booking,
    statuses = {},
    paymentStatuses = {},
    transitions = {},
}) {
    const { data, setData, put, processing, errors } = useForm({
        status: typeof booking?.status === 'object' ? booking.status.value : booking?.status,
        internal_notes: booking?.internal_notes || '',
    });

    const handleUpdate = (e) => {
        e.preventDefault();
        put(`/admin/bookings/${booking.id}`, { preserveScroll: true });
    };

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
        <AdminLayout
            title={`Booking ${booking.booking_number}`}
            heading={`Booking · ${booking.booking_number}`}
        >
            <Head title={`Booking ${booking.booking_number} — Admin — Trip Sathi`} />

            <div className="row g-4">
                <div className="col-lg-8">
                    {/* Items and Experience */}
                    <div className="card shadow-sm border-0 mb-4">
                        <div className="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span className="text-primary text-uppercase fw-bold small">{booking.type_label}</span>
                                <h2 className="h5 mb-0 fw-bold">{booking.title}</h2>
                            </div>
                            <div className="d-flex gap-2">
                                <span className={`badge ${getStatusBadge(booking.status)}`}>
                                    {booking.status_label || (typeof booking.status === 'object' ? booking.status.label : booking.status)}
                                </span>
                                <span className={`badge ${getPaymentBadge(booking.payment_status)}`}>
                                    {booking.payment_status?.label || booking.payment_status}
                                </span>
                            </div>
                        </div>

                        <div className="card-body p-4">
                            <div className="row g-3 mb-4 pb-3 border-bottom">
                                <div className="col-sm-4">
                                    <span className="small text-muted d-block">Booking Date</span>
                                    <strong>{booking.booking_date ? new Date(booking.booking_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : '—'}</strong>
                                </div>
                                <div className="col-sm-4">
                                    <span className="small text-muted d-block">Destination</span>
                                    <strong>{booking.destination?.name || '—'}</strong>
                                </div>
                                <div className="col-sm-4">
                                    <span className="small text-muted d-block">Guests / Quantity</span>
                                    <strong>{booking.guests || 1}</strong>
                                </div>
                            </div>

                            <h3 className="h6 fw-bold mb-3">Booked Items</h3>
                            <div className="d-flex flex-column gap-3 mb-4">
                                {(booking.items || []).map((item) => (
                                    <div key={item.id} className="p-3 border rounded d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong className="d-block">{item.name}</strong>
                                            <div className="small text-muted">
                                                Quantity: {item.quantity} · Unit Price: {formatMoney(item.unit_price)}
                                            </div>
                                        </div>
                                        <div className="fw-bold fs-6">
                                            {formatMoney(item.total_amount)}
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {booking.notes && (
                                <div className="p-3 bg-light rounded mb-3">
                                    <span className="small text-muted d-block">Customer Note</span>
                                    <p className="mb-0 small">{booking.notes}</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Payments History */}
                    <div className="card shadow-sm border-0">
                        <div className="card-header bg-white border-bottom p-3">
                            <h3 className="h6 fw-bold mb-0">Payment Records</h3>
                        </div>
                        <div className="card-body p-0">
                            {(!booking.payments || booking.payments.length === 0) ? (
                                <p className="text-muted p-4 mb-0">No payment records found.</p>
                            ) : (
                                <div className="table-responsive">
                                    <table className="table mb-0 align-middle">
                                        <thead className="table-light">
                                            <tr>
                                                <th>Reference</th>
                                                <th>Gateway</th>
                                                <th className="text-end">Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {booking.payments.map((p) => (
                                                <tr key={p.id}>
                                                    <td>
                                                        <strong className="d-block">{p.transaction_reference || '—'}</strong>
                                                        <span className="small text-muted">{new Date(p.created_at).toLocaleString()}</span>
                                                    </td>
                                                    <td>{p.gateway || p.method_label || '—'}</td>
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

                <div className="col-lg-4">
                    {/* Status Management */}
                    <div className="card shadow-sm border-0 mb-4">
                        <div className="card-body p-4">
                            <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Status Management</h3>

                            <form onSubmit={handleUpdate}>
                                <div className="mb-3">
                                    <label htmlFor="status" className="form-label fw-semibold">Update Status</label>
                                    <select
                                        id="status"
                                        className="form-select"
                                        value={data.status}
                                        onChange={(e) => setData('status', e.target.value)}
                                    >
                                        {Object.entries(statuses).map(([k, label]) => (
                                            <option key={k} value={k}>{label}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="mb-3">
                                    <label htmlFor="internal_notes" className="form-label fw-semibold">Internal Admin Note</label>
                                    <textarea
                                        id="internal_notes"
                                        rows="3"
                                        className="form-control"
                                        placeholder="Visible only to staff…"
                                        value={data.internal_notes}
                                        onChange={(e) => setData('internal_notes', e.target.value)}
                                    ></textarea>
                                </div>

                                <button className="btn btn-primary w-100" type="submit" disabled={processing}>
                                    {processing ? 'Updating…' : 'Save Changes'}
                                </button>
                            </form>
                        </div>
                    </div>

                    {/* Customer info */}
                    <div className="card shadow-sm border-0 mb-4">
                        <div className="card-body p-4">
                            <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Customer Details</h3>
                            <p className="fw-bold mb-1">{booking.customer_name}</p>
                            <p className="small text-muted mb-1">{booking.customer_email}</p>
                            <p className="small text-muted mb-3">{booking.customer_phone || '—'}</p>

                            {booking.user && (
                                <Link className="btn btn-sm btn-outline-secondary w-100" href={`/admin/users/${booking.user.id}`}>
                                    View Customer Profile
                                </Link>
                            )}
                        </div>
                    </div>

                    {/* Financial summary */}
                    <div className="card shadow-sm border-0">
                        <div className="card-body p-4">
                            <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Financial Breakdown</h3>

                            <div className="d-flex justify-content-between mb-2">
                                <span className="text-muted">Subtotal</span>
                                <span className="fw-semibold">{formatMoney(booking.subtotal)}</span>
                            </div>

                            {parseInt(booking.discount, 10) > 0 && (
                                <div className="d-flex justify-content-between mb-2 text-success">
                                    <span>Discount {booking.coupon ? `(${booking.coupon.code})` : ''}</span>
                                    <span>-{formatMoney(booking.discount)}</span>
                                </div>
                            )}

                            <div className="d-flex justify-content-between mb-2">
                                <span className="text-muted">Taxes</span>
                                <span className="fw-semibold">{formatMoney(booking.tax)}</span>
                            </div>

                            {parseInt(booking.refunded_amount, 10) > 0 && (
                                <div className="d-flex justify-content-between mb-2 text-danger">
                                    <span>Refunded</span>
                                    <span>-{formatMoney(booking.refunded_amount)}</span>
                                </div>
                            )}

                            <div className="d-flex justify-content-between border-top pt-2 mt-2 fw-bold fs-5">
                                <span>Total</span>
                                <span className="text-primary">{formatMoney(booking.final_amount)}</span>
                            </div>

                            <a
                                className="btn btn-soft w-100 mt-3"
                                href={`/bookings/${booking.id}/invoice`}
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                <i className="bi bi-printer me-1"></i> Print Receipt
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
