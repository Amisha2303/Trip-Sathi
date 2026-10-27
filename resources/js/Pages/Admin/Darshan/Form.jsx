import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

export default function Form({ darshan = {}, destinations = [], categories = [], types = {} }) {
    const isEdit = Boolean(darshan?.id);

    const { data, setData, post, processing, errors } = useForm({
        name: darshan?.name || '',
        destination_id: darshan?.destination_id || '',
        category_id: darshan?.category_id || '',
        temple_name: darshan?.temple_name || '',
        deity: darshan?.deity || '',
        type: darshan?.type || 'vip',
        price: darshan?.price || '',
        discount_price: darshan?.discount_price || '',
        duration_minutes: darshan?.duration_minutes || 60,
        dress_code: darshan?.dress_code || '',
        timings: darshan?.timings || '',
        prasad_included: Boolean(darshan?.prasad_included),
        requires_id_proof: Boolean(darshan?.requires_id_proof),
        description: darshan?.description || '',
        short_description: darshan?.short_description || '',
        guidelines: darshan?.guidelines || '',
        is_featured: Boolean(darshan?.is_featured),
        is_active: darshan?.id ? Boolean(darshan?.is_active) : true,
        main_image: null,
        remove_image: false,
    });

    const [imagePreview, setImagePreview] = useState(darshan?.image_url || null);

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
            router.post(`/admin/darshan/${darshan.id}`, {
                _method: 'put',
                ...data,
            });
        } else {
            post('/admin/darshan');
        }
    };

    return (
        <AdminLayout
            title={isEdit ? `Edit ${darshan.name}` : 'New VIP Darshan'}
            heading={isEdit ? `Edit · ${darshan.name}` : 'New VIP Darshan'}
        >
            <Head title={`${isEdit ? 'Edit Darshan' : 'New VIP Darshan'} — Admin — Trip Sathi`} />

            <form onSubmit={handleSubmit}>
                <div className="row g-4">
                    <div className="col-lg-8">
                        <div className="card shadow-sm border-0 mb-4">
                            <div className="card-body p-4">
                                <h3 className="h6 fw-bold mb-3 border-bottom pb-2">Temple &amp; Darshan Info</h3>

                                <div className="row g-3">
                                    <div className="col-md-8">
                                        <label htmlFor="name" className="form-label fw-semibold">
                                            Listing Name <span className="text-danger">*</span>
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
                                            className="form-select"
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
                                        <label htmlFor="temple_name" className="form-label fw-semibold">Temple Name</label>
                                        <input
                                            id="temple_name"
                                            className="form-control"
                                            value={data.temple_name}
                                            onChange={(e) => setData('temple_name', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="deity" className="form-label fw-semibold">Presiding Deity</label>
                                        <input
                                            id="deity"
                                            className="form-control"
                                            placeholder="e.g. Lord Krishna, Lord Shiva"
                                            value={data.deity}
                                            onChange={(e) => setData('deity', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-4">
                                        <label htmlFor="type" className="form-label fw-semibold">Darshan Category</label>
                                        <select
                                            id="type"
                                            className="form-select"
                                            value={data.type}
                                            onChange={(e) => setData('type', e.target.value)}
                                        >
                                            {Object.entries(types).map(([k, label]) => (
                                                <option key={k} value={k}>{label}</option>
                                            ))}
                                        </select>
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
                                        <label htmlFor="discount_price" className="form-label fw-semibold">Special Price (₹)</label>
                                        <input
                                            id="discount_price"
                                            type="number"
                                            className="form-control"
                                            value={data.discount_price}
                                            onChange={(e) => setData('discount_price', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="timings" className="form-label fw-semibold">Timings</label>
                                        <input
                                            id="timings"
                                            className="form-control"
                                            placeholder="e.g. 6:00 AM - 12:00 PM, 4:00 PM - 9:00 PM"
                                            value={data.timings}
                                            onChange={(e) => setData('timings', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label htmlFor="dress_code" className="form-label fw-semibold">Dress Code</label>
                                        <input
                                            id="dress_code"
                                            className="form-control"
                                            placeholder="e.g. Traditional Indian attire required"
                                            value={data.dress_code}
                                            onChange={(e) => setData('dress_code', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-12 border-top pt-3">
                                        <div className="d-flex gap-4">
                                            <div className="form-check">
                                                <input
                                                    type="checkbox"
                                                    id="prasad_included"
                                                    className="form-check-input"
                                                    checked={data.prasad_included}
                                                    onChange={(e) => setData('prasad_included', e.target.checked)}
                                                />
                                                <label className="form-check-label" htmlFor="prasad_included">
                                                    Special Prasad Packet Included
                                                </label>
                                            </div>

                                            <div className="form-check">
                                                <input
                                                    type="checkbox"
                                                    id="requires_id_proof"
                                                    className="form-check-input"
                                                    checked={data.requires_id_proof}
                                                    onChange={(e) => setData('requires_id_proof', e.target.checked)}
                                                />
                                                <label className="form-check-label" htmlFor="requires_id_proof">
                                                    Government ID Proof Required
                                                </label>
                                            </div>
                                        </div>
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
                                        Featured darshan
                                    </label>
                                </div>

                                <div className="d-grid gap-2">
                                    <button className="btn btn-accent" type="submit" disabled={processing}>
                                        {processing ? 'Saving…' : (isEdit ? 'Update Darshan' : 'Create Darshan')}
                                    </button>
                                    <Link className="btn btn-outline-secondary" href="/admin/darshan">
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
