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

    return (
        <AdminLayout title="Users" heading="Users">
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

            <div className="card shadow-sm border-0">
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
                                            <div className="d-flex align-items-center gap-3">
                                                <div
                                                    className="rounded-circle bg-light text-primary fw-bold d-flex align-items-center justify-content-center"
                                                    style={{ width: '38px', height: '38px' }}
                                                >
                                                    {(user.name || 'U').substring(0, 1).toUpperCase()}
                                                </div>
                                                <div>
                                                    <strong className="text-dark d-block">{user.name}</strong>
                                                    <span className="small text-muted">{user.email}</span>
                                                </div>
                                            </div>
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
                                            <Link className="btn btn-sm btn-soft" href={`/admin/users/${user.id}`}>
                                                View / Edit
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
                <Pagination links={users.links} />
            </div>
        </AdminLayout>
    );
}
