import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Form({ destination = {}, categories = [] }) {
    const isEdit = Boolean(destination?.id);

    const { data, setData, post, processing, errors } = useForm({
        name: destination?.name || '',
        category_id: destination?.category_id || '',
        country: destination?.country || 'India',
        state: destination?.state || '',
        city: destination?.city || '',
        short_description: destination?.short_description || '',
        description: destination?.description || '',
        best_time_to_visit: destination?.best_time_to_visit || '',
        latitude: destination?.latitude || '',
        longitude: destination?.longitude || '',
        language: destination?.language || '',
        is_featured: Boolean(destination?.is_featured),
        is_active: destination?.id ? Boolean(destination?.is_active) : true,
        main_image: null,
        remove_image: false,
    });

    const [imagePreview, setImagePreview] = useState(destination?.image_url || null);

    const handleImageChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('main_image', file);
            setData('remove_image', false);
            setImagePreview(URL.createObjectURL(file));
        }
    };

    const handleRemoveImage = () => {
        setData('main_image', null);
        setData('remove_image', true);
        setImagePreview(null);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (isEdit) {
            router.post(`/admin/destinations/${destination.id}`, {
                _method: 'put',
                ...data,
            });
        } else {
            post('/admin/destinations');
        }
    };

    return (
        <AdminLayout
            title={isEdit ? `Edit ${destination.name}` : 'New Destination'}
            heading={isEdit ? `Edit · ${destination.name}` : 'New Destination'}
            breadcrumbs={[
                { label: 'Admin', href: '/admin/dashboard' },
                { label: 'Destinations', href: '/admin/destinations' },
                { label: isEdit ? destination.name : 'New' },
            ]}
        >
            <Head title={`${isEdit ? 'Edit Destination' : 'New Destination'} — Admin — Trip Sathi`} />

            <form onSubmit={handleSubmit}>
                <div className="row g-4">
                    <div className="col-lg-8">
                        <div className="card shadow-sm border-0 mb-4">
                            <div className="card-body p-4">
                                <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Basic Info</h3>

                                <div className="row g-3">
                                    <div className="col-md-8">
                                        <label htmlFor="name" className="form-label fw-semibold">
                                            Name <span className="text-danger">*</span>
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
                                        <label htmlFor="category_id" className="form-label fw-semibold">Category</label>
                                        <select
                                            id="category_id"
                                            className={`form-select ${errors.category_id ? 'is-invalid' : ''}`}
                                            value={data.category_id}
                                            onChange={(e) => setData('category_id', e.target.value)}
                                        >
                                            <option value="">— None —</option>
                                            {categories.map((c) => (
                                                <option key={c.id} value={c.id}>{c.name}</option>
                                            ))}
                                        </select>
                                        {errors.category_id && <div className="invalid-feedback">{errors.category_id}</div>}
                                    </div>

                                    <div className="col-md-4">
                                        <label htmlFor="country" className="form-label fw-semibold">
                                            Country <span className="text-danger">*</span>
                                        </label>
                                        <input
                                            id="country"
                                            className={`form-control ${errors.country ? 'is-invalid' : ''}`}
                                            value={data.country}
                                            onChange={(e) => setData('country', e.target.value)}
                                            required
                                        />
                                        {errors.country && <div className="invalid-feedback">{errors.country}</div>}
                                    </div>

                                    <div className="col-md-4">
                                        <label htmlFor="state" className="form-label fw-semibold">State</label>
                                        <input
                                            id="state"
                                            className={`form-control ${errors.state ? 'is-invalid' : ''}`}
                                            value={data.state}
                                            onChange={(e) => setData('state', e.target.value)}
                                        />
                                        {errors.state && <div className="invalid-feedback">{errors.state}</div>}
                                    </div>

                                    <div className="col-md-4">
                                        <label htmlFor="city" className="form-label fw-semibold">City</label>
                                        <input
                                            id="city"
                                            className={`form-control ${errors.city ? 'is-invalid' : ''}`}
                                            value={data.city}
                                            onChange={(e) => setData('city', e.target.value)}
                                        />
                                        {errors.city && <div className="invalid-feedback">{errors.city}</div>}
                                    </div>

                                    <div className="col-12">
                                        <label htmlFor="short_description" className="form-label fw-semibold">Short Description</label>
                                        <input
                                            id="short_description"
                                            maxLength="500"
                                            className={`form-control ${errors.short_description ? 'is-invalid' : ''}`}
                                            value={data.short_description}
                                            onChange={(e) => setData('short_description', e.target.value)}
                                        />
                                        {errors.short_description && <div className="invalid-feedback">{errors.short_description}</div>}
                                    </div>

                                    <div className="col-12">
                                        <label htmlFor="description" className="form-label fw-semibold">Detailed Description</label>
                                        <textarea
                                            id="description"
                                            rows="5"
                                            className={`form-control ${errors.description ? 'is-invalid' : ''}`}
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                        ></textarea>
                                        {errors.description && <div className="invalid-feedback">{errors.description}</div>}
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="best_time_to_visit" className="form-label fw-semibold">Best Time to Visit</label>
                                        <input
                                            id="best_time_to_visit"
                                            className="form-control"
                                            placeholder="e.g. October to March"
                                            value={data.best_time_to_visit}
                                            onChange={(e) => setData('best_time_to_visit', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="language" className="form-label fw-semibold">Languages Spoken</label>
                                        <input
                                            id="language"
                                            className="form-control"
                                            placeholder="e.g. Hindi, Gujarati, English"
                                            value={data.language}
                                            onChange={(e) => setData('language', e.target.value)}
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="col-lg-4">
                        {/* Image card */}
                        <div className="card shadow-sm border-0 mb-4">
                            <div className="card-body p-4">
                                <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Cover Image</h3>

                                {imagePreview && (
                                    <div className="mb-3 position-relative">
                                        <img
                                            src={imagePreview}
                                            alt="Preview"
                                            className="img-fluid rounded w-100 object-fit-cover"
                                            style={{ maxHeight: '200px' }}
                                        />
                                        <button
                                            type="button"
                                            className="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                            onClick={handleRemoveImage}
                                        >
                                            <i className="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                )}

                                <input
                                    type="file"
                                    className={`form-control ${errors.main_image ? 'is-invalid' : ''}`}
                                    accept="image/*"
                                    onChange={handleImageChange}
                                />
                                {errors.main_image && <div className="invalid-feedback">{errors.main_image}</div>}
                            </div>
                        </div>

                        {/* Visibility card */}
                        <div className="card shadow-sm border-0 mb-4">
                            <div className="card-body p-4">
                                <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Publishing</h3>

                                <div className="form-check mb-3">
                                    <input
                                        type="checkbox"
                                        id="is_active"
                                        className="form-check-input"
                                        checked={data.is_active}
                                        onChange={(e) => setData('is_active', e.target.checked)}
                                    />
                                    <label className="form-check-label fw-semibold" htmlFor="is_active">
                                        Active listing
                                    </label>
                                    <div className="small text-muted">Visible to travelers on the site</div>
                                </div>

                                <div className="form-check mb-4">
                                    <input
                                        type="checkbox"
                                        id="is_featured"
                                        className="form-check-input"
                                        checked={data.is_featured}
                                        onChange={(e) => setData('is_featured', e.target.checked)}
                                    />
                                    <label className="form-check-label fw-semibold" htmlFor="is_featured">
                                        Featured on homepage
                                    </label>
                                </div>

                                <div className="d-grid gap-2">
                                    <button className="btn btn-accent" type="submit" disabled={processing}>
                                        {processing ? 'Saving…' : (isEdit ? 'Update Destination' : 'Create Destination')}
                                    </button>
                                    <Link className="btn btn-outline-secondary" href="/admin/destinations">
                                        Cancel
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </AdminLayout>
    );
}
