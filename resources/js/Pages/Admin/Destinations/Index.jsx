import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import Pagination from '@/Components/Pagination';

export default function Index({ destinations, filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [status, setStatus] = useState(filters.status || '');
    const [featured, setFeatured] = useState(filters.featured || false);

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/admin/destinations', { q: search, status, featured: featured ? 1 : undefined }, { preserveState: true });
    };

    const handleDelete = (d) => {
        if (confirm(`Delete destination "${d.name}"?`)) {
            router.delete(`/admin/destinations/${d.id}`);
        }
    };

    const list = destinations?.data || [];

    return (
        <AdminLayout
            title="Destinations"
            heading="Destinations"
            breadcrumbs={[{ label: 'Admin', href: '/admin/dashboard' }, { label: 'Destinations' }]}
        >
            <Head title="Destinations — Admin — Trip Sathi" />

            <div className="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <form onSubmit={handleFilter} className="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                    <input
                        className="form-control form-control-sm"
                        style={{ maxWidth: '240px' }}
                        type="search"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Search destinations…"
                    />
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
                    <div className="form-check form-check-inline ms-2">
                        <input
                            type="checkbox"
                            id="featured"
                            className="form-check-input"
                            checked={Boolean(featured)}
                            onChange={(e) => setFeatured(e.target.checked)}
                        />
                        <label className="form-check-label small" htmlFor="featured">Featured only</label>
                    </div>
                    <button className="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
                </form>

                <Link className="btn btn-sm btn-accent" href="/admin/destinations/create">
                    <i className="bi bi-plus-lg me-1"></i> Add Destination
                </Link>
            </div>

            <div className="card shadow-sm border-0">
                <div className="table-responsive">
                    <table className="table mb-0 align-middle">
                        <thead className="table-light">
                            <tr>
                                <th>Destination</th>
                                <th>Location</th>
                                <th>Category</th>
                                <th>Popularity</th>
                                <th>Status</th>
                                <th className="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {list.length === 0 ? (
                                <tr>
                                    <td colSpan="6" className="text-center py-4 text-muted">
                                        No destinations found.
                                    </td>
                                </tr>
                            ) : (
                                list.map((dest) => (
                                    <tr key={dest.id}>
                                        <td>
                                            <div className="d-flex align-items-center gap-3">
                                                <img
                                                    src={dest.image_url || '/images/destination-placeholder.svg'}
                                                    alt={dest.name}
                                                    className="rounded object-fit-cover"
                                                    style={{ width: '44px', height: '44px' }}
                                                    onError={(e) => {
                                                        e.target.onerror = null;
                                                        e.target.src = '/images/destination-placeholder.svg';
                                                    }}
                                                />
                                                <div>
                                                    <strong className="text-dark d-block">{dest.name}</strong>
                                                    {dest.is_featured && (
                                                        <span className="badge bg-warning text-dark me-1" style={{ fontSize: '0.65rem' }}>
                                                            Featured
                                                        </span>
                                                    )}
                                                    <span className="small text-muted">{dest.slug}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {[dest.city, dest.state, dest.country].filter(Boolean).join(', ') || '—'}
                                        </td>
                                        <td>{dest.category?.name || '—'}</td>
                                        <td>{dest.popularity || 0}</td>
                                        <td>
                                            <span className={`badge ${dest.is_active ? 'bg-success' : 'bg-secondary'}`}>
                                                {dest.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td className="text-end">
                                            <div className="btn-group btn-group-sm">
                                                <Link className="btn btn-outline-secondary" href={`/admin/destinations/${dest.id}/edit`}>
                                                    Edit
                                                </Link>
                                                <button
                                                    className="btn btn-outline-danger"
                                                    type="button"
                                                    onClick={() => handleDelete(dest)}
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
                <Pagination links={destinations.links} />
            </div>
        </AdminLayout>
    );
}
