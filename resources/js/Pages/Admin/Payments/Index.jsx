import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import Pagination from '@/Components/Pagination';

export default function Index({ payments, totals = {}, statuses = {}, gateways = {}, filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [status, setStatus] = useState(filters.status || '');
    const [gateway, setGateway] = useState(filters.gateway || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/admin/payments', { q: search, status, gateway }, { preserveState: true });
    };

    const formatMoney = (amount) => {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
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

    const list = payments?.data || [];

    return (
        <AdminLayout title="Payments" heading="Payments">
            <Head title="Payments — Admin — Trip Sathi" />

            <div className="card shadow-sm border-0 mb-4 p-3">
                <form onSubmit={handleFilter} className="row g-2 align-items-center">
                    <div className="col-md-4">
                        <input
                            className="form-control form-control-sm"
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Search reference, booking number, customer…"
                        />
                    </div>
                    <div className="col-sm-6 col-md-3">
                        <select
                            className="form-select form-select-sm"
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                        >
                            <option value="">All payment statuses</option>
                            {Object.entries(statuses).map(([k, label]) => (
                                <option key={k} value={k}>{label} ({totals[k]?.total || 0})</option>
                            ))}
                        </select>
                    </div>
                    <div className="col-sm-6 col-md-3">
                        <select
                            className="form-select form-select-sm"
                            value={gateway}
                            onChange={(e) => setGateway(e.target.value)}
                        >
                            <option value="">All gateways</option>
                            {Object.entries(gateways).map(([k, label]) => (
                                <option key={k} value={k}>{label}</option>
                            ))}
                        </select>
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
                                <th>Reference</th>
                                <th>Booking</th>
                                <th>Customer</th>
                                <th>Gateway</th>
                                <th className="text-end">Amount</th>
                                <th>Status</th>
                                <th className="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {list.length === 0 ? (
                                <tr>
                                    <td colSpan="7" className="text-center py-4 text-muted">
                                        No payment records found.
                                    </td>
                                </tr>
                            ) : (
                                list.map((p) => (
                                    <tr key={p.id}>
                                        <td>
                                            <strong className="text-dark d-block">{p.transaction_reference || '—'}</strong>
                                            <span className="small text-muted">{new Date(p.created_at).toLocaleString()}</span>
                                        </td>
                                        <td>
                                            {p.booking ? (
                                                <Link href={`/admin/bookings/${p.booking.id}`} className="text-dark fw-semibold text-decoration-none">
                                                    {p.booking.booking_number}
                                                </Link>
                                            ) : '—'}
                                        </td>
                                        <td>
                                            <div>{p.booking?.customer_name || p.user?.name || '—'}</div>
                                            <span className="small text-muted">{p.user?.email || ''}</span>
                                        </td>
                                        <td>
                                            {p.gateway === 'demo' ? (
                                                <span className="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">
                                                    <i className="bi bi-shield-check me-1"></i>Demo Pay
                                                </span>
                                            ) : (
                                                <span className="text-capitalize">{p.gateway || p.method_label || '—'}</span>
                                            )}
                                        </td>
                                        <td className="text-end fw-semibold">{formatMoney(p.amount)}</td>
                                        <td>
                                            <span className={`badge ${getPaymentBadge(p.status)}`}>
                                                {p.status?.label || p.status}
                                            </span>
                                        </td>
                                        <td className="text-end">
                                            <Link className="btn btn-sm btn-soft" href={`/admin/payments/${p.id}`}>
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
                <Pagination links={payments.links} />
            </div>
        </AdminLayout>
    );
}
