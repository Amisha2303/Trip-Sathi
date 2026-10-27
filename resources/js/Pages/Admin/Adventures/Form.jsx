import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Form({ adventure = {}, destinations = [], categories = [], difficulties = {} }) {
    const isEdit = Boolean(adventure?.id);

    const { data, setData, post, processing, errors } = useForm({
        name: adventure?.name || '',
        destination_id: adventure?.destination_id || '',
        category_id: adventure?.category_id || '',
        activity_type: adventure?.activity_type || '',
        difficulty: adventure?.difficulty || 'easy',
        duration_hours: adventure?.duration_hours || '',
        min_age: adventure?.min_age || 10,
        max_participants: adventure?.max_participants || 12,
        price: adventure?.price || '',
        discount_price: adventure?.discount_price || '',
        meeting_point: adventure?.meeting_point || '',
        description: adventure?.description || '',
        short_description: adventure?.short_description || '',
        safety_guidelines: adventure?.safety_guidelines || '',
        is_featured: Boolean(adventure?.is_featured),
        is_active: adventure?.id ? Boolean(adventure?.is_active) : true,
        main_image: null,
        remove_image: false,
    });

    const [imagePreview, setImagePreview] = useState(adventure?.image_url || null);

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
            router.post(`/admin/adventures/${adventure.id}`, {
                _method: 'put',
                ...data,
            });
        } else {
            post('/admin/adventures');
        }
    };

    return (
        <AdminLayout
            title={isEdit ? `Edit ${adventure.name}` : 'New Adventure'}
            heading={isEdit ? `Edit · ${adventure.name}` : 'New Adventure'}
        >
            <Head title={`${isEdit ? 'Edit Adventure' : 'New Adventure'} — Admin — Trip Sathi`} />

            <form onSubmit={handleSubmit}>
                <div className="row g-4">
                    <div className="col-lg-8">
                        <div className="card shadow-sm border-0 mb-4">
                            <div className="card-body p-4">
                                <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Adventure Info</h3>

                                <div className="row g-3">
                                    <div className="col-md-8">
                                        <label htmlFor="name" className="form-label fw-semibold">
                                            Title <span className="text-danger">*</span>
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
                                        <label htmlFor="destination_id" className="form-label fw-semibold">
                                            Destination <span className="text-danger">*</span>
                                        </label>
                                        <select
                                            id="destination_id"
                                            className={`form-select ${errors.destination_id ? 'is-invalid' : ''}`}
                                            value={data.destination_id}
                                            onChange={(e) => setData('destination_id', e.target.value)}
                                            required
                                        >
                                            <option value="">— Select —</option>
                                            {destinations.map((d) => (
                                                <option key={d.id} value={d.id}>{d.name}</option>
                                            ))}
                                        </select>
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="activity_type" className="form-label fw-semibold">Activity Type</label>
                                        <input
                                            id="activity_type"
                                            className="form-control"
                                            placeholder="e.g. Trekking, River Rafting, Paragliding"
                                            value={data.activity_type}
                                            onChange={(e) => setData('activity_type', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="difficulty" className="form-label fw-semibold">Difficulty Level</label>
                                        <select
                                            id="difficulty"
                                            className="form-select"
                                            value={data.difficulty}
                                            onChange={(e) => setData('difficulty', e.target.value)}
                                        >
                                            {Object.entries(difficulties).map(([k, label]) => (
                                                <option key={k} value={k}>{label}</option>
                                            ))}
                                        </select>
                                    </div>

                                    <div className="col-md-4">
                                        <label htmlFor="duration_hours" className="form-label fw-semibold">Duration (Hours)</label>
                                        <input
                                            id="duration_hours"
                                            type="number"
                                            step="0.5"
                                            className="form-control"
                                            value={data.duration_hours}
                                            onChange={(e) => setData('duration_hours', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-4">
                                        <label htmlFor="price" className="form-label fw-semibold">
                                            Price (₹) <span className="text-danger">*</span>
                                        </label>
                                        <input
                                            id="price"
                                            type="number"
                                            className={`form-control ${errors.price ? 'is-invalid' : ''}`}
                                            value={data.price}
                                            onChange={(e) => setData('price', e.target.value)}
                                            required
                                        />
                                    </div>

                                    <div className="col-md-4">
                                        <label htmlFor="discount_price" className="form-label fw-semibold">Discounted Price (₹)</label>
                                        <input
                                            id="discount_price"
                                            type="number"
                                            className="form-control"
                                            value={data.discount_price}
                                            onChange={(e) => setData('discount_price', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="min_age" className="form-label fw-semibold">Min Age</label>
                                        <input
                                            id="min_age"
                                            type="number"
                                            className="form-control"
                                            value={data.min_age}
                                            onChange={(e) => setData('min_age', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="max_participants" className="form-label fw-semibold">Max Group Size</label>
                                        <input
                                            id="max_participants"
                                            type="number"
                                            className="form-control"
                                            value={data.max_participants}
                                            onChange={(e) => setData('max_participants', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-12">
                                        <label htmlFor="meeting_point" className="form-label fw-semibold">Meeting Point</label>
                                        <input
                                            id="meeting_point"
                                            className="form-control"
                                            value={data.meeting_point}
                                            onChange={(e) => setData('meeting_point', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-12">
                                        <label htmlFor="short_description" className="form-label fw-semibold">Short Description</label>
                                        <input
                                            id="short_description"
                                            className="form-control"
                                            value={data.short_description}
                                            onChange={(e) => setData('short_description', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-12">
                                        <label htmlFor="description" className="form-label fw-semibold">Detailed Description</label>
                                        <textarea
                                            id="description"
                                            rows="4"
                                            className="form-control"
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                        ></textarea>
                                    </div>

                                    <div className="col-12">
                                        <label htmlFor="safety_guidelines" className="form-label fw-semibold">Safety Guidelines</label>
                                        <textarea
                                            id="safety_guidelines"
                                            rows="3"
                                            className="form-control"
                                            placeholder="Equipment provided, physical fitness requirements, what to wear..."
                                            value={data.safety_guidelines}
                                            onChange={(e) => setData('safety_guidelines', e.target.value)}
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="col-lg-4">
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
                            </div>
                        </div>

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
                                        Featured adventure
                                    </label>
                                </div>

                                <div className="d-grid gap-2">
                                    <button className="btn btn-accent" type="submit" disabled={processing}>
                                        {processing ? 'Saving…' : (isEdit ? 'Update Adventure' : 'Create Adventure')}
                                    </button>
                                    <Link className="btn btn-outline-secondary" href="/admin/adventures">
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
