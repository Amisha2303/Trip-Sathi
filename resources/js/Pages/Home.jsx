import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import AppLayout from '../Layouts/AppLayout';
import ItemCard from '../Components/ItemCard';

export default function Home({
    destinations = [],
    hotels = [],
    adventures = [],
    events = [],
    darshans = [],
    poojas = [],
    restaurants = [],
    experiences = [],
    trips = [],
    testimonials = [],
    counts = {},
    destinationOptions = [],
}) {
    const { appSettings } = usePage().props;

    const [searchState, setSearchState] = useState({
        destination: '',
        date: '',
        travelers: '2',
        category: '/destinations',
    });

    const [newsletterEmail, setNewsletterEmail] = useState('');
    const [subscribing, setSubscribing] = useState(false);

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        const targetRoute = searchState.category || '/destinations';
        const params = new URLSearchParams();
        if (searchState.destination) params.append('destination', searchState.destination);
        if (searchState.date) params.append('date', searchState.date);
        if (searchState.travelers) params.append('travelers', searchState.travelers);

        const url = params.toString() ? `${targetRoute}?${params.toString()}` : targetRoute;
        router.visit(url);
    };

    const handleNewsletterSubmit = (e) => {
        e.preventDefault();
        if (!newsletterEmail) return;
        setSubscribing(true);
        router.post(
            '/newsletter',
            { email: newsletterEmail },
            {
                preserveScroll: true,
                onSuccess: () => setNewsletterEmail(''),
                onFinish: () => setSubscribing(false),
            }
        );
    };

    return (
        <AppLayout
            title="Plan Your Journey. Create Beautiful Memories."
            description="Discover destinations, stays, events, adventures, and spiritual darshan & pooja experiences across India."
        >
            {/* Hero Section */}
            <section
                className="ts-hero ts-hero-photo"
                style={{ '--ts-hero-img': `url('/photos/banners/banner-home.jpg')` }}
            >
                <div className="container">
                    <span className="ts-eyebrow">Plan · Book · Remember</span>
                    <h1 className="ts-hero-title">Plan Your Journey. Create Beautiful Memories.</h1>
                    <p className="ts-hero-lede">
                        Discover destinations, stays, events, adventures and spiritual experiences across India — then keep the whole itinerary in one place.
                    </p>

                    <div className="ts-hero-stats">
                        <div>
                            <strong>{counts.destinations || 0}+</strong>
                            <span>Destinations</span>
                        </div>
                        <div>
                            <strong>{counts.hotels || 0}+</strong>
                            <span>Stays</span>
                        </div>
                        <div>
                            <strong>{counts.events || 0}+</strong>
                            <span>Events</span>
                        </div>
                        <div>
                            <strong>{counts.darshan || 0}+</strong>
                            <span>Darshan slots</span>
                        </div>
                    </div>
                </div>
            </section>

            {/* Main Search Bar */}
            <div className="container">
                <form className="ts-search ts-home-search shadow-sm" onSubmit={handleSearchSubmit}>
                    <div className="row g-2 align-items-center">
                        <div className="col-md-3 ts-search-field">
                            <label htmlFor="search-destination">Destination</label>
                            <select
                                className="form-select border-0 shadow-none"
                                id="search-destination"
                                value={searchState.destination}
                                onChange={(e) => setSearchState({ ...searchState, destination: e.target.value })}
                            >
                                <option value="">Anywhere in India</option>
                                {destinationOptions.map((d) => (
                                    <option key={d.id} value={d.slug}>
                                        {d.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="col-md-2 ts-search-field">
                            <label htmlFor="search-date">Travel date</label>
                            <input
                                className="form-control border-0 shadow-none"
                                type="date"
                                id="search-date"
                                value={searchState.date}
                                onChange={(e) => setSearchState({ ...searchState, date: e.target.value })}
                            />
                        </div>

                        <div className="col-md-2 ts-search-field">
                            <label htmlFor="search-travelers">Travelers</label>
                            <select
                                className="form-select border-0 shadow-none"
                                id="search-travelers"
                                value={searchState.travelers}
                                onChange={(e) => setSearchState({ ...searchState, travelers: e.target.value })}
                            >
                                <option value="1">1 traveler</option>
                                <option value="2">2 travelers</option>
                                <option value="3">3 travelers</option>
                                <option value="4">4+ travelers</option>
                            </select>
                        </div>

                        <div className="col-md-3 ts-search-field">
                            <label htmlFor="search-category">Category</label>
                            <select
                                className="form-select border-0 shadow-none"
                                id="search-category"
                                value={searchState.category}
                                onChange={(e) => setSearchState({ ...searchState, category: e.target.value })}
                            >
                                <option value="/destinations">Destinations</option>
                                <option value="/hotels">Hotels</option>
                                <option value="/events">Events</option>
                                <option value="/adventures">Adventures</option>
                                <option value="/darshan">VIP Darshan</option>
                                <option value="/pooja">Pooja Vidhi</option>
                                <option value="/trips">Trip ideas</option>
                            </select>
                        </div>

                        <div className="col-md-2">
                            <button className="btn btn-accent w-100 ts-search-submit py-2" type="submit">
                                Explore Now
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {/* Popular Destinations */}
            <section className="ts-section py-5">
                <div className="container">
                    <div className="ts-section-head d-flex justify-content-between align-items-end mb-4">
                        <div>
                            <span className="ts-eyebrow">Where travellers go</span>
                            <h2 className="ts-title">Popular destinations</h2>
                            <p className="ts-subtitle text-muted">
                                Top-rated places for your next trip, with stays and experiences attached.
                            </p>
                        </div>
                        <Link className="btn btn-soft" href="/destinations">
                            View all <i className="bi bi-arrow-right ms-1"></i>
                        </Link>
                    </div>

                    <div className="row g-4">
                        {destinations.map((d) => (
                            <div key={d.id} className="col-md-6 col-lg-4">
                                <ItemCard
                                    image={d.main_image ? (d.main_image.startsWith('http') || d.main_image.startsWith('/') ? d.main_image : `/${d.main_image}`) : null}
                                    title={d.name}
                                    subtitle={`${d.city ? `${d.city}, ` : ''}${d.state}`}
                                    text={d.short_description}
                                    badge={d.is_featured ? 'Popular' : null}
                                    wishType="destination"
                                    wishId={d.id}
                                    cta="Explore Guide"
                                    ctaUrl={`/destinations/${d.slug}`}
                                />
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Featured Stays */}
            {hotels.length > 0 && (
                <section className="ts-section py-5 bg-light">
                    <div className="container">
                        <div className="ts-section-head d-flex justify-content-between align-items-end mb-4">
                            <div>
                                <span className="ts-eyebrow">Rest in comfort</span>
                                <h2 className="ts-title">Featured Stays</h2>
                                <p className="ts-subtitle text-muted">Curated hotels, heritage havelis, and serene resorts.</p>
                            </div>
                            <Link className="btn btn-soft" href="/hotels">
                                View stays <i className="bi bi-arrow-right ms-1"></i>
                            </Link>
                        </div>

                        <div className="row g-4">
                            {hotels.map((h) => (
                                <div key={h.id} className="col-md-6 col-lg-3">
                                    <ItemCard
                                        image={h.main_image ? (h.main_image.startsWith('http') || h.main_image.startsWith('/') ? h.main_image : `/${h.main_image}`) : null}
                                        title={h.name}
                                        subtitle={h.destination?.name}
                                        stars={h.star_rating}
                                        price={h.effective_price || h.price_per_night}
                                        priceNote="/ night"
                                        rating={h.rating}
                                        wishType="hotel"
                                        wishId={h.id}
                                        cta="Book Room"
                                        ctaUrl={`/hotels/${h.slug}`}
                                    />
                                </div>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {/* VIP Darshan & Spiritual Experiences */}
            {darshans.length > 0 && (
                <section className="ts-section py-5">
                    <div className="container">
                        <div className="ts-section-head d-flex justify-content-between align-items-end mb-4">
                            <div>
                                <span className="ts-eyebrow text-warning">Devotional Journey</span>
                                <h2 className="ts-title">VIP Darshan & Pooja Vidhi</h2>
                                <p className="ts-subtitle text-muted">Skip queue waiting times with verified shrine entry and rituals.</p>
                            </div>
                            <Link className="btn btn-soft" href="/darshan">
                                View all darshans <i className="bi bi-arrow-right ms-1"></i>
                            </Link>
                        </div>

                        <div className="row g-4">
                            {darshans.map((ds) => (
                                <div key={ds.id} className="col-md-6 col-lg-3">
                                    <ItemCard
                                        image={ds.main_image ? (ds.main_image.startsWith('http') || ds.main_image.startsWith('/') ? ds.main_image : `/${ds.main_image}`) : null}
                                        title={ds.name}
                                        subtitle={ds.destination?.name}
                                        text={ds.temple_name}
                                        price={ds.price}
                                        priceNote="/ person"
                                        badge="VIP Pass"
                                        wishType="darshan"
                                        wishId={ds.id}
                                        cta="Reserve Slot"
                                        ctaUrl={`/darshan/${ds.slug}`}
                                    />
                                </div>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {/* Upcoming Events & Adventures */}
            {(events.length > 0 || adventures.length > 0) && (
                <section className="ts-section py-5 bg-light">
                    <div className="container">
                        <div className="row g-5">
                            {events.length > 0 && (
                                <div className="col-lg-6">
                                    <div className="d-flex justify-content-between align-items-center mb-3">
                                        <h3 className="h4 mb-0 fw-bold">Upcoming Events</h3>
                                        <Link href="/events" className="btn btn-sm btn-soft">All Events</Link>
                                    </div>
                                    <div className="row g-3">
                                        {events.slice(0, 2).map((ev) => (
                                            <div key={ev.id} className="col-12">
                                                <ItemCard
                                                    image={ev.main_image ? (ev.main_image.startsWith('http') || ev.main_image.startsWith('/') ? ev.main_image : `/${ev.main_image}`) : null}
                                                    title={ev.name}
                                                    subtitle={ev.venue || ev.destination?.name}
                                                    text={ev.short_description}
                                                    price={ev.price}
                                                    wishType="event"
                                                    wishId={ev.id}
                                                    cta="Get Tickets"
                                                    ctaUrl={`/events/${ev.slug}`}
                                                />
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {adventures.length > 0 && (
                                <div className="col-lg-6">
                                    <div className="d-flex justify-content-between align-items-center mb-3">
                                        <h3 className="h4 mb-0 fw-bold">Adventure & Outdoor</h3>
                                        <Link href="/adventures" className="btn btn-sm btn-soft">All Adventures</Link>
                                    </div>
                                    <div className="row g-3">
                                        {adventures.slice(0, 2).map((adv) => (
                                            <div key={adv.id} className="col-12">
                                                <ItemCard
                                                    image={adv.main_image ? (adv.main_image.startsWith('http') || adv.main_image.startsWith('/') ? adv.main_image : `/${adv.main_image}`) : null}
                                                    title={adv.name}
                                                    subtitle={adv.destination?.name}
                                                    badge={adv.difficulty ? `Difficulty: ${adv.difficulty}` : null}
                                                    price={adv.price}
                                                    wishType="adventure"
                                                    wishId={adv.id}
                                                    cta="Book Adventure"
                                                    ctaUrl={`/adventures/${adv.slug}`}
                                                />
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </section>
            )}

            {/* Why Trip Sathi Section */}
            <section className="ts-section py-5">
                <div className="container text-center">
                    <span className="ts-eyebrow">Your trusted travel partner</span>
                    <h2 className="ts-title mb-5">Why Choose Trip Sathi?</h2>

                    <div className="row g-4 text-start">
                        <div className="col-md-4">
                            <div className="card h-100 border-0 shadow-sm p-4 rounded-4">
                                <div className="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex p-3 mb-3 fs-3" style={{ width: 'fit-content' }}>
                                    <i className="bi bi-map"></i>
                                </div>
                                <h4 className="fw-bold">All-In-One Platform</h4>
                                <p className="text-muted mb-0">
                                    Stays, temple darshan, spiritual rituals, excursions, and day-by-day itineraries managed under a single login.
                                </p>
                            </div>
                        </div>

                        <div className="col-md-4">
                            <div className="card h-100 border-0 shadow-sm p-4 rounded-4">
                                <div className="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex p-3 mb-3 fs-3" style={{ width: 'fit-content' }}>
                                    <i className="bi bi-shield-check"></i>
                                </div>
                                <h4 className="fw-bold">Guaranteed Slot Allocation</h4>
                                <p className="text-muted mb-0">
                                    Real-time concurrency locks ensure that once you book a room or darshan window, it is locked and guaranteed for you.
                                </p>
                            </div>
                        </div>

                        <div className="col-md-4">
                            <div className="card h-100 border-0 shadow-sm p-4 rounded-4">
                                <div className="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex p-3 mb-3 fs-3" style={{ width: 'fit-content' }}>
                                    <i className="bi bi-heart"></i>
                                </div>
                                <h4 className="fw-bold">Verified Travellers Only</h4>
                                <p className="text-muted mb-0">
                                    Every review and rating on our platform comes exclusively from guests who have completed their confirmed journey.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Testimonials */}
            {testimonials.length > 0 && (
                <section className="ts-section py-5 bg-light">
                    <div className="container">
                        <div className="text-center mb-5">
                            <span className="ts-eyebrow">Traveller Stories</span>
                            <h2 className="ts-title">Loved by Thousands of Travellers</h2>
                        </div>

                        <div className="row g-4">
                            {testimonials.slice(0, 3).map((t) => (
                                <div key={t.id} className="col-md-4">
                                    <div className="card h-100 border-0 shadow-sm p-4 rounded-4">
                                        <div className="mb-3 text-warning">
                                            {[...Array(t.rating || 5)].map((_, i) => (
                                                <i key={i} className="bi bi-star-fill me-1"></i>
                                            ))}
                                        </div>
                                        <blockquote className="card-text text-muted fst-italic mb-4">
                                            "{t.quote}"
                                        </blockquote>
                                        <div className="mt-auto d-flex align-items-center">
                                            <div className="rounded-circle bg-secondary bg-opacity-25 text-dark fw-bold d-flex align-items-center justify-content-center me-3" style={{ width: 44, height: 44 }}>
                                                {t.author_name.charAt(0)}
                                            </div>
                                            <div>
                                                <div className="fw-bold">{t.author_name}</div>
                                                <small className="text-muted">{t.location || t.author_designation}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {/* Newsletter Subscription */}
            <section className="ts-section py-5">
                <div className="container">
                    <div className="card bg-dark text-white p-5 rounded-4 border-0 position-relative overflow-hidden shadow">
                        <div className="row align-items-center">
                            <div className="col-lg-6">
                                <span className="ts-eyebrow text-warning">Stay in the loop</span>
                                <h2 className="display-6 fw-bold mb-3">Get Exclusive Travel Deals & Sacred Darshan Updates</h2>
                                <p className="text-white-50 mb-0">
                                    Sign up for our newsletter to receive handpicked travel ideas, seasonal discounts, and early access to festival tickets.
                                </p>
                            </div>
                            <div className="col-lg-6 mt-4 mt-lg-0">
                                <form onSubmit={handleNewsletterSubmit} className="d-flex flex-column flex-sm-row gap-2">
                                    <input
                                        type="email"
                                        className="form-control form-control-lg rounded-pill px-4"
                                        placeholder="Enter your email address"
                                        value={newsletterEmail}
                                        onChange={(e) => setNewsletterEmail(e.target.value)}
                                        required
                                    />
                                    <button
                                        type="submit"
                                        className="btn btn-accent btn-lg rounded-pill px-4"
                                        disabled={subscribing}
                                    >
                                        {subscribing ? 'Subscribing...' : 'Subscribe'}
                                    </button>
                                </form>
                                <small className="text-white-50 d-block mt-2">
                                    We respect your privacy. Unsubscribe anytime with 1 click.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}
