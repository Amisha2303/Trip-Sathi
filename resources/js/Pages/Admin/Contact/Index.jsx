import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import Pagination from '@/Components/Pagination';

export default function Index({ messages, counts = {}, statuses = {}, filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [status, setStatus] = useState(filters.status || '');
    const [unread, setUnread] = useState(filters.unread || false);

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/admin/contact-messages', {
            q: search,
            status,
            unread: unread ? 1 : undefined,
        }, { preserveState: true });
    };

    const handleDelete = (msg) => {
        if (confirm(`Delete message from ${msg.name}?`)) {
            router.delete(`/admin/contact-messages/${msg.id}`);
        }
    };

    const list = messages?.data || [];

    return (
        <AdminLayout title="Contact Inbox" heading="Contact Inbox">
            <Head title="Contact Inbox — Admin — Trip Sathi" />

            <div className="card shadow-sm border-0 mb-4 p-3">
                <form onSubmit={handleFilter} className="row g-2 align-items-center">
                    <div className="col-md-4">
                        <input
                            className="form-control form-control-sm"
                            type="search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Search name, email, subject…"
                        />
                    </div>
                    <div className="col-sm-6 col-md-3">
                        <select
                            className="form-select form-select-sm"
                            value={status}
                            onChange={(e) => setStatus(e.target.value)}
                        >
                            <option value="">All statuses</option>
                            {Object.entries(statuses).map(([k, label]) => (
                                <option key={k} value={k}>{label} ({counts[k] || 0})</option>
                            ))}
                        </select>
                    </div>
                    <div className="col-sm-6 col-md-3">
                        <div className="form-check form-check-inline mt-1">
                            <input
                                type="checkbox"
                                id="unread"
                                className="form-check-input"
                                checked={Boolean(unread)}
                                onChange={(e) => setUnread(e.target.checked)}
                            />
                            <label className="form-check-label small" htmlFor="unread">
                                Unread only ({counts.unread || 0})
                            </label>
                        </div>
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
                                <th>Subject</th>
                                <th>Sender</th>
                                <th>Received</th>
                                <th>Status</th>
                                <th className="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {list.length === 0 ? (
                                <tr>
                                    <td colSpan="5" className="text-center py-4 text-muted">
                                        No messages found.
                                    </td>
                                </tr>
                            ) : (
                                list.map((msg) => (
                                    <tr key={msg.id} className={!msg.is_read ? 'table-light fw-semibold' : ''}>
                                        <td>
                                            <div className="d-flex align-items-center gap-2">
                                                {!msg.is_read && (
                                                    <span className="badge bg-primary rounded-circle p-1"></span>
                                                )}
                                                <div>
                                                    <Link href={`/admin/contact-messages/${msg.id}`} className="text-dark text-decoration-none">
                                                        {msg.subject || 'General enquiry'}
                                                    </Link>
                                                    <p className="small text-muted mb-0 text-truncate" style={{ maxWidth: '380px' }}>
                                                        {msg.message}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>{msg.name}</div>
                                            <span className="small text-muted">{msg.email}</span>
                                        </td>
                                        <td className="small text-muted">
                                            {new Date(msg.created_at).toLocaleDateString()}
                                        </td>
                                        <td>
                                            <span className={`badge ${msg.status === 'replied' ? 'bg-success' : msg.status === 'new' ? 'bg-warning text-dark' : 'bg-secondary'}`}>
                                                {statuses[msg.status] || msg.status}
                                            </span>
                                        </td>
                                        <td className="text-end">
                                            <div className="btn-group btn-group-sm">
                                                <Link className="btn btn-outline-secondary" href={`/admin/contact-messages/${msg.id}`}>
                                                    View
                                                </Link>
                                                <button
                                                    className="btn btn-outline-danger"
                                                    type="button"
                                                    onClick={() => handleDelete(msg)}
                                                >
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

            <div className="mt-4">
                <Pagination links={messages.links} />
            </div>
        </AdminLayout>
    );
}
