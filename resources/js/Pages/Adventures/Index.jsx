import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import PageHero from '../../Components/PageHero';
import ItemCard from '../../Components/ItemCard';
import Pagination from '../../Components/Pagination';

export default function AdventuresIndex({
    adventures,
    destinations = [],
    types = {},
    difficulties = {},
    total = 0,
    filters = {},
}) {
    const [filterState, setFilterState] = useState({
        q: filters.q || '',
        destination: filters.destination || '',
        type: filters.type || '',
        difficulty: filters.difficulty || '',
        sort: filters.sort || 'popular',
    });

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get('/adventures', filterState, { preserveState: true });
    };

    const handleReset = () => {
        setFilterState({ q: '', destination: '', type: '', difficulty: '', sort: 'popular' });
        router.get('/adventures');
    };

    return (
        <AppLayout
            title="Outdoor Adventures"
            description="Trekking, river rafting, safaris and high-adrenaline outdoor sports across India."
        >
            <PageHero
                image="/photos/banners/banner-adventures.jpg"
                eyebrow="Feel the rush"
                title="Outdoor Adventures"
                lede="Trek through Himalayan valleys, raft turbulent rapids, camp under starry skies, and explore wild national parks."
                crumbs={[{ label: 'Home', url: '/' }, { label: 'Adventures' }]}
                stats={[
                    [`${total}+`, 'Activities'],
                    ['Certified', 'Guides & Gear'],
                    ['100%', 'Safety Verified'],
                ]}
            />

            <section className="ts-section py-5">
                <div className="container">
                    <div className="row g-4">
                        {/* Filters */}
                        <div className="col-lg-3">
                            <form className="card border-0 shadow-sm p-4 rounded-4" onSubmit={handleFilterSubmit}>
                                <h3 className="h6 fw-bold mb-3 d-flex align-items-center">
                                    <i className="bi bi-funnel me-2 text-primary"></i> Filter adventures
                                </h3>

                                <div className="mb-3">
                                    <label className="form-label small fw-semibold">Search</label>
                                    <input
                                        className="form-control"
                                        placeholder="Trek, rafting, safari…"
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
                                    <label className="form-label small fw-semibold">Activity Type</label>
                                    <select
                                        className="form-select"
                                        value={filterState.type}
                                        onChange={(e) => setFilterState({ ...filterState, type: e.target.value })}
                                    >
                                        <option value="">All activity types</option>
                                        {Object.entries(types).map(([k, label]) => (
                                            <option key={k} value={k}>{label}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="mb-3">
                                    <label className="form-label small fw-semibold">Difficulty Level</label>
                                    <select
                                        className="form-select"
                                        value={filterState.difficulty}
                                        onChange={(e) => setFilterState({ ...filterState, difficulty: e.target.value })}
                                    >
                                        <option value="">Any difficulty</option>
                                        {Object.entries(difficulties).map(([k, label]) => (
                                            <option key={k} value={k}>{label}</option>
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
                                        <option value="price_asc">Price: Low to High</option>
                                        <option value="price_desc">Price: High to Low</option>
                                        <option value="duration">Shortest duration</option>
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
                                    Showing <strong>{adventures.data?.length || 0}</strong> of <strong>{adventures.total || 0}</strong> adventures
                                </p>
                            </div>

                            {adventures.data?.length === 0 ? (
                                <div className="text-center py-5 card border-0 shadow-sm rounded-4">
                                    <i className="bi bi-signpost-split fs-1 text-muted mb-2"></i>
                                    <h4 className="fw-bold">No adventure matches your search</h4>
                                    <p className="text-muted">Try choosing another activity type or destination.</p>
                                    <button className="btn btn-primary btn-sm mx-auto" onClick={handleReset}>
                                        Reset filters
                                    </button>
                                </div>
                            ) : (
                                <div className="row g-4">
                                    {adventures.data.map((adv) => (
                                        <div key={adv.id} className="col-md-6 col-lg-4">
                                            <ItemCard
                                                image={adv.main_image ? (adv.main_image.startsWith('http') || adv.main_image.startsWith('/') ? adv.main_image : `/${adv.main_image}`) : null}
                                                title={adv.name}
                                                subtitle={adv.destination?.name}
                                                text={adv.short_description}
                                                badge={adv.difficulty ? `Difficulty: ${adv.difficulty}` : null}
                                                price={adv.price}
                                                priceNote={adv.duration_minutes ? `· ${Math.round(adv.duration_minutes / 60)}h` : null}
                                                wishType="adventure"
                                                wishId={adv.id}
                                                cta="Book Activity"
                                                ctaUrl={`/adventures/${adv.slug}`}
                                            />
                                        </div>
                                    ))}
                                </div>
                            )}

                            <Pagination links={adventures.links} />
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}
