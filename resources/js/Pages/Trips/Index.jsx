import React, { useState } from 'react';
import { Link, usePage, useForm, router } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import PageHero from '../../Components/PageHero';
import Pagination from '../../Components/Pagination';

export default function TripsIndex({
    trips,
    destinations = [],
    scope = 'community',
    canCreate = false,
    total = 0,
    filters = {},
}) {
    const { auth } = usePage().props;
    const [showModal, setShowModal] = useState(false);
    const [searchQuery, setSearchQuery] = useState(filters.q || '');

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        destination_id: '',
        start_date: '',
        end_date: '',
        travelers: 2,
        budget: '',
        description: '',
        is_public: true,
    });

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        router.get('/trips', { scope, q: searchQuery }, { preserveState: true });
    };

    const handleCreateTrip = (e) => {
        e.preventDefault();
        post('/trips', {
            onSuccess: () => {
                setShowModal(false);
                reset();
            },
        });
    };

    const getInitials = (name) => {
        if (!name) return 'TS';
        return name
            .split(' ')
            .map((p) => p.charAt(0).toUpperCase())
            .slice(0, 2)
            .join('');
    };

    const formatCurrency = (amt) => {
        if (!amt) return null;
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            maximumFractionDigits: 0,
        }).format(amt);
    };

    return (
        <AppLayout
            title="Trip Planner"
            description="Build multi-day travel itineraries, track your budget, and get inspired by community trips."
        >
            <PageHero
                image="/photos/banners/banner-trips.jpg"
                eyebrow="Plan day by day"
                title="Trip Planner"
                lede="Sketch your itinerary day by day, keep bookings attached to the right date, and borrow ideas from trips other travellers have made public."
                crumbs={[{ label: 'Home', url: '/' }, { label: 'Trip Planner' }]}
                stats={[
                    [`${total}+`, 'Public itineraries'],
                    ['Day-by-day', 'Planning'],
                    ['Synced', 'With your bookings'],
                ]}
            />

            <section className="ts-section py-5">
                <div className="container">
                    {/* Toolbar */}
                    <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div className="btn-group">
                            <Link
                                href="/trips?scope=community"
                                className={`btn ${scope === 'community' ? 'btn-primary' : 'btn-outline-primary'}`}
                            >
                                <i className="bi bi-people me-1"></i> Community trips
                            </Link>
                            {auth?.user && (
                                <Link
                                    href="/trips?scope=mine"
                                    className={`btn ${scope === 'mine' ? 'btn-primary' : 'btn-outline-primary'}`}
                                >
                                    <i className="bi bi-map me-1"></i> My trips
                                </Link>
                            )}
                        </div>

                        <div className="d-flex gap-2 align-items-center">
                            <form onSubmit={handleSearchSubmit} className="d-flex gap-1">
                                <input
                                    type="search"
                                    className="form-control"
                                    placeholder="Search trips…"
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                />
                                <button type="submit" className="btn btn-outline-secondary">
                                    <i className="bi bi-search"></i>
                                </button>
                            </form>

                            {auth?.user ? (
                                <button
                                    type="button"
                                    className="btn btn-accent text-nowrap"
                                    onClick={() => setShowModal(true)}
                                >
                                    <i className="bi bi-plus-lg me-1"></i> New trip
                                </button>
                            ) : (
                                <Link className="btn btn-accent text-nowrap" href="/login">
                                    <i className="bi bi-plus-lg me-1"></i> Sign in to plan
                                </Link>
                            )}
                        </div>
                    </div>

                    {/* Trips Grid */}
                    {trips.data?.length === 0 ? (
                        <div className="text-center py-5 card border-0 shadow-sm rounded-4">
                            <i className="bi bi-map fs-1 text-muted mb-2"></i>
                            <h4 className="fw-bold">No trips found in this view</h4>
                            <p className="text-muted">
                                {scope === 'mine'
                                    ? "You haven't created any trips yet. Click 'New trip' to sketch your first itinerary!"
                                    : 'Try another search query or create your own custom itinerary.'}
                            </p>
                            {auth?.user && (
                                <button
                                    className="btn btn-accent btn-sm mx-auto"
                                    onClick={() => setShowModal(true)}
                                >
                                    Create a Trip
                                </button>
                            )}
                        </div>
                    ) : (
                        <div className="row g-4">
                            {trips.data.map((trip) => (
                                <div key={trip.id} className="col-md-6 col-lg-4">
                                    <div className="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                                        <div className="position-relative" style={{ height: 180, background: '#1c2833' }}>
                                            {trip.cover_image ? (
                                                <img
                                                    src={trip.cover_image.startsWith('/') ? trip.cover_image : `/${trip.cover_image}`}
                                                    alt={trip.name}
                                                    className="w-100 h-100 object-fit-cover"
                                                />
                                            ) : (
                                                <div className="w-100 h-100 d-flex align-items-center justify-content-center text-white-50">
                                                    <i className="bi bi-geo-alt fs-1"></i>
                                                </div>
                                            )}
                                            {trip.destination && (
                                                <span className="badge bg-dark bg-opacity-75 position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill">
                                                    {trip.destination.name}
                                                </span>
                                            )}
                                        </div>

                                        <div className="card-body p-4 d-flex flex-column">
                                            <h3 className="h5 fw-bold mb-2">{trip.name}</h3>

                                            {trip.description && (
                                                <p className="text-muted small mb-3 flex-grow-1">
                                                    {trip.description}
                                                </p>
                                            )}

                                            <div className="d-flex flex-wrap gap-3 small text-muted mb-3">
                                                <span>
                                                    <i className="bi bi-calendar3 me-1"></i>
                                                    {trip.total_days} {trip.total_days === 1 ? 'day' : 'days'}
                                                </span>
                                                <span>
                                                    <i className="bi bi-people me-1"></i>
                                                    {trip.travelers} {trip.travelers === 1 ? 'traveler' : 'travelers'}
                                                </span>
                                                {trip.budget && (
                                                    <span>
                                                        <i className="bi bi-wallet2 me-1"></i>
                                                        Budget: {formatCurrency(trip.budget)}
                                                    </span>
                                                )}
                                            </div>

                                            <div className="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                                <div className="d-flex align-items-center gap-2">
                                                    <div
                                                        className="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center"
                                                        style={{ width: 32, height: 32, fontSize: '0.75rem' }}
                                                    >
                                                        {getInitials(trip.user?.name)}
                                                    </div>
                                                    <span className="small text-secondary fw-semibold">
                                                        {trip.user?.name?.split(' ')[0] || 'Traveller'}
                                                    </span>
                                                </div>

                                                <span className="badge bg-light text-secondary">
                                                    {trip.is_public ? 'Public' : 'Private'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}

                    <Pagination links={trips.links} />
                </div>
            </section>

            {/* Create Trip Modal */}
            {showModal && (
                <div
                    className="modal show d-block"
                    tabIndex="-1"
                    style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}
                >
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content border-0 rounded-4 shadow">
                            <div className="modal-header border-0 pb-0">
                                <h5 className="modal-title fw-bold">Create New Trip</h5>
                                <button
                                    type="button"
                                    className="btn-close"
                                    onClick={() => setShowModal(false)}
                                ></button>
                            </div>

                            <form onSubmit={handleCreateTrip}>
                                <div className="modal-body">
                                    <div className="mb-3">
                                        <label className="form-label small fw-semibold">Trip Name *</label>
                                        <input
                                            type="text"
                                            className={`form-control ${errors.name ? 'is-invalid' : ''}`}
                                            placeholder="e.g., Gujarat Pilgrimage & Beach Retreat"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            required
                                        />
                                        {errors.name && <div className="invalid-feedback">{errors.name}</div>}
                                    </div>

                                    <div className="mb-3">
                                        <label className="form-label small fw-semibold">Destination</label>
                                        <select
                                            className="form-select"
                                            value={data.destination_id}
                                            onChange={(e) => setData('destination_id', e.target.value)}
                                        >
                                            <option value="">Select destination (optional)</option>
                                            {destinations.map((d) => (
                                                <option key={d.id} value={d.id}>{d.name}</option>
                                            ))}
                                        </select>
                                    </div>

                                    <div className="row g-2 mb-3">
                                        <div className="col-6">
                                            <label className="form-label small fw-semibold">Start Date *</label>
                                            <input
                                                type="date"
                                                className={`form-control ${errors.start_date ? 'is-invalid' : ''}`}
                                                value={data.start_date}
                                                onChange={(e) => setData('start_date', e.target.value)}
                                                required
                                            />
                                            {errors.start_date && <div className="invalid-feedback">{errors.start_date}</div>}
                                        </div>

                                        <div className="col-6">
                                            <label className="form-label small fw-semibold">End Date *</label>
                                            <input
                                                type="date"
                                                className={`form-control ${errors.end_date ? 'is-invalid' : ''}`}
                                                value={data.end_date}
                                                onChange={(e) => setData('end_date', e.target.value)}
                                                required
                                            />
                                            {errors.end_date && <div className="invalid-feedback">{errors.end_date}</div>}
                                        </div>
                                    </div>

                                    <div className="row g-2 mb-3">
                                        <div className="col-6">
                                            <label className="form-label small fw-semibold">Travelers *</label>
                                            <input
                                                type="number"
                                                min="1"
                                                max="50"
                                                className="form-control"
                                                value={data.travelers}
                                                onChange={(e) => setData('travelers', e.target.value)}
                                                required
                                            />
                                        </div>

                                        <div className="col-6">
                                            <label className="form-label small fw-semibold">Budget (₹)</label>
                                            <input
                                                type="number"
                                                min="0"
                                                className="form-control"
                                                placeholder="e.g. 25000"
                                                value={data.budget}
                                                onChange={(e) => setData('budget', e.target.value)}
                                            />
                                        </div>
                                    </div>

                                    <div className="mb-3">
                                        <label className="form-label small fw-semibold">Notes / Description</label>
                                        <textarea
                                            className="form-control"
                                            rows="3"
                                            placeholder="What are you hoping to see or experience?"
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                        ></textarea>
                                    </div>

                                    <div className="form-check form-switch mb-2">
                                        <input
                                            className="form-check-input"
                                            type="checkbox"
                                            id="tripPublicCheck"
                                            checked={data.is_public}
                                            onChange={(e) => setData('is_public', e.target.checked)}
                                        />
                                        <label className="form-check-label small" htmlFor="tripPublicCheck">
                                            Share publicly with the Trip Sathi community
                                        </label>
                                    </div>
                                </div>

                                <div className="modal-footer border-0 pt-0">
                                    <button
                                        type="button"
                                        className="btn btn-outline-secondary"
                                        onClick={() => setShowModal(false)}
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        className="btn btn-accent px-4"
                                        disabled={processing}
                                    >
                                        {processing ? 'Creating...' : 'Create Trip'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
