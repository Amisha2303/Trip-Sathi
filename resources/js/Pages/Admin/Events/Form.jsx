import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Form({ event = {}, destinations = [], categories = [] }) {
    const isEdit = Boolean(event?.id);

    const { data, setData, post, processing, errors } = useForm({
        name: event?.name || '',
        destination_id: event?.destination_id || '',
        category_id: event?.category_id || '',
        venue: event?.venue || '',
        address: event?.address || '',
        city: event?.city || '',
        event_date: event?.event_date ? event.event_date.substring(0, 10) : '',
        starts_at: event?.starts_at ? event.starts_at.substring(11, 16) : '',
        ends_at: event?.ends_at ? event.ends_at.substring(11, 16) : '',
        organiser: event?.organiser || '',
        description: event?.description || '',
        short_description: event?.short_description || '',
        is_featured: Boolean(event?.is_featured),
        is_active: event?.id ? Boolean(event?.is_active) : true,
        main_image: null,
        remove_image: false,
    });

    const [imagePreview, setImagePreview] = useState(event?.image_url || null);

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
            router.post(`/admin/events/${event.id}`, {
                _method: 'put',
                ...data,
            });
        } else {
            post('/admin/events');
        }
    };

    return (
        <AdminLayout
            title={isEdit ? `Edit ${event.name}` : 'New Event'}
            heading={isEdit ? `Edit · ${event.name}` : 'New Event'}
        >
            <Head title={`${isEdit ? 'Edit Event' : 'New Event'} — Admin — Trip Sathi`} />

            <form onSubmit={handleSubmit}>
                <div className="row g-4">
                    <div className="col-lg-8">
                        <div className="card shadow-sm border-0 mb-4">
                            <div className="card-body p-4">
                                <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Event Information</h3>

                                <div className="row g-3">
                                    <div className="col-md-8">
                                        <label htmlFor="name" className="form-label fw-semibold">
                                            Event Name <span className="text-danger">*</span>
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
                                        {errors.destination_id && <div className="invalid-feedback">{errors.destination_id}</div>}
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="category_id" className="form-label fw-semibold">Category</label>
                                        <select
                                            id="category_id"
                                            className="form-select"
                                            value={data.category_id}
                                            onChange={(e) => setData('category_id', e.target.value)}
                                        >
                                            <option value="">— None —</option>
                                            {categories.map((c) => (
                                                <option key={c.id} value={c.id}>{c.name}</option>
                                            ))}
                                        </select>
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="event_date" className="form-label fw-semibold">
                                            Event Date <span className="text-danger">*</span>
                                        </label>
                                        <input
                                            id="event_date"
                                            type="date"
                                            className={`form-control ${errors.event_date ? 'is-invalid' : ''}`}
                                            value={data.event_date}
                                            onChange={(e) => setData('event_date', e.target.value)}
                                            required
                                        />
                                        {errors.event_date && <div className="invalid-feedback">{errors.event_date}</div>}
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="starts_at" className="form-label fw-semibold">Starts At</label>
                                        <input
                                            id="starts_at"
                                            type="time"
                                            className="form-control"
                                            value={data.starts_at}
                                            onChange={(e) => setData('starts_at', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="ends_at" className="form-label fw-semibold">Ends At</label>
                                        <input
                                            id="ends_at"
                                            type="time"
                                            className="form-control"
                                            value={data.ends_at}
                                            onChange={(e) => setData('ends_at', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="venue" className="form-label fw-semibold">Venue</label>
                                        <input
                                            id="venue"
                                            className="form-control"
                                            value={data.venue}
                                            onChange={(e) => setData('venue', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="city" className="form-label fw-semibold">City</label>
                                        <input
                                            id="city"
                                            className="form-control"
                                            value={data.city}
                                            onChange={(e) => setData('city', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-12">
                                        <label htmlFor="organiser" className="form-label fw-semibold">Organiser</label>
                                        <input
                                            id="organiser"
                                            className="form-control"
                                            value={data.organiser}
                                            onChange={(e) => setData('organiser', e.target.value)}
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
                                            rows="5"
                                            className="form-control"
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                        ></textarea>
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
                                        Featured event
                                    </label>
                                </div>

                                <div className="d-grid gap-2">
                                    <button className="btn btn-accent" type="submit" disabled={processing}>
                                        {processing ? 'Saving…' : (isEdit ? 'Update Event' : 'Create Event')}
                                    </button>
                                    <Link className="btn btn-outline-secondary" href="/admin/events">
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
