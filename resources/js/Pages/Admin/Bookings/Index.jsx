import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import Pagination from '@/Components/Pagination';

export default function Index({
    bookings,
    statusTotals = {},
    statuses = {},
    paymentStatuses = {},
    types = {},
    filters = {},
}) {
    const [search, setSearch] = useState(filters.q || '');
    const [status, setStatus] = useState(filters.status || '');
    const [paymentStatus, setPaymentStatus] = useState(filters.payment_status || '');
    const [bookingType, setBookingType] = useState(filters.type || '');
    const [from, setFrom] = useState(filters.from || '');
    const [to, setTo] = useState(filters.to || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/admin/bookings', {
            q: search,
            status,
            payment_status: paymentStatus,
            type: bookingType,
            from,
            to,
        }, { preserveState: true });
    };

    const formatMoney = (amount) => {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
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

    const list = bookings?.data || [];

    return (
        <AdminLayout title="Bookings" heading="Bookings">
            <Head title="Bookings — Admin — Trip Sathi" />

            <div className="card shadow-sm border-0 mb-4 p-3">
                <form onSubmit={handleFilter} className="row g-2 align-items-center">
                    <div className="col-md-3">
                        <input
                            className="form-control form-control-sm"
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Search number, name, email…"
                        />
                    </div>
                    <div className="col-sm-6 col-md-2">
                        <select
                            className="form-select form-select-sm"
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                        >
                            <option value="">All statuses</option>
                            {Object.entries(statuses).map(([k, label]) => (
                                <option key={k} value={k}>{label} ({statusTotals[k] || 0})</option>
                            ))}
                        </select>
                    </div>
                    <div className="col-sm-6 col-md-2">
                        <select
                            className="form-select form-select-sm"
                            value={paymentStatus}
                            onChange={(e) => setPaymentStatus(e.target.value)}
                        >
                            <option value="">All payment statuses</option>
                            {Object.entries(paymentStatuses).map(([k, label]) => (
                                <option key={k} value={k}>{label}</option>
                            ))}
                        </select>
                    </div>
                    <div className="col-sm-6 col-md-2">
                        <select
                            className="form-select form-select-sm"
                            value={bookingType}
                            onChange={(e) => setBookingType(e.target.value)}
                        >
                            <option value="">All categories</option>
                            {Object.entries(types).map(([k, label]) => (
                                <option key={k} value={k}>{label}</option>
                            ))}
                        </select>
                    </div>
                    <div className="col-sm-6 col-md-2">
                        <input
                            type="date"
                            className="form-control form-control-sm"
                            value={from}
                            onChange={(e) => setFrom(e.target.value)}
                            title="From date"
                        />
                    </div>
                    <div className="col-auto">
                        <button className="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
                    </div>
                </form>
            </div>

            <div className="card shadow-sm border-0">
                <div className="table-responsive">
                    <table className="table mb-0 align-middle">
                        <thead className="table-light">
                            <tr>
                                <th>Booking Number</th>
                                <th>Customer</th>
                                <th>Experience</th>
                                <th>Booking Date</th>
                                <th className="text-end">Amount</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th className="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {list.length === 0 ? (
                                <tr>
                                    <td colSpan="8" className="text-center py-4 text-muted">
                                        No bookings match the selected filters.
                                    </td>
                                </tr>
                            ) : (
                                list.map((b) => (
                                    <tr key={b.id}>
                                        <td>
                                            <strong className="text-dark d-block">{b.booking_number}</strong>
                                            <span className="small text-muted">{b.created_at ? new Date(b.created_at).toLocaleDateString() : ''}</span>
                                        </td>
                                        <td>
                                            <div>{b.customer_name}</div>
                                            <span className="small text-muted">{b.customer_email}</span>
                                        </td>
                                        <td>
                                            <div>{b.title}</div>
                                            <span className="small text-muted">{b.destination?.name || b.type_label}</span>
                                        </td>
                                        <td>
                                            {b.booking_date ? new Date(b.booking_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : '—'}
                                        </td>
                                        <td className="text-end fw-semibold">{formatMoney(b.final_amount)}</td>
                                        <td>
                                            <span className={`badge ${getStatusBadge(b.status)}`}>
                                                {b.status_label || (typeof b.status === 'object' ? b.status.label : b.status)}
                                            </span>
                                        </td>
                                        <td>
                                            <span className={`badge ${getPaymentBadge(b.payment_status)}`}>
                                                {b.payment_status?.label || b.payment_status}
                                            </span>
                                        </td>
                                        <td className="text-end">
                                            <Link className="btn btn-sm btn-soft" href={`/admin/bookings/${b.id}`}>
                                                Details
                                            </Link>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            <div className="mt-4">
                <Pagination links={bookings.links} />
            </div>
        </AdminLayout>
    );
}
