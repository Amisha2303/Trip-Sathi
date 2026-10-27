import React from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Form({ category = {}, types = {} }) {
    const isEdit = Boolean(category?.id);

    const { data, setData, post, processing, errors } = useForm({
        name: category?.name || '',
        type: category?.type || 'destination',
        description: category?.description || '',
        icon: category?.icon || '',
        sort_order: category?.sort_order ?? 0,
        is_active: category?.id ? Boolean(category?.is_active) : true,
        is_featured: Boolean(category?.is_featured),
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            router.put(`/admin/categories/${category.id}`, data);
        } else {
            post('/admin/categories');
        }
    };

    return (
        <AdminLayout
            title={isEdit ? `Edit ${category.name}` : 'New Category'}
            heading={isEdit ? `Edit · ${category.name}` : 'New Category'}
        >
            <Head title={`${isEdit ? 'Edit Category' : 'New Category'} — Admin — Trip Sathi`} />

            <div className="row g-4 justify-content-center">
                <div className="col-lg-8">
                    <form onSubmit={handleSubmit} className="card shadow-sm border-0">
                        <div className="card-body p-4">
                            <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Category Details</h3>

                            <div className="row g-3">
                                <div className="col-md-8">
                                    <label htmlFor="name" className="form-label fw-semibold">
                                        Category Name <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        id="name"
                                        className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                        value={data.name}
                                        onChange={(e) => setData('name', e.target.value)}
                                        required
                                    />
                                    {errors.name && <div className="invalid-feedback">{errors.name}</div>}
                                </div>

                                <div className="col-md-4">
                                    <label htmlFor="type" className="form-label fw-semibold">
                                        Section / Type <span className="text-danger">*</span>
                                    </label>
                                    <select
                                        id="type"
                                        className="form-select"
                                        value={data.type}
                                        onChange={(e) => setData('type', e.target.value)}
                                        required
                                    >
                                        {Object.entries(types).map(([k, label]) => (
                                            <option key={k} value={k}>{label}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="col-md-6">
                                    <label htmlFor="icon" className="form-label fw-semibold">Bootstrap Icon Class</label>
                                    <input
                                        id="icon"
                                        className="form-control"
                                        placeholder="e.g. bi-compass, bi-flower1"
                                        value={data.icon}
                                        onChange={(e) => setData('icon', e.target.value)}
                                    />
                                </div>

                                <div className="col-md-6">
                                    <label htmlFor="sort_order" className="form-label fw-semibold">Sort Order</label>
                                    <input
                                        id="sort_order"
                                        type="number"
                                        className="form-control"
                                        value={data.sort_order}
                                        onChange={(e) => setData('sort_order', parseInt(e.target.value, 10))}
                                    />
                                </div>

                                <div className="col-12">
                                    <label htmlFor="description" className="form-label fw-semibold">Description</label>
                                    <textarea
                                        id="description"
                                        rows="3"
                                        className="form-control"
                                        value={data.description}
                                        onChange={(e) => setData('description', e.target.value)}
                                    ></textarea>
                                </div>

                                <div className="col-12 border-top pt-3">
                                    <div className="d-flex gap-4">
                                        <div className="form-check">
                                            <input
                                                type="checkbox"
                                                id="is_active"
                                                className="form-check-input"
                                                checked={data.is_active}
                                                onChange={(e) => setData('is_active', e.target.checked)}
                                            />
                                            <label className="form-check-label fw-semibold" htmlFor="is_active">
                                                Active
                                            </label>
                                        </div>

                                        <div className="form-check">
                                            <input
                                                type="checkbox"
                                                id="is_featured"
                                                className="form-check-input"
                                                checked={data.is_featured}
                                                onChange={(e) => setData('is_featured', e.target.checked)}
                                            />
                                            <label className="form-check-label fw-semibold" htmlFor="is_featured">
                                                Featured
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div className="col-12 d-flex justify-content-end gap-2 mt-4">
                                    <Link className="btn btn-outline-secondary" href="/admin/categories">
                                        Cancel
                                    </Link>
                                    <button className="btn btn-accent" type="submit" disabled={processing}>
                                        {processing ? 'Saving…' : (isEdit ? 'Update Category' : 'Create Category')}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </AdminLayout>
    );
}
