import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import AccountLayout from '../../Layouts/AccountLayout';

export default function Dashboard({
    stats = {},
    upcomingBookings = [],
    recentBookings = [],
    recommendations = [],
    notifications = [],
}) {
    const { auth } = usePage().props;
    const user = auth?.user;

    const formatCurrency = (amt) => {
        if (!amt) return '₹0';
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            maximumFractionDigits: 0,
        }).format(amt);
    };

    const getStatusBadge = (status) => {
        const badges = {
            confirmed: 'bg-success',
            completed: 'bg-primary',
            pending: 'bg-warning text-dark',
            cancelled: 'bg-secondary',
            failed: 'bg-danger',
        };
        return badges[status] || 'bg-light text-dark';
    };

    return (
        <AccountLayout title="Dashboard">
            {/* Header banner */}
            <div className="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <div className="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div className="d-flex align-items-center gap-3">
                        <div
                            className="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center fs-4"
                            style={{ width: 56, height: 56 }}
                        >
                            {user?.name?.charAt(0) || 'U'}
                        </div>
                        <div>
                            <h2 className="h4 fw-bold mb-1">Welcome back, {user?.name?.split(' ')[0]}</h2>
                            <p className="text-muted small mb-0">{user?.email} · {user?.is_admin ? 'Administrator' : 'Traveller'}</p>
                        </div>
                    </div>

                    <div className="d-flex gap-2">
                        <Link className="btn btn-accent btn-sm" href="/trips?scope=mine">
                            <i className="bi bi-plus-lg me-1"></i> Plan a trip
                        </Link>
                        <Link className="btn btn-outline-secondary btn-sm" href="/profile/edit">
                            <i className="bi bi-person-gear me-1"></i> Edit profile
                        </Link>
                    </div>
                </div>
            </div>

            {/* Quick Stats Grid */}
            <div className="row g-3 mb-4">
                {[
                    { label: 'Total bookings', value: stats.total || 0, icon: 'bi-ticket-perforated', color: 'text-primary' },
                    { label: 'Upcoming', value: stats.upcoming || 0, icon: 'bi-calendar-check', color: 'text-success' },
                    { label: 'Completed', value: stats.completed || 0, icon: 'bi-check2-circle', color: 'text-info' },
                    { label: 'Cancelled', value: stats.cancelled || 0, icon: 'bi-x-circle', color: 'text-secondary' },
                    { label: 'Saved trips', value: stats.trips || 0, icon: 'bi-map', color: 'text-warning' },
                    { label: 'Wishlist', value: stats.wishlist || 0, icon: 'bi-heart', color: 'text-danger' },
                ].map((st, idx) => (
                    <div key={idx} className="col-6 col-md-4 col-xl-2">
                        <div className="card border-0 shadow-sm p-3 rounded-4 h-100 text-center">
                            <i className={`bi ${st.icon} fs-4 mb-2 ${st.color}`}></i>
                            <div className="fs-4 fw-bold text-dark">{st.value}</div>
                            <small className="text-muted text-nowrap">{st.label}</small>
                        </div>
                    </div>
                ))}
            </div>

            <div className="row g-4">
                {/* Upcoming Bookings */}
                <div className="col-lg-7">
                    <div className="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <div className="d-flex justify-content-between align-items-center mb-3">
                            <h3 className="h5 fw-bold mb-0">Upcoming Bookings</h3>
                            <Link className="btn btn-sm btn-soft" href="/bookings">View all</Link>
                        </div>

                        {upcomingBookings.length === 0 ? (
                            <div className="text-center py-4">
                                <i className="bi bi-calendar-plus fs-2 text-muted mb-2"></i>
                                <p className="text-muted small mb-3">No upcoming bookings. Ready to plan your next getaway?</p>
                                <Link className="btn btn-accent btn-sm" href="/destinations">Explore Destinations</Link>
                            </div>
                        ) : (
                            <div className="d-flex flex-column gap-3">
                                {upcomingBookings.map((b) => (
                                    <div key={b.id} className="d-flex justify-content-between align-items-center p-3 rounded-3 bg-light">
                                        <div className="d-flex align-items-center gap-3">
                                            <div className="rounded-3 bg-white p-2 shadow-sm fs-4 text-primary">
                                                <i className="bi bi-ticket-perforated"></i>
                                            </div>
                                            <div>
                                                <div className="fw-bold">{b.customer_name} ({b.booking_number})</div>
                                                <small className="text-muted">
                                                    <i className="bi bi-calendar3 me-1"></i>{b.booking_date}
                                                    {b.destination && ` · ${b.destination.name}`}
                                                </small>
                                            </div>
                                        </div>
                                        <div className="d-flex align-items-center gap-2">
                                            <span className={`badge ${getStatusBadge(b.status)}`}>{b.status}</span>
                                            <Link href={`/bookings/${b.id}`} className="btn btn-sm btn-outline-secondary">
                                                Details
                                            </Link>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Recent Bookings Table */}
                    <div className="card border-0 shadow-sm rounded-4 p-4">
                        <div className="d-flex justify-content-between align-items-center mb-3">
                            <h3 className="h5 fw-bold mb-0">Recent Bookings History</h3>
                            <Link className="btn btn-sm btn-soft" href="/bookings">History</Link>
                        </div>

                        {recentBookings.length === 0 ? (
                            <p className="text-muted small mb-0">No booking history recorded yet.</p>
                        ) : (
                            <div className="table-responsive">
                                <table className="table table-hover align-middle mb-0">
                                    <thead className="table-light small">
                                        <tr>
                                            <th>Booking #</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody className="small">
                                        {recentBookings.map((rb) => (
                                            <tr key={rb.id}>
                                                <td className="fw-semibold">{rb.booking_number}</td>
                                                <td className="text-muted">{rb.booking_date}</td>
                                                <td><span className={`badge ${getStatusBadge(rb.status)}`}>{rb.status}</span></td>
                                                <td className="fw-semibold">{formatCurrency(rb.final_amount_rupees || (rb.final_amount / 100))}</td>
                                                <td className="text-end">
                                                    <Link href={`/bookings/${rb.id}`} className="btn btn-sm btn-light">
                                                        <i className="bi bi-eye"></i>
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        )}
                    </div>
                </div>

                {/* Recommendations & Quick Actions */}
                <div className="col-lg-5">
                    <div className="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <h3 className="h5 fw-bold mb-3">Recommended For You</h3>
                        <div className="d-flex flex-column gap-3">
                            {recommendations.map((rec) => (
                                <Link
                                    key={rec.id}
                                    href={`/destinations/${rec.slug}`}
                                    className="d-flex align-items-center gap-3 p-2 rounded-3 text-decoration-none text-dark hover-bg-light"
                                >
                                    <img
                                        src={rec.main_image ? (rec.main_image.startsWith('/') ? rec.main_image : `/${rec.main_image}`) : '/images/placeholder.svg'}
                                        alt={rec.name}
                                        className="rounded-3 object-fit-cover"
                                        style={{ width: 56, height: 56 }}
                                    />
                                    <div className="flex-grow-1">
                                        <div className="fw-bold">{rec.name}</div>
                                        <small className="text-muted">{rec.hotels_count || 0} stays available</small>
                                    </div>
                                    <i className="bi bi-chevron-right text-muted"></i>
                                </Link>
                            ))}
                        </div>
                    </div>

                    <div className="card border-0 shadow-sm rounded-4 p-4 bg-primary text-white">
                        <h4 className="fw-bold mb-2">Need Travel Assistance?</h4>
                        <p className="text-white-50 small mb-3">
                            Our team can help arrange VIP temple passes, pooja rituals, transport, or group stays.
                        </p>
                        <Link href="/contact" className="btn btn-accent btn-sm">
                            <i className="bi bi-chat-dots me-1"></i> Contact Support
                        </Link>
                    </div>
                </div>
            </div>
        </AccountLayout>
    );
}
