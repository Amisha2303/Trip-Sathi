import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import Pagination from '@/Components/Pagination';

export default function Index({ adventures, destinations = [], difficulties = {}, filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [destId, setDestId] = useState(filters.destination || '');
    const [status, setStatus] = useState(filters.status || '');
    const [featured, setFeatured] = useState(filters.featured || false);

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/admin/adventures', {
            q: search,
            destination: destId,
            status,
            featured: featured ? 1 : undefined,
        }, { preserveState: true });
    };

    const handleDelete = (adv) => {
        if (confirm(`Delete adventure "${adv.name}"?`)) {
            router.delete(`/admin/adventures/${adv.id}`);
        }
    };

    const list = adventures?.data || [];

    const formatMoney = (amount) => {
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            maximumFractionDigits: 0,
        }).format(amount || 0);
    };

    return (
        <AdminLayout title="Adventures" heading="Adventures">
            <Head title="Adventures — Admin — Trip Sathi" />

            <div className="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <form onSubmit={handleFilter} className="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                    <input
                        className="form-control form-control-sm"
                        style={{ maxWidth: '220px' }}
                        type="search"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Search adventures…"
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

                <Link className="btn btn-sm btn-accent" href="/admin/adventures/create">
                    <i className="bi bi-plus-lg me-1"></i> Add Adventure
                </Link>
            </div>

            <div className="card shadow-sm border-0">
                <div className="table-responsive">
                    <table className="table mb-0 align-middle">
                        <thead className="table-light">
                            <tr>
                                <th>Adventure</th>
                                <th>Destination</th>
                                <th>Difficulty</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th className="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {list.length === 0 ? (
                                <tr>
                                    <td colSpan="6" className="text-center py-4 text-muted">
                                        No adventures found.
                                    </td>
                                </tr>
                            ) : (
                                list.map((adv) => (
                                    <tr key={adv.id}>
                                        <td>
                                            <div className="d-flex align-items-center gap-3">
                                                <img
                                                    src={adv.image_url || '/images/adventure-placeholder.svg'}
                                                    alt={adv.name}
                                                    className="rounded object-fit-cover"
                                                    style={{ width: '44px', height: '44px' }}
                                                    onError={(e) => {
                                                        e.target.onerror = null;
                                                        e.target.src = '/images/adventure-placeholder.svg';
                                                    }}
                                                />
                                                <div>
                                                    <strong className="text-dark d-block">{adv.name}</strong>
                                                    {adv.is_featured && (
                                                        <span className="badge bg-warning text-dark me-1" style={{ fontSize: '0.65rem' }}>
                                                            Featured
                                                        </span>
                                                    )}
                                                    <span className="small text-muted">{adv.activity_type || '—'}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{adv.destination?.name || '—'}</td>
                                        <td>
                                            <span className="badge bg-light text-dark border">
                                                {difficulties[adv.difficulty] || adv.difficulty || 'Easy'}
                                            </span>
                                        </td>
                                        <td className="fw-semibold">{formatMoney(adv.effective_price || adv.price)}</td>
                                        <td>
                                            <span className={`badge ${adv.is_active ? 'bg-success' : 'bg-secondary'}`}>
                                                {adv.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td className="text-end">
                                            <div className="btn-group btn-group-sm">
                                                <Link className="btn btn-outline-secondary" href={`/admin/adventures/${adv.id}/edit`}>
                                                    Edit
                                                </Link>
                                                <button
                                                    className="btn btn-outline-danger"
                                                    type="button"
                                                    onClick={() => handleDelete(adv)}
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
                <Pagination links={adventures.links} />
            </div>
        </AdminLayout>
    );
}
