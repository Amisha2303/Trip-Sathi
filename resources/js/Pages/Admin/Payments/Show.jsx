import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Show({ payment }) {
    const [reference, setReference] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    const formatMoney = (amount) => {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: payment.currency || 'INR',
            maximumFractionDigits: 0,
        }).format(amount || 0);
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

    const isPaid = payment.status === 'paid' || payment.status?.value === 'paid';

    const handleMarkPaid = (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        router.post(`/admin/payments/${payment.id}/mark-paid`, { reference }, {
            onFinish: () => setIsSubmitting(false),
        });
    };

    return (
        <AdminLayout
            title={`Payment ${payment.transaction_reference || payment.id}`}
            heading={`Payment · ${payment.transaction_reference || payment.id}`}
        >
            <Head title={`Payment ${payment.transaction_reference || payment.id} — Admin — Trip Sathi`} />

            <div className="row g-4">
                <div className="col-lg-7">
                    <div className="card shadow-sm border-0 mb-4">
                        <div className="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                            <h2 className="h5 mb-0 fw-bold">Payment Summary</h2>
                            <span className={`badge ${getPaymentBadge(payment.status)}`}>
                                {payment.status?.label || payment.status}
                            </span>
                        </div>

                        <div className="card-body p-4">
                            <div className="row g-3">
                                <div className="col-sm-6">
                                    <span className="small text-muted d-block">Transaction Reference</span>
                                    <strong className="fs-6">{payment.transaction_reference || '—'}</strong>
                                </div>
                                <div className="col-sm-6">
                                    <span className="small text-muted d-block">Amount</span>
                                    <strong className="fs-5 text-primary">{formatMoney(payment.amount)}</strong>
                                </div>
                                <div className="col-sm-6">
                                    <span className="small text-muted d-block">Gateway</span>
                                    <div>{payment.gateway || payment.method_label || '—'}</div>
                                </div>
                                <div className="col-sm-6">
                                    <span className="small text-muted d-block">Created At</span>
                                    <div>{new Date(payment.created_at).toLocaleString()}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Linked Booking */}
                    {payment.booking && (
                        <div className="card shadow-sm border-0 mb-4">
                            <div className="card-header bg-white border-bottom p-3">
                                <h3 className="h6 fw-bold mb-0">Linked Booking</h3>
                            </div>
                            <div className="card-body p-4">
                                <div className="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                    <div>
                                        <strong className="fs-5 d-block">{payment.booking.booking_number}</strong>
                                        <span className="text-muted small">{payment.booking.title}</span>
                                    </div>
                                    <Link className="btn btn-sm btn-outline-primary" href={`/admin/bookings/${payment.booking.id}`}>
                                        View Booking
                                    </Link>
                                </div>

                                <div className="row g-2 small text-muted">
                                    <div className="col-6">Customer: {payment.booking.customer_name}</div>
                                    <div className="col-6">Email: {payment.booking.customer_email}</div>
                                    <div className="col-6">Total Due: {formatMoney(payment.booking.final_amount)}</div>
                                    <div className="col-6">Status: {payment.booking.status}</div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Transactions Log */}
                    <div className="card shadow-sm border-0">
                        <div className="card-header bg-white border-bottom p-3">
                            <h3 className="h6 fw-bold mb-0">Gateway Transactions</h3>
                        </div>
                        <div className="card-body p-0">
                            {(!payment.transactions || payment.transactions.length === 0) ? (
                                <p className="text-muted p-4 mb-0">No external gateway logs recorded.</p>
                            ) : (
                                <div className="table-responsive">
                                    <table className="table mb-0 align-middle">
                                        <thead className="table-light">
                                            <tr>
                                                <th>Event</th>
                                                <th>Reference</th>
                                                <th>Status</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {payment.transactions.map((tx) => (
                                                <tr key={tx.id}>
                                                    <td>{tx.event || tx.type || 'transaction'}</td>
                                                    <td><code>{tx.gateway_transaction_id || tx.reference || '—'}</code></td>
                                                    <td>{tx.status}</td>
                                                    <td className="small text-muted">{new Date(tx.created_at).toLocaleString()}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <div className="col-lg-5">
                    {/* Mark as Paid Action */}
                    {!isPaid && (
                        <div className="card shadow-sm border-0 mb-4 border-warning">
                            <div className="card-body p-4">
                                <h3 className="h6 fw-bold mb-2">Record Offline Settlement</h3>
                                <p className="small text-muted mb-3">
                                    If the traveler paid offline (cash, bank transfer or UPI directly), mark this payment as settled.
                                </p>

                                <form onSubmit={handleMarkPaid}>
                                    <div className="mb-3">
                                        <label htmlFor="ref" className="form-label small fw-semibold">Bank / UPI Reference</label>
                                        <input
                                            id="ref"
                                            className="form-control form-control-sm"
                                            placeholder="Optional transaction reference"
                                            value={reference}
                                            onChange={(e) => setReference(e.target.value)}
                                        />
                                    </div>
                                    <button className="btn btn-success btn-sm w-100" type="submit" disabled={isSubmitting}>
                                        <i className="bi bi-check-circle me-1"></i> Mark as Paid
                                    </button>
                                </form>
                            </div>
                        </div>
                    )}

                    {/* Customer */}
                    <div className="card shadow-sm border-0">
                        <div className="card-body p-4">
                            <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Customer Profile</h3>
                            <p className="fw-semibold mb-1">{payment.user?.name || payment.booking?.customer_name}</p>
                            <p className="small text-muted mb-1">{payment.user?.email || payment.booking?.customer_email}</p>
                            <p className="small text-muted mb-0">{payment.user?.phone || payment.booking?.customer_phone || '—'}</p>
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
