import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import PageHero from '../../Components/PageHero';
import ItemCard from '../../Components/ItemCard';
import Pagination from '../../Components/Pagination';

export default function DarshanIndex({ darshans, destinations = [], types = {}, total = 0, filters = {} }) {
    const [filterState, setFilterState] = useState({
        q: filters.q || '',
        destination: filters.destination || '',
        type: filters.type || '',
        sort: filters.sort || 'popular',
    });

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get('/darshan', filterState, { preserveState: true });
    };

    const handleReset = () => {
        setFilterState({ q: '', destination: '', type: '', sort: 'popular' });
        router.get('/darshan');
    };

    return (
        <AppLayout
            title="VIP Darshan"
            description="Book verified VIP Darshan passes for Dwarka, Somnath and major shrines across India."
        >
            <PageHero
                image="/photos/banners/banner-darshan.jpg"
                eyebrow="Sacred Temple Journey"
                title="VIP Darshan Bookings"
                lede="Skip long queue lines at major shrines with verified VIP darshan entry passes and respectful queue guidance."
                crumbs={[{ label: 'Home', url: '/' }, { label: 'VIP Darshan' }]}
                stats={[
                    [`${total}+`, 'Shrine passes'],
                    ['Dedicated', 'Entry Gate'],
                    ['Temple', 'Certified'],
                ]}
            />

            <section className="ts-section py-5">
                <div className="container">
                    <div className="row g-4">
                        {/* Filters */}
                        <div className="col-lg-3">
                            <form className="card border-0 shadow-sm p-4 rounded-4" onSubmit={handleFilterSubmit}>
                                <h3 className="h6 fw-bold mb-3 d-flex align-items-center">
                                    <i className="bi bi-funnel me-2 text-primary"></i> Filter darshan
                                </h3>

                                <div className="mb-3">
                                    <label className="form-label small fw-semibold">Search</label>
                                    <input
                                        className="form-control"
                                        placeholder="Temple or deity name…"
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
                                        <option value="">All holy cities</option>
                                        {destinations.map((d) => (
                                            <option key={d.id} value={d.slug}>{d.name}</option>
                                        ))}
                                    </select>
                                </div>

                                <div className="mb-3">
                                    <label className="form-label small fw-semibold">Darshan Category</label>
                                    <select
                                        className="form-select"
                                        value={filterState.type}
                                        onChange={(e) => setFilterState({ ...filterState, type: e.target.value })}
                                    >
                                        <option value="">All types</option>
                                        {Object.entries(types).map(([k, label]) => (
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
                                    Showing <strong>{darshans.data?.length || 0}</strong> of <strong>{darshans.total || 0}</strong> shrines
                                </p>
                            </div>

                            {darshans.data?.length === 0 ? (
                                <div className="text-center py-5 card border-0 shadow-sm rounded-4">
                                    <i className="bi bi-gem fs-1 text-muted mb-2"></i>
                                    <h4 className="fw-bold">No darshan slot matches those filters</h4>
                                    <p className="text-muted">Try selecting another holy city or clearing filters.</p>
                                    <button className="btn btn-primary btn-sm mx-auto" onClick={handleReset}>
                                        Reset filters
                                    </button>
                                </div>
                            ) : (
                                <div className="row g-4">
                                    {darshans.data.map((ds) => (
                                        <div key={ds.id} className="col-md-6 col-lg-4">
                                            <ItemCard
                                                image={ds.main_image ? (ds.main_image.startsWith('http') || ds.main_image.startsWith('/') ? ds.main_image : `/${ds.main_image}`) : null}
                                                title={ds.name}
                                                subtitle={ds.destination?.name}
                                                text={ds.temple_name}
                                                badge="VIP Pass"
                                                price={ds.price}
                                                priceNote="/ person"
                                                wishType="darshan"
                                                wishId={ds.id}
                                                cta="Book Pass"
                                                ctaUrl={`/darshan/${ds.slug}`}
                                            />
                                        </div>
                                    ))}
                                </div>
                            )}

                            <Pagination links={darshans.links} />
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}
