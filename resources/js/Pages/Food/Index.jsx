import React, { useState } from 'react';
import { router, Link } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import PageHero from '../../Components/PageHero';
import ItemCard from '../../Components/ItemCard';
import Pagination from '../../Components/Pagination';

export default function FoodIndex({
    restaurants,
    experiences = [],
    destinations = {},
    cuisines = [],
    total = 0,
    filters = {},
}) {
    const [filterState, setFilterState] = useState({
        q: filters.q || '',
        destination: filters.destination || '',
        cuisine: filters.cuisine || '',
        sort: filters.sort || 'popular',
    });

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get('/food', filterState, { preserveState: true });
    };

    const handleReset = () => {
        setFilterState({ q: '', destination: '', cuisine: '', sort: 'popular' });
        router.get('/food');
    };

    return (
        <AppLayout
            title="Food & Dining"
            description="Discover regional cuisines, authentic thalis and food walks across India."
        >
            <PageHero
                image="/photos/banners/banner-food.jpg"
                eyebrow="Flavours of India"
                title="Food & Dining Experiences"
                lede="From authentic Gujarati kathiyawadi thalis to royal Rajasthani banquets and historic street food walks."
                crumbs={[{ label: 'Home', url: '/' }, { label: 'Food & Dining' }]}
                stats={[
                    [`${total}+`, 'Restaurants'],
                    ['Curated', 'Food walks'],
                    ['Top Rated', 'Local Flavours'],
                ]}
            />

            {/* Curated Food Experiences Strip */}
            {experiences.length > 0 && (
                <section className="ts-section py-5 bg-light">
                    <div className="container">
                        <div className="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <span className="ts-eyebrow text-danger">Curated Tasting</span>
                                <h2 className="ts-title h3 mb-0">Special Food Experiences</h2>
                            </div>
                        </div>

                        <div className="row g-4">
                            {experiences.map((exp) => (
                                <div key={exp.id} className="col-md-6 col-lg-3">
                                    <ItemCard
                                        image={exp.main_image ? (exp.main_image.startsWith('http') || exp.main_image.startsWith('/') ? exp.main_image : `/${exp.main_image}`) : null}
                                        title={exp.name}
                                        subtitle={exp.destination?.name}
                                        text={exp.short_description}
                                        price={exp.price}
                                        priceNote="/ person"
                                        badge="Experience"
                                        wishType="food"
                                        wishId={exp.id}
                                        cta="Book Experience"
                                        ctaUrl={`/food/${exp.slug}`}
                                    />
                                </div>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            <section className="ts-section py-5">
                <div className="container">
                    <div className="row g-4">
                        {/* Filters */}
                        <div className="col-lg-3">
                            <form className="card border-0 shadow-sm p-4 rounded-4" onSubmit={handleFilterSubmit}>
                                <h3 className="h6 fw-bold mb-3 d-flex align-items-center">
                                    <i className="bi bi-funnel me-2 text-primary"></i> Filter dining
                                </h3>

                                <div className="mb-3">
                                    <label className="form-label small fw-semibold">Search</label>
                                    <input
                                        className="form-control"
                                        placeholder="Restaurant name…"
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
                                        <option value="">All cities</option>
                                        {Object.entries(destinations).map(([slug, name]) => (
                                            <option key={slug} value={slug}>{name}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="mb-3">
                                    <label className="form-label small fw-semibold">Cuisine</label>
                                    <select
                                        className="form-select"
                                        value={filterState.cuisine}
                                        onChange={(e) => setFilterState({ ...filterState, cuisine: e.target.value })}
                                    >
                                        <option value="">All cuisines</option>
                                        {cuisines.map((c) => (
                                            <option key={c} value={c}>{c}</option>
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
                                        <option value="name">Name (A-Z)</option>
                                        <option value="cost_low">Cost: Low to High</option>
                                        <option value="cost_high">Cost: High to Low</option>
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
                                    Showing <strong>{restaurants.data?.length || 0}</strong> of <strong>{restaurants.total || 0}</strong> dining spots
                                </p>
                            </div>

                            {restaurants.data?.length === 0 ? (
                                <div className="text-center py-5 card border-0 shadow-sm rounded-4">
                                    <i className="bi bi-cup-hot fs-1 text-muted mb-2"></i>
                                    <h4 className="fw-bold">No restaurants match your search</h4>
                                    <p className="text-muted">Try clearing the cuisine filter or picking another city.</p>
                                    <button className="btn btn-primary btn-sm mx-auto" onClick={handleReset}>
                                        Reset filters
                                    </button>
                                </div>
                            ) : (
                                <div className="row g-4">
                                    {restaurants.data.map((res) => (
                                        <div key={res.id} className="col-md-6 col-lg-4">
                                            <ItemCard
                                                image={res.main_image ? (res.main_image.startsWith('http') || res.main_image.startsWith('/') ? res.main_image : `/${res.main_image}`) : null}
                                                title={res.name}
                                                subtitle={res.destination?.name}
                                                meta={{ 'bi-tag': res.cuisine || 'Multi-cuisine' }}
                                                text={res.short_description}
                                                price={res.average_cost}
                                                priceNote="for two"
                                                rating={res.rating}
                                                wishType="restaurant"
                                                wishId={res.id}
                                                cta="View Details"
                                                ctaUrl={`/food/${res.slug}`}
                                            />
                                        </div>
                                    ))}
                                </div>
                            )}

                            <Pagination links={restaurants.links} />
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}
