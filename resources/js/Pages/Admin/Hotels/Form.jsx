import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Form({ hotel = {}, destinations = [], categories = [] }) {
    const isEdit = Boolean(hotel?.id);

    const { data, setData, post, processing, errors } = useForm({
        name: hotel?.name || '',
        destination_id: hotel?.destination_id || '',
        category_id: hotel?.category_id || '',
        address: hotel?.address || '',
        city: hotel?.city || '',
        state: hotel?.state || '',
        country: hotel?.country || 'India',
        postal_code: hotel?.postal_code || '',
        phone: hotel?.phone || '',
        email: hotel?.email || '',
        website: hotel?.website || '',
        description: hotel?.description || '',
        short_description: hotel?.short_description || '',
        star_rating: hotel?.star_rating || 3,
        check_in_time: hotel?.check_in_time || '14:00',
        check_out_time: hotel?.check_out_time || '11:00',
        is_featured: Boolean(hotel?.is_featured),
        is_active: hotel?.id ? Boolean(hotel?.is_active) : true,
        main_image: null,
        remove_image: false,
    });

    const [imagePreview, setImagePreview] = useState(hotel?.image_url || null);

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
            router.post(`/admin/hotels/${hotel.id}`, {
                _method: 'put',
                ...data,
            });
        } else {
            post('/admin/hotels');
        }
    };

    return (
        <AdminLayout
            title={isEdit ? `Edit ${hotel.name}` : 'New Hotel'}
            heading={isEdit ? `Edit · ${hotel.name}` : 'New Hotel'}
        >
            <Head title={`${isEdit ? 'Edit Hotel' : 'New Hotel'} — Admin — Trip Sathi`} />

            <form onSubmit={handleSubmit}>
                <div className="row g-4">
                    <div className="col-lg-8">
                        <div className="card shadow-sm border-0 mb-4">
                            <div className="card-body p-4">
                                <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Hotel Information</h3>

                                <div className="row g-3">
                                    <div className="col-md-8">
                                        <label htmlFor="name" className="form-label fw-semibold">
                                            Hotel Name <span className="text-danger">*</span>
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
                                            className={`form-select ${errors.category_id ? 'is-invalid' : ''}`}
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
                                        <label htmlFor="star_rating" className="form-label fw-semibold">Star Rating</label>
                                        <select
                                            id="star_rating"
                                            className="form-select"
                                            value={data.star_rating}
                                            onChange={(e) => setData('star_rating', parseInt(e.target.value, 10))}
                                        >
                                            {[1, 2, 3, 4, 5].map((s) => (
                                                <option key={s} value={s}>{s} Star{s > 1 ? 's' : ''}</option>
                                            ))}
                                        </select>
                                    </div>

                                    <div className="col-12">
                                        <label htmlFor="address" className="form-label fw-semibold">Address</label>
                                        <input
                                            id="address"
                                            className="form-control"
                                            value={data.address}
                                            onChange={(e) => setData('address', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-4">
                                        <label htmlFor="city" className="form-label fw-semibold">City</label>
                                        <input
                                            id="city"
                                            className="form-control"
                                            value={data.city}
                                            onChange={(e) => setData('city', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-4">
                                        <label htmlFor="state" className="form-label fw-semibold">State</label>
                                        <input
                                            id="state"
                                            className="form-control"
                                            value={data.state}
                                            onChange={(e) => setData('state', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-4">
                                        <label htmlFor="postal_code" className="form-label fw-semibold">Postal Code</label>
                                        <input
                                            id="postal_code"
                                            className="form-control"
                                            value={data.postal_code}
                                            onChange={(e) => setData('postal_code', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="phone" className="form-label fw-semibold">Phone</label>
                                        <input
                                            id="phone"
                                            className="form-control"
                                            value={data.phone}
                                            onChange={(e) => setData('phone', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="email" className="form-label fw-semibold">Email</label>
                                        <input
                                            id="email"
                                            type="email"
                                            className="form-control"
                                            value={data.email}
                                            onChange={(e) => setData('email', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="check_in_time" className="form-label fw-semibold">Check-in Time</label>
                                        <input
                                            id="check_in_time"
                                            type="time"
                                            className="form-control"
                                            value={data.check_in_time}
                                            onChange={(e) => setData('check_in_time', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="check_out_time" className="form-label fw-semibold">Check-out Time</label>
                                        <input
                                            id="check_out_time"
                                            type="time"
                                            className="form-control"
                                            value={data.check_out_time}
                                            onChange={(e) => setData('check_out_time', e.target.value)}
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
                                    <div className="small text-muted">Visible for reservations</div>
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
                                        Featured hotel
                                    </label>
                                </div>

                                <div className="d-grid gap-2">
                                    <button className="btn btn-accent" type="submit" disabled={processing}>
                                        {processing ? 'Saving…' : (isEdit ? 'Update Hotel' : 'Create Hotel')}
                                    </button>
                                    <Link className="btn btn-outline-secondary" href="/admin/hotels">
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
