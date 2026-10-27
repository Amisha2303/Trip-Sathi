import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import Pagination from '@/Components/Pagination';

export default function Index({ darshans, destinations = [], types = {}, filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [destId, setDestId] = useState(filters.destination || '');
    const [status, setStatus] = useState(filters.status || '');
    const [featured, setFeatured] = useState(filters.featured || false);

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/admin/darshan', {
            q: search,
            destination: destId,
            status,
            featured: featured ? 1 : undefined,
        }, { preserveState: true });
    };

    const handleDelete = (d) => {
        if (confirm(`Delete darshan "${d.name}"?`)) {
            router.delete(`/admin/darshan/${d.id}`);
        }
    };

    const list = darshans?.data || [];

    const formatMoney = (amount) => {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            maximumFractionDigits: 0,
        }).format(amount || 0);
    };

    return (
        <AdminLayout title="VIP Darshan" heading="VIP Darshan">
            <Head title="VIP Darshan — Admin — Trip Sathi" />

            <div className="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <form onSubmit={handleFilter} className="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                    <input
                        className="form-control form-control-sm"
                        style={{ maxWidth: '220px' }}
                        type="search"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Search temples, deities…"
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

                <Link className="btn btn-sm btn-accent" href="/admin/darshan/create">
                    <i className="bi bi-plus-lg me-1"></i> Add Darshan
                </Link>
            </div>

            <div className="card shadow-sm border-0">
                <div className="table-responsive">
                    <table className="table mb-0 align-middle">
                        <thead className="table-light">
                            <tr>
                                <th>Darshan &amp; Temple</th>
                                <th>Destination</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th className="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {list.length === 0 ? (
                                <tr>
                                    <td colSpan="6" className="text-center py-4 text-muted">
                                        No darshan listings found.
                                    </td>
                                </tr>
                            ) : (
                                list.map((d) => (
                                    <tr key={d.id}>
                                        <td>
                                            <div className="d-flex align-items-center gap-3">
                                                <img
                                                    src={d.image_url || '/images/darshan-placeholder.svg'}
                                                    alt={d.name}
                                                    className="rounded object-fit-cover"
                                                    style={{ width: '44px', height: '44px' }}
                                                    onError={(e) => {
                                                        e.target.onerror = null;
                                                        e.target.src = '/images/darshan-placeholder.svg';
                                                    }}
                                                />
                                                <div>
                                                    <strong className="text-dark d-block">{d.name}</strong>
                                                    {d.is_featured && (
                                                        <span className="badge bg-warning text-dark me-1" style={{ fontSize: '0.65rem' }}>
                                                            Featured
                                                        </span>
                                                    )}
                                                    <span className="small text-muted">{d.temple_name || d.deity || '—'}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{d.destination?.name || '—'}</td>
                                        <td>
                                            <span className="badge bg-light text-dark border">
                                                {types[d.type] || d.type || 'VIP'}
                                            </span>
                                        </td>
                                        <td className="fw-semibold">{formatMoney(d.effective_price || d.price)}</td>
                                        <td>
                                            <span className={`badge ${d.is_active ? 'bg-success' : 'bg-secondary'}`}>
                                                {d.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td className="text-end">
                                            <div className="btn-group btn-group-sm">
                                                <Link className="btn btn-outline-secondary" href={`/admin/darshan/${d.id}/edit`}>
                                                    Edit
                                                </Link>
                                                <button
                                                    className="btn btn-outline-danger"
                                                    type="button"
                                                    onClick={() => handleDelete(d)}
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
                <Pagination links={darshans.links} />
            </div>
        </AdminLayout>
    );
}
