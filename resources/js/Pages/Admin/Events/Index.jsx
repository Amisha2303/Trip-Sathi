import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import Pagination from '@/Components/Pagination';

export default function Index({ events, destinations = [], filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [destId, setDestId] = useState(filters.destination || '');
    const [status, setStatus] = useState(filters.status || '');
    const [featured, setFeatured] = useState(filters.featured || false);

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/admin/events', {
            q: search,
            destination: destId,
            status,
            featured: featured ? 1 : undefined,
        }, { preserveState: true });
    };

    const handleDelete = (ev) => {
        if (confirm(`Delete event "${ev.name}"?`)) {
            router.delete(`/admin/events/${ev.id}`);
        }
    };

    const list = events?.data || [];

    return (
        <AdminLayout title="Events" heading="Events">
            <Head title="Events — Admin — Trip Sathi" />

            <div className="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <form onSubmit={handleFilter} className="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                    <input
                        className="form-control form-control-sm"
                        style={{ maxWidth: '220px' }}
                        type="search"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Search events…"
                    />
                    <select
                        className="form-select form-select-sm"
                        style={{ maxWidth: '160px' }}
                        value={destId}
                        onChange={(e) => setDestId(e.target.value)}
                    >
                        <option value="">All destinations</option>
                        {destinations.map((d) => (
                            <option key={d.id} value={d.id}>{d.name}</option>
                        ))}
                    </select>
                    <select
                        className="form-select form-select-sm"
                        style={{ maxWidth: '130px' }}
                        value={status}
                        onChange={(e) => setStatus(e.target.value)}
                    >
                        <option value="">Any status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <div className="form-check form-check-inline ms-1">
                        <input
                            type="checkbox"
                            id="featured"
                            className="form-check-input"
                            checked={Boolean(featured)}
                            onChange={(e) => setFeatured(e.target.checked)}
                        />
                        <label className="form-check-label small" htmlFor="featured">Featured</label>
                    </div>
                    <button className="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
                </form>

                <Link className="btn btn-sm btn-accent" href="/admin/events/create">
                    <i className="bi bi-plus-lg me-1"></i> Add Event
                </Link>
            </div>

            <div className="card shadow-sm border-0">
                <div className="table-responsive">
                    <table className="table mb-0 align-middle">
                        <thead className="table-light">
                            <tr>
                                <th>Event</th>
                                <th>Date &amp; Venue</th>
                                <th>Destination</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th className="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {list.length === 0 ? (
                                <tr>
                                    <td colSpan="6" className="text-center py-4 text-muted">
                                        No events found.
                                    </td>
                                </tr>
                            ) : (
                                list.map((ev) => (
                                    <tr key={ev.id}>
                                        <td>
                                            <div className="d-flex align-items-center gap-3">
                                                <img
                                                    src={ev.image_url || '/images/event-placeholder.svg'}
                                                    alt={ev.name}
                                                    className="rounded object-fit-cover"
                                                    style={{ width: '44px', height: '44px' }}
                                                    onError={(e) => {
                                                        e.target.onerror = null;
                                                        e.target.src = '/images/event-placeholder.svg';
                                                    }}
                                                />
                                                <div>
                                                    <strong className="text-dark d-block">{ev.name}</strong>
                                                    {ev.is_featured && (
                                                        <span className="badge bg-warning text-dark me-1" style={{ fontSize: '0.65rem' }}>
                                                            Featured
                                                        </span>
                                                    )}
                                                    <span className="small text-muted">{ev.organiser || '—'}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>{ev.event_date ? new Date(ev.event_date).toLocaleDateString() : '—'}</div>
                                            <span className="small text-muted">{ev.venue || '—'}</span>
                                        </td>
                                        <td>{ev.destination?.name || '—'}</td>
                                        <td>{ev.category?.name || '—'}</td>
                                        <td>
                                            <span className={`badge ${ev.is_active ? 'bg-success' : 'bg-secondary'}`}>
                                                {ev.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td className="text-end">
                                            <div className="btn-group btn-group-sm">
                                                <Link className="btn btn-outline-secondary" href={`/admin/events/${ev.id}/edit`}>
                                                    Edit
                                                </Link>
                                                <button
                                                    className="btn btn-outline-danger"
                                                    type="button"
                                                    onClick={() => handleDelete(ev)}
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
                <Pagination links={events.links} />
            </div>
        </AdminLayout>
    );
}
