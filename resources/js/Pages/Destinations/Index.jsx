import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import PageHero from '../../Components/PageHero';
import ItemCard from '../../Components/ItemCard';
import Pagination from '../../Components/Pagination';

export default function DestinationsIndex({ destinations, states = [], total = 0, filters = {} }) {
    const [filterState, setFilterState] = useState({
        q: filters.q || '',
        state: filters.state || '',
        sort: filters.sort || 'popular',
    });

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get('/destinations', filterState, { preserveState: true });
    };

    const handleReset = () => {
        setFilterState({ q: '', state: '', sort: 'popular' });
        router.get('/destinations');
    };

    return (
        <AppLayout
            title="Destinations"
            description="Explore handpicked destinations across India with stays, activities and spiritual experiences."
        >
            <PageHero
                image="/photos/banners/banner-destinations.jpg"
                eyebrow="Explore India"
                title="Destinations"
                lede="From temple towns on the Gujarat coast to lake cities in Rajasthan — handpicked places, honest notes on when to go, and everything you can book once you land."
                crumbs={[{ label: 'Home', url: '/' }, { label: 'Destinations' }]}
                stats={[
                    [`${total}+`, 'Places to explore'],
                    ['4.8', 'Average traveller rating'],
                    ['24×7', 'Planning support'],
                ]}
            />

            <section className="ts-section py-5">
                <div className="container">
                    <div className="row g-4">
                        {/* Sidebar Filter */}
                        <div className="col-lg-3">
                            <form className="card border-0 shadow-sm p-4 rounded-4" onSubmit={handleFilterSubmit}>
                                <h3 className="h6 fw-bold mb-3 d-flex align-items-center">
                                    <i className="bi bi-funnel me-2 text-primary"></i> Filter destinations
                                </h3>

                                <div className="mb-3">
                                    <label className="form-label small fw-semibold">Search</label>
                                    <input
                                        className="form-control"
                                        placeholder="City, state or place…"
                                        value={filterState.q}
                                        onChange={(e) => setFilterState({ ...filterState, q: e.target.value })}
                                    />
                                </div>

                                <div className="mb-3">
                                    <label className="form-label small fw-semibold">State</label>
                                    <select
                                        className="form-select"
                                        value={filterState.state}
                                        onChange={(e) => setFilterState({ ...filterState, state: e.target.value })}
                                    >
                                        <option value="">All states</option>
                                        {states.map((st) => (
                                            <option key={st} value={st}>{st}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="mb-4">
                                    <label className="form-label small fw-semibold">Sort by</label>
                                    <select
                                        className="form-select"
                                        value={filterState.sort}
                                        onChange={(e) => setFilterState({ ...filterState, sort: e.target.value })}
                                    >
                                        <option value="popular">Most popular</option>
                                        <option value="name">Name (A–Z)</option>
                                        <option value="newest">Recently added</option>
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

                        {/* Main Grid */}
                        <div className="col-lg-9">
                            <div className="d-flex justify-content-between align-items-center mb-3">
                                <p className="text-muted small mb-0">
                                    Showing <strong>{destinations.data?.length || 0}</strong> of <strong>{destinations.total || 0}</strong> destinations
                                </p>
                            </div>

                            {destinations.data?.length === 0 ? (
                                <div className="text-center py-5 card border-0 shadow-sm rounded-4">
                                    <i className="bi bi-geo-alt fs-1 text-muted mb-2"></i>
                                    <h4 className="fw-bold">No destinations match those filters</h4>
                                    <p className="text-muted">Try clearing the search or picking another state.</p>
                                    <button className="btn btn-primary btn-sm mx-auto" onClick={handleReset}>
                                        Reset filters
                                    </button>
                                </div>
                            ) : (
                                <div className="row g-4">
                                    {destinations.data.map((d) => (
                                        <div key={d.id} className="col-md-6 col-lg-4">
                                            <ItemCard
                                                image={d.main_image ? (d.main_image.startsWith('http') || d.main_image.startsWith('/') ? d.main_image : `/${d.main_image}`) : null}
                                                title={d.name}
                                                subtitle={`${d.city ? `${d.city}, ` : ''}${d.state}`}
                                                text={d.short_description}
                                                badge={d.is_featured ? 'Featured' : null}
                                                wishType="destination"
                                                wishId={d.id}
                                                cta="View details"
                                                ctaUrl={`/destinations/${d.slug}`}
                                            />
                                        </div>
                                    ))}
                                </div>
                            )}

                            <Pagination links={destinations.links} />
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}
