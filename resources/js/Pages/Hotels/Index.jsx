import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import PageHero from '../../Components/PageHero';
import ItemCard from '../../Components/ItemCard';
import Pagination from '../../Components/Pagination';

export default function HotelsIndex({ hotels, destinations = [], total = 0, filters = {} }) {
    const [filterState, setFilterState] = useState({
        q: filters.q || '',
        destination: filters.destination || '',
        stars: filters.stars || '',
        min_price: filters.min_price || '',
        max_price: filters.max_price || '',
        sort: filters.sort || 'recommended',
    });

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get('/hotels', filterState, { preserveState: true });
    };

    const handleReset = () => {
        setFilterState({
            q: '',
            destination: '',
            stars: '',
            min_price: '',
            max_price: '',
            sort: 'recommended',
        });
        router.get('/hotels');
    };

    return (
        <AppLayout
            title="Hotels & Stays"
            description="Book hotels, boutique stays, and heritage havelis across India with Trip Sathi."
        >
            <PageHero
                image="/photos/banners/banner-hotels.jpg"
                eyebrow="Rest in comfort"
                title="Hotels & Stays"
                lede="Carefully curated stays with real guest reviews, transparent rates, and guaranteed room confirmation."
                crumbs={[{ label: 'Home', url: '/' }, { label: 'Hotels' }]}
                stats={[
                    [`${total}+`, 'Verified properties'],
                    ['4.7', 'Average star rating'],
                    ['Best Rate', 'Guarantee'],
                ]}
            />

            <section className="ts-section py-5">
                <div className="container">
                    <div className="row g-4">
                        {/* Filters */}
                        <div className="col-lg-3">
                            <form className="card border-0 shadow-sm p-4 rounded-4" onSubmit={handleFilterSubmit}>
                                <h3 className="h6 fw-bold mb-3 d-flex align-items-center">
                                    <i className="bi bi-funnel me-2 text-primary"></i> Filter stays
                                </h3>

                                <div className="mb-3">
                                    <label className="form-label small fw-semibold">Search</label>
                                    <input
                                        className="form-control"
                                        placeholder="Hotel name or city…"
                                        value={filterState.q}
                                        onChange={(e) => setFilterState({ ...filterState, q: e.target.value })}
                                    />
                                </div>

                                <div className="mb-3">
                                    <label className="form-label small fw-semibold">Destination</label>
                                    <select
                                        className="form-select"
                                        value={filterState.destination}
                                        onChange={(e) => setFilterState({ ...filterState, destination: e.target.value })}
                                    >
                                        <option value="">All destinations</option>
                                        {destinations.map((d) => (
                                            <option key={d.id} value={d.slug}>{d.name}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="mb-3">
                                    <label className="form-label small fw-semibold">Minimum Star Rating</label>
                                    <select
                                        className="form-select"
                                        value={filterState.stars}
                                        onChange={(e) => setFilterState({ ...filterState, stars: e.target.value })}
                                    >
                                        <option value="">Any rating</option>
                                        <option value="5">5 Stars only</option>
                                        <option value="4">4+ Stars</option>
                                        <option value="3">3+ Stars</option>
                                    </select>
                                </div>

                                <div className="row g-2 mb-3">
                                    <div className="col-6">
                                        <label className="form-label small fw-semibold">Min Price</label>
                                        <input
                                            type="number"
                                            className="form-control"
                                            placeholder="₹"
                                            value={filterState.min_price}
                                            onChange={(e) => setFilterState({ ...filterState, min_price: e.target.value })}
                                        />
                                    </div>
                                    <div className="col-6">
                                        <label className="form-label small fw-semibold">Max Price</label>
                                        <input
                                            type="number"
                                            className="form-control"
                                            placeholder="₹"
                                            value={filterState.max_price}
                                            onChange={(e) => setFilterState({ ...filterState, max_price: e.target.value })}
                                        />
                                    </div>
                                </div>

                                <div className="mb-4">
                                    <label className="form-label small fw-semibold">Sort by</label>
                                    <select
                                        className="form-select"
                                        value={filterState.sort}
                                        onChange={(e) => setFilterState({ ...filterState, sort: e.target.value })}
                                    >
                                        <option value="recommended">Recommended</option>
                                        <option value="price_asc">Price: Low to High</option>
                                        <option value="price_desc">Price: High to Low</option>
                                        <option value="rating">Top Guest Rating</option>
                                        <option value="stars">Star Rating</option>
                                    </select>
                                </div>

                                <div className="d-flex gap-2">
                                    <button className="btn btn-primary btn-sm flex-grow-1" type="submit">
                                        Apply
                                    </button>
                                    <button className="btn btn-outline-secondary btn-sm" type="button" onClick={handleReset}>
                                        Reset
                                    </button>
                                </div>
                            </form>
                        </div>

                        {/* List */}
                        <div className="col-lg-9">
                            <div className="d-flex justify-content-between align-items-center mb-3">
                                <p className="text-muted small mb-0">
                                    Showing <strong>{hotels.data?.length || 0}</strong> of <strong>{hotels.total || 0}</strong> hotels
                                </p>
                            </div>

                            {hotels.data?.length === 0 ? (
                                <div className="text-center py-5 card border-0 shadow-sm rounded-4">
                                    <i className="bi bi-building fs-1 text-muted mb-2"></i>
                                    <h4 className="fw-bold">No hotels match your filters</h4>
                                    <p className="text-muted">Try widening your price range or selecting another destination.</p>
                                    <button className="btn btn-primary btn-sm mx-auto" onClick={handleReset}>
                                        Reset filters
                                    </button>
                                </div>
                            ) : (
                                <div className="row g-4">
                                    {hotels.data.map((h) => (
                                        <div key={h.id} className="col-md-6 col-lg-4">
                                            <ItemCard
                                                image={h.main_image ? (h.main_image.startsWith('http') || h.main_image.startsWith('/') ? h.main_image : `/${h.main_image}`) : null}
                                                title={h.name}
                                                subtitle={h.destination?.name}
                                                stars={h.star_rating}
                                                price={h.effective_price || h.price_per_night || h.base_price}
                                                priceNote="/ night"
                                                rating={h.rating}
                                                ratingNote={`${h.review_count || 0} reviews`}
                                                badge={h.is_featured ? 'Featured' : null}
                                                wishType="hotel"
                                                wishId={h.id}
                                                cta="View Rooms"
                                                ctaUrl={`/hotels/${h.slug}`}
                                            />
                                        </div>
                                    ))}
                                </div>
                            )}

                            <Pagination links={hotels.links} />
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}
