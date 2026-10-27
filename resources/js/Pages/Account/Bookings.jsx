import React from 'react';
import { Link, router } from '@inertiajs/react';
import AccountLayout from '../../Layouts/AccountLayout';
import Pagination from '../../Components/Pagination';

export default function Bookings({
    bookings,
    status = null,
    statuses = [],
    counts = {},
}) {
    const formatCurrency = (amt) => {
        if (!amt) return '—';
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            maximumFractionDigits: 0,
        }).format(amt);
    };

    const getStatusBadge = (st) => {
        const badges = {
            confirmed: 'bg-success',
            completed: 'bg-primary',
            pending: 'bg-warning text-dark',
            cancelled: 'bg-secondary',
            failed: 'bg-danger',
        };
        return badges[st] || 'bg-light text-dark';
    };

    const getPaymentBadge = (pst) => {
        const badges = {
            paid: 'bg-success text-white',
            pending: 'bg-warning text-dark',
            failed: 'bg-danger text-white',
            refunded: 'bg-info text-dark',
            partially_refunded: 'bg-info text-dark',
        };
        return badges[pst] || 'bg-light text-dark';
    };

    return (
        <AccountLayout title="My Bookings">
            <div className="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <div>
                    <h2 className="h4 fw-bold mb-1">My Bookings</h2>
                    <p className="text-muted small mb-0">Every stay, ticket, darshan and activity you have booked.</p>
                </div>
                <Link className="btn btn-accent btn-sm" href="/destinations">
                    <i className="bi bi-plus-lg me-1"></i> Book something new
                </Link>
            </div>

            {/* Filter Tabs */}
            <div className="d-flex flex-wrap gap-2 mb-4">
                {[
                    { label: 'All', key: null, count: counts.all || 0 },
                    { label: 'Upcoming', key: 'upcoming', count: counts.upcoming || 0 },
                    { label: 'Pending', key: 'pending', count: counts.pending || 0 },
                    { label: 'Completed', key: 'completed', count: counts.completed || 0 },
                    { label: 'Cancelled', key: 'cancelled', count: counts.cancelled || 0 },
                ].map((tab, idx) => {
                    const isSelected = status === tab.key;
                    const url = tab.key ? `/bookings?status=${tab.key}` : '/bookings';
                    return (
                        <Link
                            key={idx}
                            href={url}
                            className={`btn btn-sm rounded-pill px-3 ${isSelected ? 'btn-primary' : 'btn-light text-secondary'}`}
                        >
                            {tab.label} <span className="badge bg-white text-dark ms-1">{tab.count}</span>
                        </Link>
                    );
                })}
            </div>

            {/* Bookings Table */}
            {bookings.data?.length === 0 ? (
                <div className="text-center py-5 card border-0 shadow-sm rounded-4">
                    <i className="bi bi-ticket-perforated fs-1 text-muted mb-2"></i>
                    <h4 className="fw-bold">{status ? 'No bookings in this filter' : 'No bookings yet'}</h4>
                    <p className="text-muted small">Book a hotel, event, adventure or darshan and it will appear here.</p>
                    <Link className="btn btn-primary btn-sm mx-auto" href="/hotels">
                        Browse Stays
                    </Link>
                </div>
            ) : (
                <div className="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div className="table-responsive">
                        <table className="table table-hover align-middle mb-0">
                            <thead className="table-light small">
                                <tr>
                                    <th>Booking #</th>
                                    <th>Service & Details</th>
                                    <th>Date</th>
                                    <th>Guests</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th className="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="small">
                                {bookings.data.map((b) => (
                                    <tr key={b.id}>
                                        <td>
                                            <strong className="text-dark d-block">{b.booking_number}</strong>
                                            <span className="text-muted small">{b.type_label || b.booking_type}</span>
                                        </td>
                                        <td>
                                            <div className="fw-semibold">{b.customer_name}</div>
                                            {b.destination && (
                                                <div className="text-muted small">
                                                    <i className="bi bi-geo-alt me-1"></i>{b.destination.name}
                                                </div>
                                            )}
                                        </td>
                                        <td>
                                            <div>{b.booking_date}</div>
                                            {b.end_date && b.end_date !== b.booking_date && (
                                                <small className="text-muted">to {b.end_date}</small>
                                            )}
                                        </td>
                                        <td>{b.guests || 1}</td>
                                        <td className="fw-bold">
                                            {formatCurrency(b.final_amount_rupees || (b.final_amount / 100))}
                                        </td>
                                        <td>
                                            <span className={`badge ${getPaymentBadge(b.payment_status)}`}>
                                                {b.payment_status}
                                            </span>
                                        </td>
                                        <td>
                                            <span className={`badge ${getStatusBadge(b.status)}`}>
                                                {b.status}
                                            </span>
                                        </td>
                                        <td className="text-end">
                                            <div className="d-inline-flex gap-1">
                                                {b.payment_status !== 'paid' && b.status !== 'cancelled' && (
                                                    <Link href={`/bookings/${b.id}/pay`} className="btn btn-sm btn-accent">
                                                        Pay
                                                    </Link>
                                                )}
                                                <Link href={`/bookings/${b.id}`} className="btn btn-sm btn-light">
                                                    View
                                                </Link>
                                                <Link href={`/bookings/${b.id}/invoice`} className="btn btn-sm btn-outline-secondary" target="_blank">
                                                    <i className="bi bi-receipt"></i>
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}

            <Pagination links={bookings.links} />
        </AccountLayout>
    );
}
