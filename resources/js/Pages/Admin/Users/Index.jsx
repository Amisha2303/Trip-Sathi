import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import Pagination from '@/Components/Pagination';

export default function Index({ users, filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [role, setRole] = useState(filters.role || '');
    const [status, setStatus] = useState(filters.status || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/admin/users', { q: search, role, status }, { preserveState: true });
    };

    const userList = users?.data || [];

    const [deleteTarget, setDeleteTarget] = useState(null);

    const handleDelete = (u) => {
        setDeleteTarget(u);
    };

    const confirmDeleteAction = () => {
        if (deleteTarget) {
            router.delete(`/admin/users/${deleteTarget.id}`, {
                onSuccess: () => setDeleteTarget(null),
            });
        }
    };

    return (
        <AdminLayout
            title="Users"
            heading="Users Management"
            breadcrumbs={[{ label: 'Admin', href: '/admin/dashboard' }, { label: 'Users' }]}
        >
            <Head title="Users — Admin — Trip Sathi" />

            <div className="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <form onSubmit={handleFilter} className="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                    <input
                        className="form-control form-control-sm"
                        style={{ maxWidth: '240px' }}
                        type="search"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Search name, email or phone…"
                    />
                    <select
                        className="form-select form-select-sm"
                        style={{ maxWidth: '140px' }}
                        value={role}
                        onChange={(e) => setRole(e.target.value)}
                    >
                        <option value="">All roles</option>
                        <option value="admin">Admins</option>
                        <option value="traveller">Travellers</option>
                    </select>
                    <select
                        className="form-select form-select-sm"
                        style={{ maxWidth: '140px' }}
                        value={status}
                        onChange={(e) => setStatus(e.target.value)}
                    >
                        <option value="">Any status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <button className="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
                </form>

                <p className="text-muted small mb-0">{users?.total || 0} users</p>
            </div>

            {/* Mobile Cards List (< 768px) */}
            <div className="d-md-none d-flex flex-column gap-3 mb-4">
                {userList.length === 0 ? (
                    <div className="card ts-glass-card p-4 text-center text-muted">
                        No users found matching your filters.
                    </div>
                ) : (
                    userList.map((user) => (
                        <div key={user.id} className="card ts-glass-card shadow-sm border-0 p-3">
                            <div className="d-flex align-items-center justify-content-between mb-2">
                                <Link href={`/admin/users/${user.id}`} className="d-flex align-items-center gap-2 text-decoration-none">
                                    <div
                                        className="rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center"
                                        style={{ width: '40px', height: '40px' }}
                                    >
                                        {(user.name || 'U').substring(0, 1).toUpperCase()}
                                    </div>
                                    <div>
                                        <strong className="text-dark d-block">{user.name}</strong>
                                        <span className="small text-muted">{user.email}</span>
                                    </div>
                                </Link>
                                <span className={`badge ${user.is_admin ? 'bg-primary' : 'bg-light text-dark border'}`}>
                                    {user.is_admin ? 'Admin' : 'Traveller'}
                                </span>
                            </div>

                            <div className="d-flex justify-content-between align-items-center py-2 border-top border-bottom border-light-subtle my-2 small">
                                <div>Phone: <span className="text-dark fw-medium">{user.phone || '—'}</span></div>
                                <div>Bookings: <span className="badge bg-secondary-subtle text-secondary fw-bold">{user.bookings_count}</span></div>
                            </div>

                            <div className="d-flex justify-content-end gap-2 mt-2">
                                <Link className="btn btn-sm btn-outline-primary" href={`/admin/users/${user.id}`}>
                                    <i className="bi bi-pencil me-1"></i> Edit
                                </Link>
                                <button className="btn btn-sm btn-outline-danger" type="button" onClick={() => handleDelete(user)}>
                                    <i className="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    ))
                )}
            </div>

            {/* Desktop / Tablet Table (>= 768px) */}
            <div className="card shadow-sm border-0 d-none d-md-block">
                <div className="table-responsive">
                    <table className="table mb-0 align-middle">
                        <thead className="table-light">
                            <tr>
                                <th>User</th>
                                <th>Phone</th>
                                <th className="text-end">Bookings</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th className="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {userList.length === 0 ? (
                                <tr>
                                    <td colSpan="6" className="text-center py-4 text-muted">
                                        No users found matching your filters.
                                    </td>
                                </tr>
                            ) : (
                                userList.map((user) => (
                                    <tr key={user.id}>
                                        <td>
                                            <Link href={`/admin/users/${user.id}`} className="d-flex align-items-center gap-3 text-decoration-none">
                                                <div
                                                    className="rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0"
                                                    style={{ width: '38px', height: '38px' }}
                                                >
                                                    {(user.name || 'U').substring(0, 1).toUpperCase()}
                                                </div>
                                                <div>
                                                    <strong className="text-dark d-block">{user.name}</strong>
                                                    <span className="small text-muted">{user.email}</span>
                                                </div>
                                            </Link>
                                        </td>
                                        <td>{user.phone || '—'}</td>
                                        <td className="text-end fw-semibold">{user.bookings_count}</td>
                                        <td>
                                            <span className={`badge ${user.is_admin ? 'bg-primary' : 'bg-light text-dark border'}`}>
                                                {user.is_admin ? 'Admin' : 'Traveller'}
                                            </span>
                                        </td>
                                        <td>
                                            <span className={`badge ${user.is_active ? 'bg-success' : 'bg-secondary'}`}>
                                                {user.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td className="text-end">
                                            <div className="d-inline-flex gap-1">
                                                <Link className="btn btn-sm btn-soft" href={`/admin/users/${user.id}`}>
                                                    View / Edit
                                                </Link>
                                                <button className="btn btn-sm btn-outline-danger" type="button" onClick={() => handleDelete(user)} title="Delete user">
                                                    <i className="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Deletion Confirmation Modal */}
            {deleteTarget && (
                <div
                    className="ts-theme-backdrop is-open d-flex align-items-center justify-content-center p-3"
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
                            Are you sure you want to permanently delete <strong>{deleteTarget.name}</strong>? This action cannot be undone.
                        </p>
                        <div className="d-flex gap-2">
                            <button
                                type="button"
                                className="btn btn-outline-secondary flex-grow-1"
                                onClick={() => setDeleteTarget(null)}
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                className="btn btn-danger flex-grow-1"
                                onClick={confirmDeleteAction}
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            )}

            <div className="mt-4">
                <Pagination links={users.links} />
            </div>
        </AdminLayout>
    );
}
