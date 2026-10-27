import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import PageHero from '../../Components/PageHero';
import ItemCard from '../../Components/ItemCard';
import Pagination from '../../Components/Pagination';

export default function EventsIndex({ events, destinations = [], total = 0, filters = {} }) {
    const [filterState, setFilterState] = useState({
        q: filters.q || '',
        destination: filters.destination || '',
        when: filters.when || 'upcoming',
        sort: filters.sort || 'soonest',
    });

    const handleFilterSubmit = (e) => {
        e.preventDefault();
        router.get('/events', filterState, { preserveState: true });
    };

    const handleReset = () => {
        setFilterState({ q: '', destination: '', when: 'upcoming', sort: 'soonest' });
        router.get('/events');
    };

    return (
        <AppLayout
            title="Events & Festivals"
            description="Discover upcoming concerts, cultural festivals and exhibitions across India."
        >
            <PageHero
                image="/photos/banners/banner-events.jpg"
                eyebrow="Celebrate Culture"
                title="Events & Festivals"
                lede="Experience regional festivals, live concerts, spiritual gatherings and cultural showcases."
                crumbs={[{ label: 'Home', url: '/' }, { label: 'Events' }]}
                stats={[
                    [`${total}+`, 'Events'],
                    ['Instant', 'E-Ticket Pass'],
                    ['Verified', 'Organisers'],
                ]}
            />

            <section className="ts-section py-5">
                <div className="container">
                    <div className="row g-4">
                        {/* Filters */}
                        <div className="col-lg-3">
                            <form className="card border-0 shadow-sm p-4 rounded-4" onSubmit={handleFilterSubmit}>
                                <h3 className="h6 fw-bold mb-3 d-flex align-items-center">
                                    <i className="bi bi-funnel me-2 text-primary"></i> Filter events
                                </h3>

                                <div className="mb-3">
                                    <label className="form-label small fw-semibold">Search</label>
                                    <input
                                        className="form-control"
                                        placeholder="Event or artist name…"
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
                                    <label className="form-label small fw-semibold">Timeline</label>
                                    <select
                                        className="form-select"
                                        value={filterState.when}
                                        onChange={(e) => setFilterState({ ...filterState, when: e.target.value })}
                                    >
                                        <option value="upcoming">Upcoming events</option>
                                        <option value="all">All dates</option>
                                        <option value="past">Past events</option>
                                    </select>
                                </div>

                                <div className="mb-4">
                                    <label className="form-label small fw-semibold">Sort by</label>
                                    <select
                                        className="form-select"
                                        value={filterState.sort}
                                        onChange={(e) => setFilterState({ ...filterState, sort: e.target.value })}
                                    >
                                        <option value="soonest">Happening soonest</option>
                                        <option value="latest">Latest date</option>
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
                                    Showing <strong>{events.data?.length || 0}</strong> of <strong>{events.total || 0}</strong> events
                                </p>
                            </div>

                            {events.data?.length === 0 ? (
                                <div className="text-center py-5 card border-0 shadow-sm rounded-4">
                                    <i className="bi bi-calendar-event fs-1 text-muted mb-2"></i>
                                    <h4 className="fw-bold">No events match those filters</h4>
                                    <p className="text-muted">Check back soon for newly announced concerts and festivals.</p>
                                    <button className="btn btn-primary btn-sm mx-auto" onClick={handleReset}>
                                        Reset filters
                                    </button>
                                </div>
                            ) : (
                                <div className="row g-4">
                                    {events.data.map((ev) => (
                                        <div key={ev.id} className="col-md-6 col-lg-4">
                                            <ItemCard
                                                image={ev.main_image ? (ev.main_image.startsWith('http') || ev.main_image.startsWith('/') ? ev.main_image : `/${ev.main_image}`) : null}
                                                title={ev.name}
                                                subtitle={ev.venue || ev.destination?.name}
                                                text={ev.short_description}
                                                price={ev.price || ev.base_price}
                                                wishType="event"
                                                wishId={ev.id}
                                                cta="Get Tickets"
                                                ctaUrl={`/events/${ev.slug}`}
                                            />
                                        </div>
                                    ))}
                                </div>
                            )}

                            <Pagination links={events.links} />
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}
