import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Show({ user, stats = {} }) {
    const [confirmDelete, setConfirmDelete] = useState(false);

    const { data, setData, put, processing, errors } = useForm({
        name: user?.name || '',
        email: user?.email || '',
        phone: user?.phone || '',
        city: user?.city || '',
        state: user?.state || '',
        country: user?.country || 'India',
        is_admin: Boolean(user?.is_admin),
        is_active: Boolean(user?.is_active),
        password: '',
    });

    const handleUpdate = (e) => {
        if (e && e.preventDefault) e.preventDefault();
        const payload = { ...data };
        if (!payload.password || payload.password.trim() === '') {
            delete payload.password;
        }
        put(`/admin/users/${user.id}`, payload, { preserveScroll: true });
    };

    const executeDelete = () => {
        router.delete(`/admin/users/${user.id}`);
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

    return (
        <AdminLayout
            title={`Edit ${user.name}`}
            heading={`User · ${user.name}`}
            breadcrumbs={[
                { label: 'Admin', href: '/admin/dashboard' },
                { label: 'Users', href: '/admin/users' },
                { label: user.name },
            ]}
        >
            <Head title={`${user.name} — Admin — Trip Sathi`} />

            <div className="row g-4">
                <div className="col-lg-4">
                    <div className="card shadow-sm border-0 mb-4 text-center p-4">
                        <div
                            className="rounded-circle bg-light text-primary fw-bold mx-auto mb-3 d-flex align-items-center justify-content-center"
                            style={{ width: '64px', height: '64px', fontSize: '1.6rem' }}
                        >
                            {(user.name || 'U').substring(0, 1).toUpperCase()}
                        </div>
                        <h2 className="h5 fw-bold mb-1">{user.name}</h2>
                        <p className="text-muted small mb-3">{user.email}</p>
                        <div className="d-flex justify-content-center gap-2 flex-wrap">
                            <span className={`badge ${user.is_admin ? 'bg-primary' : 'bg-light text-dark border'}`}>
                                {user.is_admin ? 'Admin' : 'Traveller'}
                            </span>
                            <span className={`badge ${user.is_active ? 'bg-success' : 'bg-secondary'}`}>
                                {user.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                    </div>

                    <div className="card shadow-sm border-0 mb-4">
                        <div className="card-body p-4">
                            <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Booking Stats</h3>
                            <div className="row g-3 text-center">
                                <div className="col-6">
                                    <div className="fs-4 fw-bold">{stats.total || 0}</div>
                                    <div className="small text-muted">Total</div>
                                </div>
                                <div className="col-6">
                                    <div className="fs-4 fw-bold text-info">{stats.upcoming || 0}</div>
                                    <div className="small text-muted">Upcoming</div>
                                </div>
                                <div className="col-6">
                                    <div className="fs-4 fw-bold text-success">{stats.completed || 0}</div>
                                    <div className="small text-muted">Completed</div>
                                </div>
                                <div className="col-6">
                                    <div className="fs-4 fw-bold text-secondary">{stats.cancelled || 0}</div>
                                    <div className="small text-muted">Cancelled</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="card shadow-sm border-0 border-danger">
                        <div className="card-body p-4">
                            <h3 className="h6 fw-bold text-danger mb-2">Delete Account</h3>
                            <p className="small text-muted mb-3">
                                Once deleted, this user account cannot be restored.
                            </p>
                            <button className="btn btn-outline-danger btn-sm w-100" type="button" onClick={() => setConfirmDelete(true)}>
                                Delete User
                            </button>
                        </div>
                    </div>
                </div>

                <div className="col-lg-8">
                    <div className="card shadow-sm border-0 mb-4">
                        <div className="card-body p-4">
                            <h3 className="h5 fw-bold mb-3 border-bottom pb-2">Account Details</h3>

                            <form onSubmit={handleUpdate}>
                                <div className="row g-3">
                                    <div className="col-md-6">
                                        <label htmlFor="name" className="form-label fw-semibold">Name</label>
                                        <input
                                            id="name"
                                            className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            required
                                        />
                                        {errors.name && <div className="invalid-feedback">{errors.name}</div>}
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="email" className="form-label fw-semibold">Email</label>
                                        <input
                                            id="email"
                                            type="email"
                                            className={`form-control ${errors.email ? 'is-invalid' : ''}`}
                                            value={data.email}
                                            onChange={(e) => setData('email', e.target.value)}
                                            required
                                        />
                                        {errors.email && <div className="invalid-feedback">{errors.email}</div>}
                                    </div>

                                    <div className="col-md-4">
                                        <label htmlFor="phone" className="form-label fw-semibold">Phone</label>
                                        <input
                                            id="phone"
                                            className="form-control"
                                            value={data.phone}
                                            onChange={(e) => setData('phone', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-4">
                                        <label htmlFor="city" className="form-label fw-semibold">City</label>
                                        <input
                                            id="city"
                                            className="form-control"
                                            value={data.city}
                                            onChange={(e) => setData('city', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-4">
                                        <label htmlFor="state" className="form-label fw-semibold">State</label>
                                        <input
                                            id="state"
                                            className="form-control"
                                            value={data.state}
                                            onChange={(e) => setData('state', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="country" className="form-label fw-semibold">Country</label>
                                        <input
                                            id="country"
                                            className="form-control"
                                            value={data.country}
                                            onChange={(e) => setData('country', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="password" className="form-label fw-semibold">
                                            Reset Password <span className="small text-muted fw-normal">(leave blank to keep current)</span>
                                        </label>
                                        <input
                                            id="password"
                                            type="password"
                                            className={`form-control ${errors.password ? 'is-invalid' : ''}`}
                                            value={data.password}
                                            onChange={(e) => setData('password', e.target.value)}
                                            placeholder="Min 8 characters…"
                                            autoComplete="new-password"
                                        />
                                        {errors.password && <div className="invalid-feedback">{errors.password}</div>}
                                    </div>

                                    <div className="col-12 border-top pt-3 mt-4">
                                        <div className="d-flex gap-4">
                                            <div className="form-check">
                                                <input
                                                    type="checkbox"
                                                    id="is_admin"
                                                    className="form-check-input"
                                                    checked={data.is_admin}
                                                    onChange={(e) => setData('is_admin', e.target.checked)}
                                                />
                                                <label className="form-check-label fw-semibold" htmlFor="is_admin">
                                                    Administrator
                                                </label>
                                            </div>

                                            <div className="form-check">
                                                <input
                                                    type="checkbox"
                                                    id="is_active"
                                                    className="form-check-input"
                                                    checked={data.is_active}
                                                    onChange={(e) => setData('is_active', e.target.checked)}
                                                />
                                                <label className="form-check-label fw-semibold" htmlFor="is_active">
                                                    Active account
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-12 d-flex justify-content-end gap-2 mt-4">
                                        <Link className="btn btn-outline-secondary" href="/admin/users">Back to list</Link>
                                        <button className="btn btn-primary" type="submit" disabled={processing}>
                                            {processing ? 'Saving…' : 'Save changes'}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {/* Bookings table */}
                    <div className="card shadow-sm border-0">
                        <div className="card-body p-4">
                            <h3 className="h5 fw-bold mb-3 border-bottom pb-2">Recent Bookings</h3>

                            {(!user.bookings || user.bookings.length === 0) ? (
                                <p className="text-muted mb-0">No bookings created by this user yet.</p>
                            ) : (
                                <div className="table-responsive">
                                    <table className="table mb-0 align-middle">
                                        <thead className="table-light">
                                            <tr>
                                                <th>Booking</th>
                                                <th>Destination</th>
                                                <th className="text-end">Amount</th>
                                                <th>Status</th>
                                                <th className="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {user.bookings.map((b) => (
                                                <tr key={b.id}>
                                                    <td>
                                                        <strong className="d-block">{b.booking_number}</strong>
                                                        <span className="small text-muted">{b.booking_date}</span>
                                                    </td>
                                                    <td>{b.destination?.name || '—'}</td>
                                                    <td className="text-end fw-semibold">{formatMoney(b.final_amount)}</td>
                                                    <td>
                                                        <span className={`badge ${getStatusBadge(b.status)}`}>
                                                            {b.status_label || b.status}
                                                        </span>
                                                    </td>
                                                    <td className="text-end">
                                                        <Link className="btn btn-sm btn-soft" href={`/admin/bookings/${b.id}`}>
                                                            View
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
                </div>
            </div>

            {/* Sticky Mobile Action Bar */}
            <div className="ts-mobile-sticky-actions d-lg-none">
                <button
                    className="btn btn-primary flex-grow-1"
                    type="button"
                    onClick={handleUpdate}
                    disabled={processing}
                >
                    <i className="bi bi-check2 me-1"></i> {processing ? 'Saving…' : 'Save Changes'}
                </button>
                <button
                    className="btn btn-outline-danger"
                    type="button"
                    onClick={() => setConfirmDelete(true)}
                    title="Delete User Account"
                >
                    <i className="bi bi-trash"></i>
                </button>
            </div>

            {/* Deletion Confirmation Modal */}
            {confirmDelete && (
                <div
                    className="ts-modal-backdrop d-flex align-items-center justify-content-center p-3"
                    style={{ zIndex: 1070 }}
                >
                    <div
                        className="card ts-glass-card shadow-lg p-4 text-center border-danger-subtle"
                        style={{ maxWidth: '400px', width: '100%', background: '#fff' }}
                    >
                        <div className="fs-1 text-danger mb-2">
                            <i className="bi bi-exclamation-triangle-fill"></i>
                        </div>
                        <h4 className="fw-bold fs-5 mb-2 text-dark">Delete User Account?</h4>
                        <p className="text-muted small mb-4">
                            Are you sure you want to permanently delete <strong>{user.name}</strong>? This action cannot be undone.
                        </p>
                        <div className="d-flex gap-2">
                            <button
                                type="button"
                                className="btn btn-outline-secondary flex-grow-1"
                                onClick={() => setConfirmDelete(false)}
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                className="btn btn-danger flex-grow-1"
                                onClick={executeDelete}
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AdminLayout>
    );
}
