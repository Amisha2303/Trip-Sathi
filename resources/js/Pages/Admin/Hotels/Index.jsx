import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import Pagination from '@/Components/Pagination';

export default function Index({ hotels, destinations = [], filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [destId, setDestId] = useState(filters.destination || '');
    const [status, setStatus] = useState(filters.status || '');
    const [featured, setFeatured] = useState(filters.featured || false);

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/admin/hotels', {
            q: search,
            destination: destId,
            status,
            featured: featured ? 1 : undefined,
        }, { preserveState: true });
    };

    const handleDelete = (h) => {
        if (confirm(`Delete hotel "${h.name}"?`)) {
            router.delete(`/admin/hotels/${h.id}`);
        }
    };

    const list = hotels?.data || [];

    return (
        <AdminLayout title="Hotels" heading="Hotels">
            <Head title="Hotels — Admin — Trip Sathi" />

            <div className="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <form onSubmit={handleFilter} className="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                    <input
                        className="form-control form-control-sm"
                        style={{ maxWidth: '220px' }}
                        type="search"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Search hotels…"
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

                <Link className="btn btn-sm btn-accent" href="/admin/hotels/create">
                    <i className="bi bi-plus-lg me-1"></i> Add Hotel
                </Link>
            </div>

            <div className="card shadow-sm border-0">
                <div className="table-responsive">
                    <table className="table mb-0 align-middle">
                        <thead className="table-light">
                            <tr>
                                <th>Hotel</th>
                                <th>Destination</th>
                                <th>Category</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th className="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {list.length === 0 ? (
                                <tr>
                                    <td colSpan="6" className="text-center py-4 text-muted">
                                        No hotels found matching criteria.
                                    </td>
                                </tr>
                            ) : (
                                list.map((hotel) => (
                                    <tr key={hotel.id}>
                                        <td>
                                            <div className="d-flex align-items-center gap-3">
                                                <img
                                                    src={hotel.image_url || '/images/hotel-placeholder.svg'}
                                                    alt={hotel.name}
                                                    className="rounded object-fit-cover"
                                                    style={{ width: '44px', height: '44px' }}
                                                    onError={(e) => {
                                                        e.target.onerror = null;
                                                        e.target.src = '/images/hotel-placeholder.svg';
                                                    }}
                                                />
                                                <div>
                                                    <strong className="text-dark d-block">{hotel.name}</strong>
                                                    {hotel.is_featured && (
                                                        <span className="badge bg-warning text-dark me-1" style={{ fontSize: '0.65rem' }}>
                                                            Featured
                                                        </span>
                                                    )}
                                                    <span className="small text-muted">{hotel.city || '—'}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{hotel.destination?.name || '—'}</td>
                                        <td>{hotel.category?.name || '—'}</td>
                                        <td>
                                            <i className="bi bi-star-fill text-warning me-1"></i>
                                            {Number(hotel.rating || 0).toFixed(1)}
                                        </td>
                                        <td>
                                            <span className={`badge ${hotel.is_active ? 'bg-success' : 'bg-secondary'}`}>
                                                {hotel.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td className="text-end">
                                            <div className="btn-group btn-group-sm">
                                                <Link className="btn btn-outline-secondary" href={`/admin/hotels/${hotel.id}/edit`}>
                                                    Edit
                                                </Link>
                                                <button
                                                    className="btn btn-outline-danger"
                                                    type="button"
                                                    onClick={() => handleDelete(hotel)}
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
                <Pagination links={hotels.links} />
            </div>
        </AdminLayout>
    );
}
