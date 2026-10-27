import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import Pagination from '@/Components/Pagination';

export default function Index({ categories, types = {}, filters = {} }) {
    const [search, setSearch] = useState(filters.q || '');
    const [type, setType] = useState(filters.type || '');
    const [status, setStatus] = useState(filters.status || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/admin/categories', { q: search, type, status }, { preserveState: true });
    };

    const handleDelete = (c) => {
        if (confirm(`Delete category "${c.name}"?`)) {
            router.delete(`/admin/categories/${c.id}`);
        }
    };

    const list = categories?.data || [];

    return (
        <AdminLayout title="Categories" heading="Categories">
            <Head title="Categories — Admin — Trip Sathi" />

            <div className="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <form onSubmit={handleFilter} className="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                    <input
                        className="form-control form-control-sm"
                        style={{ maxWidth: '220px' }}
                        type="search"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Search categories…"
                    />
                    <select
                        className="form-select form-select-sm"
                        style={{ maxWidth: '160px' }}
                        value={type}
                        onChange={(e) => setType(e.target.value)}
                    >
                        <option value="">All types</option>
                        {Object.entries(types).map(([k, label]) => (
                            <option key={k} value={k}>{label}</option>
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
                    <button className="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
                </form>

                <Link className="btn btn-sm btn-accent" href="/admin/categories/create">
                    <i className="bi bi-plus-lg me-1"></i> Add Category
                </Link>
            </div>

            <div className="card shadow-sm border-0">
                <div className="table-responsive">
                    <table className="table mb-0 align-middle">
                        <thead className="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Icon</th>
                                <th>Sort Order</th>
                                <th>Status</th>
                                <th className="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {list.length === 0 ? (
                                <tr>
                                    <td colSpan="6" className="text-center py-4 text-muted">
                                        No categories found.
                                    </td>
                                </tr>
                            ) : (
                                list.map((cat) => (
                                    <tr key={cat.id}>
                                        <td>
                                            <strong className="text-dark d-block">{cat.name}</strong>
                                            <span className="small text-muted">{cat.slug}</span>
                                        </td>
                                        <td>
                                            <span className="badge bg-light text-dark border">
                                                {types[cat.type] || cat.type}
                                            </span>
                                        </td>
                                        <td>
                                            {cat.icon && <i className={`bi ${cat.icon} fs-5 text-primary`}></i>}
                                        </td>
                                        <td>{cat.sort_order ?? 0}</td>
                                        <td>
                                            <span className={`badge ${cat.is_active ? 'bg-success' : 'bg-secondary'}`}>
                                                {cat.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td className="text-end">
                                            <div className="btn-group btn-group-sm">
                                                <Link className="btn btn-outline-secondary" href={`/admin/categories/${cat.id}/edit`}>
                                                    Edit
                                                </Link>
                                                <button
                                                    className="btn btn-outline-danger"
                                                    type="button"
                                                    onClick={() => handleDelete(cat)}
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
                <Pagination links={categories.links} />
            </div>
        </AdminLayout>
    );
}
