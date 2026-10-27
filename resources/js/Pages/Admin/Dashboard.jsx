import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Dashboard({
    kpis = [],
    statusBreakdown = [],
    recentBookings = [],
    recentUsers = [],
    pendingMessages = [],
    unreadMessages = 0,
    topDestinations = [],
    statusLabels = {},
}) {
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

    return (
        <AdminLayout title="Admin Dashboard" heading="Admin Dashboard">
            <Head title="Admin Dashboard — Trip Sathi" />

            {/* KPI Cards */}
            <div className="row g-3 mb-4">
                {kpis.map((kpi, idx) => (
                    <div key={idx} className="col-6 col-xl-3">
                        <div className="card shadow-sm border-0 p-3 h-100 d-flex flex-row align-items-center gap-3">
                            <div className="fs-2 text-primary p-2 bg-light rounded">
                                <i className={`bi ${kpi.icon}`}></i>
                            </div>
                            <div>
                                <div className="text-muted small">{kpi.label}</div>
                                <div className="fs-4 fw-bold">{kpi.value}</div>
                                <div className="small text-muted">{kpi.note}</div>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {/* Status Breakdown & Top Destinations */}
            <div className="row g-4 mb-4">
                <div className="col-lg-7">
                    <div className="card shadow-sm border-0 h-100">
                        <div className="card-body p-4">
                            <div className="d-flex justify-content-between align-items-center mb-3">
                                <h2 className="h5 fw-bold mb-0">Booking Status</h2>
                                <span className="small text-muted">All time</span>
                            </div>

                            {statusBreakdown.length === 0 ? (
                                <p className="text-muted mb-0">No bookings have been created yet.</p>
                            ) : (
                                <div className="table-responsive">
                                    <table className="table mb-0 align-middle">
                                        <thead className="table-light">
                                            <tr>
                                                <th>Status</th>
                                                <th className="text-end">Bookings</th>
                                                <th className="text-end">Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {statusBreakdown.map((row, idx) => {
                                                const stVal = typeof row.status === 'object' ? row.status.value : row.status;
                                                return (
                                                    <tr key={idx}>
                                                        <td>
                                                            <span className={`badge ${getStatusBadge(stVal)}`}>
                                                                {statusLabels[stVal] || stVal}
                                                            </span>
                                                        </td>
                                                        <td className="text-end">{row.total}</td>
                                                        <td className="text-end fw-semibold">{formatMoney(row.amount)}</td>
                                                    </tr>
                                                );
                                            })}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <div className="col-lg-5">
                    <div className="card shadow-sm border-0 h-100">
                        <div className="card-body p-4">
                            <div className="d-flex justify-content-between align-items-center mb-3">
                                <h2 className="h5 fw-bold mb-0">Top Destinations</h2>
                                <span className="small text-muted">By bookings</span>
                            </div>

                            {topDestinations.length === 0 ? (
                                <p className="text-muted mb-0">Catalogue is still empty.</p>
                            ) : (
                                <ul className="list-unstyled mb-0 d-flex flex-column gap-3">
                                    {topDestinations.map((d) => (
                                        <li key={d.id} className="d-flex align-items-center gap-3">
                                            <img
                                                src={d.image_url || '/images/destination-placeholder.svg'}
                                                alt={d.name}
                                                className="rounded object-fit-cover"
                                                style={{ width: '48px', height: '48px' }}
                                                onError={(e) => {
                                                    e.target.onerror = null;
                                                    e.target.src = '/images/destination-placeholder.svg';
                                                }}
                                            />
                                            <div className="flex-grow-1">
                                                <p className="mb-0 fw-semibold">{d.name}</p>
                                                <p className="mb-0 text-muted small">
                                                    {d.bookings_count} booking{d.bookings_count === 1 ? '' : 's'} · {d.hotels_count} stay{d.hotels_count === 1 ? '' : 's'}
                                                </p>
                                            </div>
                                            <span className="badge bg-light text-dark border">
                                                {d.popularity || 0} pts
                                            </span>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Recent Bookings & Inbox / Users */}
            <div className="row g-4">
                <div className="col-lg-7">
                    <div className="card shadow-sm border-0">
                        <div className="card-body p-4">
                            <div className="d-flex justify-content-between align-items-center mb-3">
                                <h2 className="h5 fw-bold mb-0">Recent Bookings</h2>
                                <Link href="/admin/bookings" className="btn btn-sm btn-link p-0 text-decoration-none">View all</Link>
                            </div>

                            {recentBookings.length === 0 ? (
                                <p className="text-muted mb-0">Nothing booked yet.</p>
                            ) : (
                                <div className="table-responsive">
                                    <table className="table mb-0 align-middle">
                                        <thead className="table-light">
                                            <tr>
                                                <th>Booking</th>
                                                <th>Customer</th>
                                                <th>Experience</th>
                                                <th className="text-end">Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {recentBookings.map((b) => (
                                                <tr key={b.id}>
                                                    <td>
                                                        <Link href={`/admin/bookings/${b.id}`} className="text-dark fw-bold text-decoration-none">
                                                            {b.booking_number}
                                                        </Link>
                                                        <div className="small text-muted">
                                                            {b.booking_date ? new Date(b.booking_date).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : ''}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div>{b.customer_name}</div>
                                                        <div className="small text-muted">{b.customer_email}</div>
                                                    </td>
                                                    <td>
                                                        <div>{b.type_label}</div>
                                                        <div className="small text-muted">{b.destination?.name || '—'}</div>
                                                    </td>
                                                    <td className="text-end fw-semibold">{formatMoney(b.final_amount)}</td>
                                                    <td>
                                                        <span className={`badge ${getStatusBadge(b.status)}`}>
                                                            {b.status_label || (typeof b.status === 'object' ? b.status.label : b.status)}
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

                <div className="col-lg-5">
                    {/* Contact Inbox Card */}
                    <div className="card shadow-sm border-0 mb-4">
                        <div className="card-body p-4">
                            <div className="d-flex justify-content-between align-items-center mb-3">
                                <h2 className="h5 fw-bold mb-0">Contact Inbox</h2>
                                <span className="badge bg-warning text-dark">{unreadMessages} unread</span>
                            </div>

                            {pendingMessages.length === 0 ? (
                                <p className="text-muted mb-0">No unread messages.</p>
                            ) : (
                                <ul className="list-unstyled mb-0 d-flex flex-column gap-3">
                                    {pendingMessages.map((msg) => (
                                        <li key={msg.id} className="d-flex align-items-start gap-3 p-2 rounded hover-bg">
                                            <i className="bi bi-envelope-exclamation text-warning fs-5 mt-1"></i>
                                            <div className="flex-grow-1">
                                                <Link href={`/admin/contact-messages/${msg.id}`} className="fw-semibold text-dark text-decoration-none d-block">
                                                    {msg.subject || 'General enquiry'}
                                                </Link>
                                                <p className="text-muted small mb-1">
                                                    {msg.name} · {msg.email} · {new Date(msg.created_at).toLocaleDateString()}
                                                </p>
                                                <p className="small text-muted fst-italic mb-0 text-truncate" style={{ maxWidth: '300px' }}>
                                                    "{msg.message}"
                                                </p>
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </div>
                    </div>

                    {/* Newest Accounts Card */}
                    <div className="card shadow-sm border-0">
                        <div className="card-body p-4">
                            <div className="d-flex justify-content-between align-items-center mb-3">
                                <h2 className="h5 fw-bold mb-0">Newest Accounts</h2>
                                <Link href="/admin/users" className="btn btn-sm btn-link p-0 text-decoration-none">View all</Link>
                            </div>

                            {recentUsers.length === 0 ? (
                                <p className="text-muted mb-0">No accounts yet.</p>
                            ) : (
                                <ul className="list-unstyled mb-0 d-flex flex-column gap-3">
                                    {recentUsers.map((u) => (
                                        <li key={u.id} className="d-flex align-items-center gap-3">
                                            <div
                                                className="rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center"
                                                style={{ width: '40px', height: '40px' }}
                                            >
                                                {(u.name || 'U').substring(0, 1).toUpperCase()}
                                            </div>
                                            <div className="flex-grow-1">
                                                <Link href={`/admin/users/${u.id}`} className="fw-semibold text-dark text-decoration-none d-block">
                                                    {u.name}
                                                </Link>
                                                <span className="text-muted small">{u.email}</span>
                                            </div>
                                            <span className={`badge ${u.is_admin ? 'bg-primary' : 'bg-light text-dark border'}`}>
                                                {u.is_admin ? 'Admin' : 'Traveller'}
                                            </span>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
}
