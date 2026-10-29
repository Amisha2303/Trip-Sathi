import React, { useState, useEffect } from 'react';
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
    const [showCalculator, setShowCalculator] = useState(false);
    const [showChecklist, setShowChecklist] = useState(false);
    const [searchQuery, setSearchQuery] = useState(filters.q || '');

    // Calculator state
    const [calcDays, setCalcDays] = useState(3);
    const [calcTravelers, setCalcTravelers] = useState(2);
    const [calcTier, setCalcTier] = useState('moderate'); // budget: 1800, moderate: 4200, luxury: 9500

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

    // Auto-open create modal if action=create is passed in URL
    useEffect(() => {
        if (typeof window !== 'undefined') {
            const params = new URLSearchParams(window.location.search);
            if (params.get('action') === 'create') {
                if (auth?.user) {
                    setShowModal(true);
                }
                const destSlug = params.get('destination');
                if (destSlug && destinations.length > 0) {
                    const match = destinations.find(
                        (d) => d.slug === destSlug || String(d.id) === destSlug
                    );
                    if (match) {
                        setData((prev) => ({
                            ...prev,
                            destination_id: match.id,
                            name: `${match.name} Odyssey`,
                        }));
                    }
                }
            }
        }
    }, [destinations, auth?.user]);

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

    // Calculate budget breakdown
    const tierRates = {
        budget: { stay: 800, food: 500, transport: 300, activity: 200 },
        moderate: { stay: 2000, food: 1000, transport: 700, activity: 500 },
        luxury: { stay: 5500, food: 2200, transport: 1200, activity: 1100 },
    };

    const rates = tierRates[calcTier] || tierRates.moderate;
    const stayTotal = rates.stay * calcDays * Math.ceil(calcTravelers / 2);
    const foodTotal = rates.food * calcDays * calcTravelers;
    const transportTotal = rates.transport * calcDays * calcTravelers;
    const activityTotal = rates.activity * calcDays * calcTravelers;
    const estimatedTotal = stayTotal + foodTotal + transportTotal + activityTotal;

    const applyCalculatedBudget = () => {
        setData('budget', estimatedTotal);
        setData('travelers', calcTravelers);
        setShowCalculator(false);
        if (!showModal) setShowModal(true);
    };

    const checklistItems = [
        { category: 'Documentation', items: ['Government ID (Aadhaar/Passport)', 'Flight/Train tickets & boarding passes', 'Hotel & Darshan voucher confirmations', 'Emergency cash & debit/credit cards'] },
        { category: 'Health & Essentials', items: ['Personal medications & basic first aid kit', 'Power bank & multi-port charging adapter', 'Sunscreen, lip balm & insect repellent', 'Reusable water bottle & dry snacks'] },
        { category: 'Destination Specific', items: ['Thermal layers for Kashmir & Ladakh', 'Cotton wear & beach towel for Goa', 'Conservative attire for temple darshans', 'Comfortable walking shoes for heritage forts'] },
    ];

    return (
        <AppLayout
            title="Trip Planner — Build Custom Itineraries"
            description="Build multi-day travel itineraries, track your budget, and get inspired by community trips with Trip Sathi."
        >
            <PageHero
                image="/photos/banners/banner-trips.jpg"
                eyebrow="Plan Day By Day"
                title="Trip Planner"
                lede="Sketch your itinerary day by day, estimate expenses with our built-in budget calculator, and bring your travel dreams to life."
                crumbs={[{ label: 'Home', url: '/' }, { label: 'Trip Planner' }]}
                stats={[
                    [`${total}+`, 'Itineraries'],
                    ['Budget Tool', 'Built-in Calculator'],
                    ['Sync Ready', 'With Bookings'],
                ]}
            />

            <section className="py-5" style={{ backgroundColor: '#F7F8F5' }}>
                <div className="container">
                    {/* Top Planner Controls & Utilities */}
                    <div className="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div className="d-flex align-items-center gap-2">
                            <div className="btn-group p-1 rounded-pill" style={{ backgroundColor: '#FFFFFF', border: '1px solid #E2E8F0' }}>
                                <Link
                                    href="/trips?scope=community"
                                    className={`btn btn-sm rounded-pill px-3 py-1.5 fw-semibold ${scope === 'community' ? 'text-white' : 'text-dark'}`}
                                    style={{
                                        backgroundColor: scope === 'community' ? '#0F9D91' : 'transparent',
                                        transition: 'all 0.18s ease'
                                    }}
                                >
                                    <i className="bi bi-compass me-1.5"></i> Community Trips
                                </Link>
                                {auth?.user && (
                                    <Link
                                        href="/trips?scope=mine"
                                        className={`btn btn-sm rounded-pill px-3 py-1.5 fw-semibold ${scope === 'mine' ? 'text-white' : 'text-dark'}`}
                                        style={{
                                            backgroundColor: scope === 'mine' ? '#0F9D91' : 'transparent',
                                            transition: 'all 0.18s ease'
                                        }}
                                    >
                                        <i className="bi bi-map me-1.5"></i> My Saved Trips
                                    </Link>
                                )}
                            </div>

                            {/* Utility Buttons: Budget Calculator & Travel Checklist */}
                            <button
                                type="button"
                                className="btn btn-sm btn-white border rounded-pill px-3 py-1.5 fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5"
                                onClick={() => setShowCalculator(true)}
                                style={{ backgroundColor: '#FFFFFF', borderColor: '#CBD5E1', color: '#16324F' }}
                            >
                                <i className="bi bi-calculator" style={{ color: '#0F9D91' }}></i>
                                <span>Budget Calculator</span>
                            </button>

                            <button
                                type="button"
                                className="btn btn-sm btn-white border rounded-pill px-3 py-1.5 fw-semibold shadow-sm d-inline-flex align-items-center gap-1.5"
                                onClick={() => setShowChecklist(true)}
                                style={{ backgroundColor: '#FFFFFF', borderColor: '#CBD5E1', color: '#16324F' }}
                            >
                                <i className="bi bi-check2-square" style={{ color: '#FF795D' }}></i>
                                <span>Travel Checklist</span>
                            </button>
                        </div>

                        {/* Search & New Trip CTA */}
                        <div className="d-flex gap-2 align-items-center">
                            <form onSubmit={handleSearchSubmit} className="d-flex gap-1.5">
                                <input
                                    type="search"
                                    className="form-control form-control-sm rounded-pill px-3 border"
                                    placeholder="Search itineraries..."
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    style={{ borderColor: '#CBD5E1', width: '180px' }}
                                />
                                <button type="submit" className="btn btn-sm btn-light border rounded-circle" style={{ width: '34px', height: '34px' }} aria-label="Search">
                                    <i className="bi bi-search"></i>
                                </button>
                            </form>

                            {auth?.user ? (
                                <button
                                    type="button"
                                    className="btn btn-sm fw-bold px-3.5 py-1.5 rounded-pill text-white shadow-sm d-inline-flex align-items-center gap-1.5"
                                    onClick={() => setShowModal(true)}
                                    style={{ backgroundColor: '#0F9D91', borderColor: '#0F9D91' }}
                                >
                                    <i className="bi bi-plus-lg"></i>
                                    <span>Plan New Trip</span>
                                </button>
                            ) : (
                                <Link
                                    className="btn btn-sm fw-bold px-3.5 py-1.5 rounded-pill text-white shadow-sm d-inline-flex align-items-center gap-1.5"
                                    href="/login"
                                    style={{ backgroundColor: '#0F9D91', borderColor: '#0F9D91' }}
                                >
                                    <i className="bi bi-send-fill"></i>
                                    <span>Sign in to Plan</span>
                                </Link>
                            )}
                        </div>
                    </div>

                    {/* Trips Grid */}
                    {trips.data?.length === 0 ? (
                        <div className="text-center py-5 card border-0 shadow-sm rounded-4 p-5" style={{ backgroundColor: '#FFFFFF', border: '1px solid #E2E8F0' }}>
                            <div className="mx-auto mb-3" style={{ width: '64px', height: '64px', borderRadius: '50%', backgroundColor: 'rgba(15, 157, 145, 0.1)', color: '#0F9D91', display: 'inline-flex', alignItems: 'center', justifyContent: 'center', fontSize: '1.8rem' }}>
                                <i className="bi bi-map"></i>
                            </div>
                            <h4 className="fw-bold mb-2" style={{ color: '#16324F' }}>No Itineraries Found</h4>
                            <p className="text-muted mx-auto mb-4" style={{ maxWidth: '48ch' }}>
                                {scope === 'mine'
                                    ? "You haven't saved any trips yet. Click 'Plan New Trip' to start sketching your journey with custom days and budgets!"
                                    : 'No public itineraries match your search. Try another query or create your own custom roadmap.'}
                            </p>
                            {auth?.user ? (
                                <button
                                    className="btn btn-sm fw-bold px-4 py-2 rounded-pill text-white mx-auto shadow-sm"
                                    onClick={() => setShowModal(true)}
                                    style={{ backgroundColor: '#0F9D91', borderColor: '#0F9D91' }}
                                >
                                    <i className="bi bi-plus-lg me-1.5"></i> Create My First Trip
                                </button>
                            ) : (
                                <Link className="btn btn-sm fw-bold px-4 py-2 rounded-pill text-white mx-auto shadow-sm" href="/login" style={{ backgroundColor: '#0F9D91', borderColor: '#0F9D91' }}>
                                    Sign In to Plan
                                </Link>
                            )}
                        </div>
                    ) : (
                        <div className="row g-4">
                            {trips.data.map((trip) => (
                                <div key={trip.id} className="col-12 col-md-6 col-lg-4">
                                    <div className="card h-100 border-0 shadow-sm rounded-4 overflow-hidden d-flex flex-column" style={{ backgroundColor: '#FFFFFF', border: '1px solid #E2E8F0' }}>
                                        {/* Cover Image */}
                                        <div className="position-relative" style={{ height: 190, backgroundColor: '#16324F', overflow: 'hidden' }}>
                                            <img
                                                src={trip.cover_image ? (trip.cover_image.startsWith('/') ? trip.cover_image : `/${trip.cover_image}`) : (trip.destination?.main_image ? (trip.destination.main_image.startsWith('/') ? trip.destination.main_image : `/${trip.destination.main_image}`) : '/photos/destination-goa.jpg')}
                                                alt={trip.name}
                                                className="w-100 h-100 object-fit-cover"
                                                loading="lazy"
                                            />
                                            {trip.destination && (
                                                <span className="badge rounded-pill position-absolute top-0 end-0 m-3 px-3 py-1.5 fw-semibold" style={{ backgroundColor: 'rgba(22, 50, 79, 0.88)', backdropFilter: 'blur(4px)', color: '#fff', fontSize: '0.74rem' }}>
                                                    <i className="bi bi-geo-alt me-1"></i>
                                                    {trip.destination.name}
                                                </span>
                                            )}
                                        </div>

                                        {/* Body */}
                                        <div className="p-4 d-flex flex-column flex-grow-1">
                                            <div className="d-flex justify-content-between align-items-start mb-2">
                                                <h3 className="h5 fw-bold mb-0" style={{ color: '#16324F' }}>
                                                    {trip.name}
                                                </h3>
                                            </div>

                                            <p className="small text-muted mb-3 flex-grow-1" style={{ minHeight: '38px', lineHeight: 1.5 }}>
                                                {trip.description || 'Custom multi-day journey planned with Trip Sathi.'}
                                            </p>

                                            <div className="d-flex flex-wrap gap-2.5 small text-muted mb-3 p-2.5 rounded-3" style={{ backgroundColor: '#F8FAFC', border: '1px solid #F1F5F9' }}>
                                                <span className="d-flex align-items-center gap-1">
                                                    <i className="bi bi-calendar3" style={{ color: '#0F9D91' }}></i>
                                                    <strong>{trip.total_days}</strong> {trip.total_days === 1 ? 'day' : 'days'}
                                                </span>
                                                <span>&bull;</span>
                                                <span className="d-flex align-items-center gap-1">
                                                    <i className="bi bi-people" style={{ color: '#0F9D91' }}></i>
                                                    <strong>{trip.travelers}</strong> {trip.travelers === 1 ? 'traveler' : 'travelers'}
                                                </span>
                                                {trip.budget && (
                                                    <>
                                                        <span>&bull;</span>
                                                        <span className="d-flex align-items-center gap-1">
                                                            <i className="bi bi-wallet2" style={{ color: '#10B981' }}></i>
                                                            <strong>{formatCurrency(trip.budget)}</strong>
                                                        </span>
                                                    </>
                                                )}
                                            </div>

                                            {/* Footer with Creator and Print summary */}
                                            <div className="d-flex justify-content-between align-items-center mt-auto pt-3 border-top" style={{ borderColor: '#F1F5F9' }}>
                                                <div className="d-flex align-items-center gap-2">
                                                    <div
                                                        style={{
                                                            width: '30px',
                                                            height: '30px',
                                                            borderRadius: '50%',
                                                            backgroundColor: '#16324F',
                                                            color: '#fff',
                                                            fontWeight: 700,
                                                            fontSize: '0.74rem',
                                                            display: 'inline-flex',
                                                            alignItems: 'center',
                                                            justifyContent: 'center'
                                                        }}
                                                    >
                                                        {getInitials(trip.user?.name)}
                                                    </div>
                                                    <span className="small text-secondary fw-semibold">
                                                        {trip.user?.name?.split(' ')[0] || 'Explorer'}
                                                    </span>
                                                </div>

                                                <div className="d-flex align-items-center gap-1.5">
                                                    <button
                                                        type="button"
                                                        className="btn btn-sm btn-light border p-1 rounded-circle"
                                                        style={{ width: '32px', height: '32px' }}
                                                        title="Print Itinerary Summary"
                                                        onClick={() => window.print()}
                                                    >
                                                        <i className="bi bi-printer text-muted" style={{ fontSize: '0.85rem' }}></i>
                                                    </button>
                                                    <span className="badge rounded-pill" style={{ backgroundColor: trip.is_public ? '#E6F7F5' : '#F1F5F9', color: trip.is_public ? '#0F9D91' : '#64748B', fontSize: '0.72rem' }}>
                                                        {trip.is_public ? 'Public' : 'Private'}
                                                    </span>
                                                </div>
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

            {/* MODAL 1: Create Trip Modal */}
            {showModal && (
                <div
                    className="modal show d-block"
                    tabIndex="-1"
                    style={{ backgroundColor: 'rgba(22, 50, 79, 0.65)', backdropFilter: 'blur(6px)' }}
                >
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content border-0 rounded-4 shadow-lg p-2" style={{ backgroundColor: '#FFFFFF' }}>
                            <div className="modal-header border-0 pb-1">
                                <div>
                                    <h5 className="modal-title fw-bold" style={{ color: '#16324F' }}>
                                        <i className="bi bi-send-fill text-teal me-2" style={{ color: '#0F9D91' }}></i>
                                        Plan My New Trip
                                    </h5>
                                    <small className="text-muted">Set up your destinations, dates and budget targets.</small>
                                </div>
                                <button
                                    type="button"
                                    className="btn-close"
                                    onClick={() => setShowModal(false)}
                                    aria-label="Close"
                                ></button>
                            </div>

                            <form onSubmit={handleCreateTrip}>
                                <div className="modal-body py-2">
                                    <div className="mb-3">
                                        <label className="form-label small fw-bold" style={{ color: '#16324F' }}>Trip Name *</label>
                                        <input
                                            type="text"
                                            className={`form-control rounded-3 border ${errors.name ? 'is-invalid' : ''}`}
                                            placeholder="e.g., Kashmir Snow Paradise or Goa Beach Chill"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            required
                                            style={{ borderColor: '#CBD5E1' }}
                                        />
                                        {errors.name && <div className="invalid-feedback">{errors.name}</div>}
                                    </div>

                                    <div className="mb-3">
                                        <label className="form-label small fw-bold" style={{ color: '#16324F' }}>Primary Destination</label>
                                        <select
                                            className="form-select rounded-3 border"
                                            value={data.destination_id}
                                            onChange={(e) => setData('destination_id', e.target.value)}
                                            style={{ borderColor: '#CBD5E1' }}
                                        >
                                            <option value="">Select destination (optional)</option>
                                            {destinations.map((d) => (
                                                <option key={d.id} value={d.id}>{d.name}</option>
                                            ))}
                                        </select>
                                    </div>

                                    <div className="row g-2 mb-3">
                                        <div className="col-6">
                                            <label className="form-label small fw-bold" style={{ color: '#16324F' }}>Start Date *</label>
                                            <input
                                                type="date"
                                                className={`form-control rounded-3 border ${errors.start_date ? 'is-invalid' : ''}`}
                                                value={data.start_date}
                                                onChange={(e) => setData('start_date', e.target.value)}
                                                required
                                                style={{ borderColor: '#CBD5E1' }}
                                            />
                                            {errors.start_date && <div className="invalid-feedback">{errors.start_date}</div>}
                                        </div>

                                        <div className="col-6">
                                            <label className="form-label small fw-bold" style={{ color: '#16324F' }}>End Date *</label>
                                            <input
                                                type="date"
                                                className={`form-control rounded-3 border ${errors.end_date ? 'is-invalid' : ''}`}
                                                value={data.end_date}
                                                onChange={(e) => setData('end_date', e.target.value)}
                                                required
                                                style={{ borderColor: '#CBD5E1' }}
                                            />
                                            {errors.end_date && <div className="invalid-feedback">{errors.end_date}</div>}
                                        </div>
                                    </div>

                                    <div className="row g-2 mb-3">
                                        <div className="col-6">
                                            <label className="form-label small fw-bold" style={{ color: '#16324F' }}>Travelers *</label>
                                            <input
                                                type="number"
                                                min="1"
                                                max="50"
                                                className="form-control rounded-3 border"
                                                value={data.travelers}
                                                onChange={(e) => setData('travelers', e.target.value)}
                                                required
                                                style={{ borderColor: '#CBD5E1' }}
                                            />
                                        </div>

                                        <div className="col-6">
                                            <div className="d-flex justify-content-between align-items-center">
                                                <label className="form-label small fw-bold mb-0" style={{ color: '#16324F' }}>Budget (₹)</label>
                                                <button
                                                    type="button"
                                                    className="btn btn-link btn-sm p-0 text-decoration-none fw-semibold"
                                                    onClick={() => setShowCalculator(true)}
                                                    style={{ fontSize: '0.74rem', color: '#0F9D91' }}
                                                >
                                                    Calculate
                                                </button>
                                            </div>
                                            <input
                                                type="number"
                                                min="0"
                                                className="form-control rounded-3 border mt-1"
                                                placeholder="e.g. 25000"
                                                value={data.budget}
                                                onChange={(e) => setData('budget', e.target.value)}
                                                style={{ borderColor: '#CBD5E1' }}
                                            />
                                        </div>
                                    </div>

                                    <div className="mb-3">
                                        <label className="form-label small fw-bold" style={{ color: '#16324F' }}>Itinerary Highlights & Notes</label>
                                        <textarea
                                            className="form-control rounded-3 border"
                                            rows="2"
                                            placeholder="What key sights, experiences, or activities are you prioritizing?"
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                            style={{ borderColor: '#CBD5E1' }}
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
                                        <label className="form-check-label small" htmlFor="tripPublicCheck" style={{ color: '#64748B' }}>
                                            Share publicly with Trip Sathi community
                                        </label>
                                    </div>
                                </div>

                                <div className="modal-footer border-0 pt-0">
                                    <button
                                        type="button"
                                        className="btn btn-light rounded-pill px-3.5"
                                        onClick={() => setShowModal(false)}
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        className="btn rounded-pill px-4 fw-bold text-white shadow-sm"
                                        disabled={processing}
                                        style={{ backgroundColor: '#0F9D91', borderColor: '#0F9D91' }}
                                    >
                                        {processing ? 'Saving...' : 'Save & Build Itinerary'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {/* MODAL 2: Interactive Smart Budget Calculator */}
            {showCalculator && (
                <div
                    className="modal show d-block"
                    tabIndex="-1"
                    style={{ backgroundColor: 'rgba(22, 50, 79, 0.65)', backdropFilter: 'blur(6px)', zIndex: 1065 }}
                >
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content border-0 rounded-4 shadow-lg p-3" style={{ backgroundColor: '#FFFFFF' }}>
                            <div className="modal-header border-0 pb-1">
                                <div>
                                    <h5 className="modal-title fw-bold" style={{ color: '#16324F' }}>
                                        <i className="bi bi-calculator me-2" style={{ color: '#0F9D91' }}></i>
                                        Smart Trip Budget Calculator
                                    </h5>
                                    <small className="text-muted">Estimate transparent travel expenses across Indian destinations.</small>
                                </div>
                                <button
                                    type="button"
                                    className="btn-close"
                                    onClick={() => setShowCalculator(false)}
                                    aria-label="Close"
                                ></button>
                            </div>

                            <div className="modal-body py-2">
                                <div className="row g-2 mb-3">
                                    <div className="col-6">
                                        <label className="form-label small fw-bold">Duration (Days)</label>
                                        <input
                                            type="number"
                                            min="1"
                                            max="30"
                                            className="form-control rounded-3"
                                            value={calcDays}
                                            onChange={(e) => setCalcDays(Math.max(1, parseInt(e.target.value) || 1))}
                                        />
                                    </div>
                                    <div className="col-6">
                                        <label className="form-label small fw-bold">Travelers</label>
                                        <input
                                            type="number"
                                            min="1"
                                            max="20"
                                            className="form-control rounded-3"
                                            value={calcTravelers}
                                            onChange={(e) => setCalcTravelers(Math.max(1, parseInt(e.target.value) || 1))}
                                        />
                                    </div>
                                </div>

                                <div className="mb-3">
                                    <label className="form-label small fw-bold">Travel Style & Tier</label>
                                    <div className="btn-group w-100" role="group">
                                        {[
                                            { id: 'budget', label: 'Budget' },
                                            { id: 'moderate', label: 'Moderate' },
                                            { id: 'luxury', label: 'Luxury' },
                                        ].map((tier) => (
                                            <button
                                                key={tier.id}
                                                type="button"
                                                className={`btn btn-sm ${calcTier === tier.id ? 'btn-teal text-white' : 'btn-outline-secondary'}`}
                                                onClick={() => setCalcTier(tier.id)}
                                                style={{ backgroundColor: calcTier === tier.id ? '#0F9D91' : 'transparent' }}
                                            >
                                                {tier.label}
                                            </button>
                                        ))}
                                    </div>
                                </div>

                                {/* Breakdown Breakdown Card */}
                                <div className="p-3 rounded-3 mb-2" style={{ backgroundColor: '#F8FAFC', border: '1px solid #E2E8F0' }}>
                                    <div className="d-flex justify-content-between small text-muted mb-1">
                                        <span>Accommodations ({calcDays} nights):</span>
                                        <span className="fw-semibold text-dark">{formatCurrency(stayTotal)}</span>
                                    </div>
                                    <div className="d-flex justify-content-between small text-muted mb-1">
                                        <span>Meals & Dining:</span>
                                        <span className="fw-semibold text-dark">{formatCurrency(foodTotal)}</span>
                                    </div>
                                    <div className="d-flex justify-content-between small text-muted mb-1">
                                        <span>Local Transport & Cabs:</span>
                                        <span className="fw-semibold text-dark">{formatCurrency(transportTotal)}</span>
                                    </div>
                                    <div className="d-flex justify-content-between small text-muted mb-2">
                                        <span>Sightseeing & Entry Passes:</span>
                                        <span className="fw-semibold text-dark">{formatCurrency(activityTotal)}</span>
                                    </div>
                                    <div className="d-flex justify-content-between pt-2 border-top">
                                        <strong style={{ color: '#16324F' }}>Estimated Total:</strong>
                                        <strong className="h5 mb-0" style={{ color: '#0F9D91' }}>{formatCurrency(estimatedTotal)}</strong>
                                    </div>
                                    <small className="text-muted d-block mt-1" style={{ fontSize: '0.72rem' }}>
                                        *Excludes interstate flights or trains. Based on real 2026 destination averages.
                                    </small>
                                </div>
                            </div>

                            <div className="modal-footer border-0 pt-0">
                                <button
                                    type="button"
                                    className="btn btn-light rounded-pill px-3"
                                    onClick={() => setShowCalculator(false)}
                                >
                                    Close
                                </button>
                                <button
                                    type="button"
                                    className="btn rounded-pill px-4 fw-bold text-white shadow-sm"
                                    onClick={applyCalculatedBudget}
                                    style={{ backgroundColor: '#0F9D91', borderColor: '#0F9D91' }}
                                >
                                    Apply Budget to Trip
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* MODAL 3: Travel Checklist Modal */}
            {showChecklist && (
                <div
                    className="modal show d-block"
                    tabIndex="-1"
                    style={{ backgroundColor: 'rgba(22, 50, 79, 0.65)', backdropFilter: 'blur(6px)', zIndex: 1065 }}
                >
                    <div className="modal-dialog modal-dialog-centered modal-lg">
                        <div className="modal-content border-0 rounded-4 shadow-lg p-3" style={{ backgroundColor: '#FFFFFF' }}>
                            <div className="modal-header border-0 pb-1">
                                <div>
                                    <h5 className="modal-title fw-bold" style={{ color: '#16324F' }}>
                                        <i className="bi bi-check2-square me-2" style={{ color: '#FF795D' }}></i>
                                        Indian Travel Essentials Checklist
                                    </h5>
                                    <small className="text-muted">Ensure your travel group has all essentials covered before departure.</small>
                                </div>
                                <button
                                    type="button"
                                    className="btn-close"
                                    onClick={() => setShowChecklist(false)}
                                    aria-label="Close"
                                ></button>
                            </div>

                            <div className="modal-body py-2">
                                <div className="row g-3">
                                    {checklistItems.map((cat, i) => (
                                        <div key={i} className="col-12 col-md-4">
                                            <div className="card h-100 p-3 rounded-3 border" style={{ backgroundColor: '#F8FAFC', borderColor: '#E2E8F0' }}>
                                                <h6 className="fw-bold mb-2.5" style={{ color: '#16324F' }}>
                                                    {cat.category}
                                                </h6>
                                                <ul className="list-unstyled mb-0 d-flex flex-column gap-2" style={{ fontSize: '0.84rem' }}>
                                                    {cat.items.map((item, j) => (
                                                        <li key={j} className="d-flex align-items-start gap-2">
                                                            <i className="bi bi-check-circle-fill text-teal mt-0.5" style={{ color: '#0F9D91' }}></i>
                                                            <span>{item}</span>
                                                        </li>
                                                    ))}
                                                </ul>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <div className="modal-footer border-0 pt-2">
                                <button
                                    type="button"
                                    className="btn btn-outline-dark rounded-pill px-3.5"
                                    onClick={() => window.print()}
                                >
                                    <i className="bi bi-printer me-1.5"></i> Print Checklist
                                </button>
                                <button
                                    type="button"
                                    className="btn rounded-pill px-4 text-white fw-bold"
                                    onClick={() => setShowChecklist(false)}
                                    style={{ backgroundColor: '#16324F' }}
                                >
                                    Got It
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
