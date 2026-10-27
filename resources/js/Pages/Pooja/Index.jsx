import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import PageHero from '../../Components/PageHero';
import ItemCard from '../../Components/ItemCard';
import Pagination from '../../Components/Pagination';

export default function PoojaIndex({ poojas, destinations = [], total = 0, filters = {} }) {
    const [filterState, setFilterState] = useState({
        q: filters.q || '',
        destination: filters.destination || '',
        sort: filters.sort || 'popular',
    });

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get('/pooja', filterState, { preserveState: true });
    };

    const handleReset = () => {
        setFilterState({ q: '', destination: '', sort: 'popular' });
        router.get('/pooja');
    };

    return (
        <AppLayout
            title="Pooja Vidhi & Rituals"
            description="Book Vedic Pooja ceremonies, Rudrabhishek, and sacred temple rituals with certified priests."
        >
            <PageHero
                image="/photos/banners/banner-pooja.jpg"
                eyebrow="Vedic Blessings"
                title="Pooja Vidhi & Sacred Rituals"
                lede="Perform sacred rituals and Vedic ceremonies guided by revered priests at holy pilgrimage sites."
                crumbs={[{ label: 'Home', url: '/' }, { label: 'Pooja Vidhi' }]}
                stats={[
                    [`${total}+`, 'Ritual ceremonies'],
                    ['Samagri', 'Included'],
                    ['Certified', 'Vedic Priests'],
                ]}
            />

            <section className="ts-section py-5">
                <div className="container">
                    <div className="row g-4">
                        {/* Filters */}
                        <div className="col-lg-3">
                            <form className="card border-0 shadow-sm p-4 rounded-4" onSubmit={handleFilterSubmit}>
                                <h3 className="h6 fw-bold mb-3 d-flex align-items-center">
                                    <i className="bi bi-funnel me-2 text-primary"></i> Filter rituals
                                </h3>

                                <div className="mb-3">
                                    <label className="form-label small fw-semibold">Search</label>
                                    <input
                                        className="form-control"
                                        placeholder="Pooja name or ritual…"
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
                                        <option value="">All holy places</option>
                                        {destinations.map((d) => (
                                            <option key={d.id} value={d.slug}>{d.name}</option>
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
                                    Showing <strong>{poojas.data?.length || 0}</strong> of <strong>{poojas.total || 0}</strong> rituals
                                </p>
                            </div>

                            {poojas.data?.length === 0 ? (
                                <div className="text-center py-5 card border-0 shadow-sm rounded-4">
                                    <i className="bi bi-flower1 fs-1 text-muted mb-2"></i>
                                    <h4 className="fw-bold">No pooja vidhi matches your search</h4>
                                    <p className="text-muted">Try selecting another holy destination or resetting filters.</p>
                                    <button className="btn btn-primary btn-sm mx-auto" onClick={handleReset}>
                                        Reset filters
                                    </button>
                                </div>
                            ) : (
                                <div className="row g-4">
                                    {poojas.data.map((pj) => (
                                        <div key={pj.id} className="col-md-6 col-lg-4">
                                            <ItemCard
                                                image={pj.main_image ? (pj.main_image.startsWith('http') || pj.main_image.startsWith('/') ? pj.main_image : `/${pj.main_image}`) : null}
                                                title={pj.name}
                                                subtitle={pj.destination?.name}
                                                text={pj.short_description}
                                                badge="Vedic Ritual"
                                                price={pj.price}
                                                priceNote={pj.duration_minutes ? `· ${pj.duration_minutes}m` : null}
                                                wishType="pooja"
                                                wishId={pj.id}
                                                cta="Book Pooja"
                                                ctaUrl={`/pooja/${pj.slug}`}
                                            />
                                        </div>
                                    ))}
                                </div>
                            )}

                            <Pagination links={poojas.links} />
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}
