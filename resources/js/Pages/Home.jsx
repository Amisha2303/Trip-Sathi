import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import AppLayout from '../Layouts/AppLayout';
import ItemCard from '../Components/ItemCard';
import WishlistButton from '../Components/WishlistButton';

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

    // Smart Trip Search State
    const [searchState, setSearchState] = useState({
        destination: '',
        date: '',
        travelers: '2',
        budget: 'moderate',
        style: 'all',
    });
    const [searchError, setSearchError] = useState('');

    const [newsletterEmail, setNewsletterEmail] = useState('');
    const [subscribing, setSubscribing] = useState(false);
    const [subscribedSuccess, setSubscribedSuccess] = useState(false);

    // 9 Verified Travel Styles requested in Master Prompt
    const travelStyles = [
        { id: 'weekend', name: 'Weekend Getaways', icon: 'bi-lightning-charge-fill', tag: '1–2 Days', color: '#0F9D91' },
        { id: 'adventure', name: 'Adventure & Treks', icon: 'bi-compass-fill', tag: 'Outdoors', color: '#16324F' },
        { id: 'beaches', name: 'Beaches & Coastal', icon: 'bi-water', tag: 'Sun & Sand', color: '#0F9D91' },
        { id: 'nature', name: 'Nature & Wildlife', icon: 'bi-tree-fill', tag: 'Eco Trails', color: '#10B981' },
        { id: 'heritage', name: 'Heritage & Culture', icon: 'bi-bank', tag: 'Forts & Arts', color: '#16324F' },
        { id: 'family', name: 'Family Holidays', icon: 'bi-people-fill', tag: 'Comfort Stays', color: '#0F9D91' },
        { id: 'romantic', name: 'Romantic Trips', icon: 'bi-heart-fill', tag: 'Couples', color: '#FF795D' },
        { id: 'road-trips', name: 'Scenic Road Trips', icon: 'bi-signpost-split-fill', tag: 'Self Drive', color: '#16324F' },
        { id: 'budget', name: 'Budget Travel', icon: 'bi-piggy-bank-fill', tag: 'Value Deals', color: '#0F9D91' },
    ];

    // Curated 2, 3, 5, 7 Day Itineraries requested in Master Prompt
    const featuredItineraries = [
        {
            id: 'itinerary-goa',
            destination: 'Goa',
            route: 'North Goa Beaches → Old Goa Churches → Chapora Sunset',
            duration: '2 Days / 1 Night',
            daysBadge: '2-Day Itinerary',
            style: 'Weekend Getaway',
            experiences: ['Calangute & Anjuna sunset walks', 'UNESCO Basilica of Bom Jesus', 'Coastal seafood trail & beach shacks'],
            estimatedBudget: '₹7,500',
            budgetAssumption: 'Per person (Includes boutique stay, scooter rental & dining)',
            image: '/photos/destination-goa.jpg',
            slug: 'goa',
        },
        {
            id: 'itinerary-udaipur',
            destination: 'Rajasthan (Udaipur)',
            route: 'City Palace → Lake Pichola Cruise → Bagore Ki Haveli',
            duration: '3 Days / 2 Nights',
            daysBadge: '3-Day Itinerary',
            style: 'Heritage & Culture',
            experiences: ['Sunset boat ride on Lake Pichola', 'Mewar royal palace architecture', 'Evening Dharohar cultural folk performance'],
            estimatedBudget: '₹12,800',
            budgetAssumption: 'Per person (Heritage haveli stay, private cab & monument pass)',
            image: '/photos/destination-udaipur.jpg',
            slug: 'udaipur',
        },
        {
            id: 'itinerary-kerala',
            destination: 'Kerala Backwaters & Munnar',
            route: 'Kochi → Munnar Tea Hills → Alleppey Houseboat',
            duration: '5 Days / 4 Nights',
            daysBadge: '5-Day Itinerary',
            style: 'Nature & Romantic',
            experiences: ['Overnight traditional houseboat cruise', 'Cheeyappara waterfalls & tea estate walk', 'Authentic Kerala Sadya on banana leaf'],
            estimatedBudget: '₹22,500',
            budgetAssumption: 'Per person (Resort + Houseboat, transfers, all meals on boat)',
            image: '/photos/destination-kerala.jpg',
            slug: 'kerala',
        },
        {
            id: 'itinerary-kashmir',
            destination: 'Kashmir Odyssey',
            route: 'Srinagar Dal Lake → Gulmarg Gondola → Pahalgam Valleys',
            duration: '7 Days / 6 Nights',
            daysBadge: '7-Day Itinerary',
            style: 'Family & Mountain',
            experiences: ['Sunrise Shikara ride on Dal Lake', 'Phase 2 Gondola ride in Gulmarg', 'Betaab Valley pine forest pony trek'],
            estimatedBudget: '₹34,000',
            budgetAssumption: 'Per person (Houseboat stay, private 4x4 cab, heating & gondola)',
            image: '/photos/destination-kashmir.jpg',
            slug: 'kashmir',
        },
    ];

    // Travel Guides requested in Master Prompt
    const travelGuides = [
        {
            id: 'guide-kashmir',
            title: "First Timer's Complete Guide to Kashmir: Dal Lake to Gulmarg",
            category: 'Himalayan Escapes',
            readTime: '6 min read',
            publishedDate: 'Verified Oct 2026',
            description: 'Essential packing tips, houseboat etiquette, shikara prices, and gondola booking tips for a hassle-free trip.',
            image: '/photos/destination-kashmir.jpg',
            slug: 'kashmir',
        },
        {
            id: 'guide-ladakh',
            title: 'Ladakh High Altitude Travel Advisory: Acclimatization & Permits',
            category: 'Adventure & Road Trips',
            readTime: '5 min read',
            publishedDate: 'Verified Oct 2026',
            description: 'Medical precautions for Leh elevation, Inner Line Permit requirements for Pangong and Nubra, and best travel seasons.',
            image: '/photos/destination-ladakh.jpg',
            slug: 'ladakh',
        },
        {
            id: 'guide-rajasthan',
            title: 'The Ultimate Rajasthan Heritage Circuit: Jaipur to Udaipur',
            category: 'Heritage & Culture',
            readTime: '7 min read',
            publishedDate: 'Verified Oct 2026',
            description: 'Explore the pink city forts, lake palaces of Mewar, stepwell architecture, and royal Rajasthani dining.',
            image: '/photos/destination-jaipur.jpg',
            slug: 'jaipur',
        },
    ];

    // Top 8 Indian destinations specified in Master Prompt (Kashmir, Ladakh, Rajasthan, Kerala, Goa, Meghalaya, Sikkim, Andaman Islands)
    const promptDestinations = [
        {
            id: 'kashmir',
            name: 'Kashmir',
            slug: 'kashmir',
            state: 'Jammu & Kashmir',
            short_description: 'Paradise on Earth: Dal Lake shikara cruises, snow-capped peaks, and floating flower markets.',
            best_time_to_visit: 'Apr – Oct (Tulips & valleys) / Dec – Feb (Snow)',
            main_image: '/photos/destination-kashmir.jpg',
        },
        {
            id: 'ladakh',
            name: 'Ladakh',
            slug: 'ladakh',
            state: 'Ladakh',
            short_description: 'Dramatic high-altitude moonscapes, turquoise Pangong Tso, and century-old Buddhist gompas.',
            best_time_to_visit: 'May – Sep (Clear roads & mountain festivals)',
            main_image: '/photos/destination-ladakh.jpg',
        },
        {
            id: 'rajasthan',
            name: 'Rajasthan',
            slug: 'jaipur',
            state: 'Rajasthan',
            short_description: 'Land of royal palaces, vibrant bazaars, historic hill forts, and sunset desert camel safaris.',
            best_time_to_visit: 'Oct – Mar (Pleasant weather & cultural fairs)',
            main_image: '/photos/destination-jaipur.jpg',
        },
        {
            id: 'kerala',
            name: 'Kerala',
            slug: 'kerala',
            state: 'Kerala',
            short_description: 'Tranquil palm-lined backwaters, spice plantations, mist-covered tea gardens, and beaches.',
            best_time_to_visit: 'Sep – Mar (Cool breezes) / Jun – Aug (Monsoons)',
            main_image: '/photos/destination-kerala.jpg',
        },
        {
            id: 'goa',
            name: 'Goa',
            slug: 'goa',
            state: 'Goa',
            short_description: 'Golden sandy shores, Portuguese heritage villas, coastal seafood, and tropical beach sunsets.',
            best_time_to_visit: 'Nov – Feb (Beach season) / Jul – Sep (Lush green)',
            main_image: '/photos/destination-goa.jpg',
        },
        {
            id: 'meghalaya',
            name: 'Meghalaya',
            slug: 'meghalaya',
            state: 'Meghalaya',
            short_description: 'The abode of clouds, living root bridges, mystical rainforests, and roaring waterfalls.',
            best_time_to_visit: 'Oct – Apr (Caving & trekking) / Jun – Sep (Waterfalls)',
            main_image: '/photos/destination-meghalaya.jpg',
        },
        {
            id: 'sikkim',
            name: 'Sikkim',
            slug: 'sikkim',
            state: 'Sikkim',
            short_description: 'Majestic Kanchenjunga panoramas, tranquil Buddhist monasteries, and crystal alpine lakes.',
            best_time_to_visit: 'Mar – May (Rhododendrons) / Oct – Dec (Clear skies)',
            main_image: '/photos/destination-sikkim.jpg',
        },
        {
            id: 'andaman-islands',
            name: 'Andaman Islands',
            slug: 'andaman-islands',
            state: 'Andaman & Nicobar',
            short_description: 'Pristine Radhanagar beaches, vibrant living coral reefs, and turquoise island waters.',
            best_time_to_visit: 'Oct – May (Snorkeling, diving & calm seas)',
            main_image: '/photos/destination-andaman.jpg',
        },
    ];

    // Priority-merge prompt destinations with incoming DB destinations, ensuring authentic verified photography
    const trendingDestinations = promptDestinations.map((promptD) => {
        const match = destinations.find(
            (d) => d.slug === promptD.slug || d.name?.toLowerCase() === promptD.name.toLowerCase()
        );
        if (match) {
            return {
                ...match,
                main_image: promptD.main_image, // Always ensure authentic verified photography
                best_time_to_visit: match.best_time_to_visit || promptD.best_time_to_visit,
                state: match.state || promptD.state,
                short_description: match.short_description || promptD.short_description,
            };
        }
        return promptD;
    });

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        setSearchError('');

        const params = new URLSearchParams();
        if (searchState.destination) params.append('destination', searchState.destination);
        if (searchState.date) params.append('date', searchState.date);
        if (searchState.travelers) params.append('travelers', searchState.travelers);
        if (searchState.budget && searchState.budget !== 'all') params.append('budget', searchState.budget);
        if (searchState.style && searchState.style !== 'all') params.append('style', searchState.style);

        // If specific destination selected, route to that destination or trips
        if (searchState.destination) {
            router.visit(`/destinations/${searchState.destination}`);
        } else {
            router.visit(`/destinations?${params.toString()}`);
        }
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
                onSuccess: () => {
                    setNewsletterEmail('');
                    setSubscribedSuccess(true);
                },
                onFinish: () => setSubscribing(false),
            }
        );
    };

    return (
        <AppLayout
            title="Trip Sathi — Your Next Adventure Starts Here"
            description="Discover incredible places, build your itinerary, and make every journey yours with Trip Sathi."
        >
            {/* 1. HERO SECTION */}
            <section
                className="position-relative overflow-hidden text-white"
                style={{
                    backgroundColor: '#16324F',
                    backgroundImage: `linear-gradient(180deg, rgba(22, 50, 79, 0.72) 0%, rgba(22, 50, 79, 0.88) 100%), url('/photos/banners/banner-home.jpg')`,
                    backgroundSize: 'cover',
                    backgroundPosition: 'center 35%',
                    backgroundRepeat: 'no-repeat',
                    paddingTop: 'clamp(3.5rem, 7vw, 5.5rem)',
                    paddingBottom: 'clamp(4rem, 8vw, 6rem)',
                }}
            >
                <div className="container">
                    <div className="row justify-content-center text-center">
                        <div className="col-xl-9 col-lg-10">
                            {/* Subtle Eyebrow Badge */}
                            <div className="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3 border border-light border-opacity-25" style={{ backgroundColor: 'rgba(255, 255, 255, 0.1)', backdropFilter: 'blur(8px)' }}>
                                <i className="bi bi-compass text-teal" style={{ color: '#0F9D91' }}></i>
                                <span className="small fw-semibold text-white letter-spacing-1">DISCOVER &bull; PLAN &bull; EXPLORE INDIA</span>
                            </div>

                            {/* Headline specified in Master Prompt */}
                            <h1 className="fw-extrabold display-5 display-md-4 mb-3 text-white" style={{ fontFamily: "'Manrope', 'Inter', sans-serif", letterSpacing: '-0.025em', lineHeight: 1.15, color: '#FFFFFF', textShadow: '0 3px 18px rgba(0, 0, 0, 0.5)' }}>
                                Your Next Adventure Starts Here.
                            </h1>

                            {/* Supporting text specified in Master Prompt */}
                            <p className="lead mb-4 mx-auto text-light text-opacity-90" style={{ maxWidth: '62ch', fontSize: 'clamp(1rem, 1.8vw, 1.2rem)', lineHeight: 1.6 }}>
                                Discover incredible places, build your itinerary, and make every journey yours.
                            </p>

                            {/* Hero Action Buttons */}
                            <div className="d-flex flex-wrap align-items-center justify-content-center gap-3 mb-4 pb-2">
                                <Link
                                    href="/trips?action=create"
                                    className="btn btn-lg fw-bold px-4 py-2.5 rounded-pill text-white shadow"
                                    style={{ backgroundColor: '#0F9D91', borderColor: '#0F9D91', transition: 'all 0.2s ease' }}
                                >
                                    <i className="bi bi-send-fill me-2"></i>
                                    Plan My Trip
                                </Link>

                                <Link
                                    href="/destinations"
                                    className="btn btn-lg fw-semibold px-4 py-2.5 rounded-pill text-white border border-light border-opacity-40"
                                    style={{ backgroundColor: 'rgba(255, 255, 255, 0.08)', backdropFilter: 'blur(6px)', transition: 'all 0.2s ease' }}
                                >
                                    <i className="bi bi-compass me-2"></i>
                                    Explore Destinations
                                </Link>
                            </div>

                            {/* Quick Destination Pill Suggestions */}
                            <div className="d-flex flex-wrap align-items-center justify-content-center gap-2 pt-1">
                                <span className="small text-white-50 me-1">Popular right now:</span>
                                {['Kashmir', 'Ladakh', 'Kerala', 'Goa', 'Rajasthan', 'Meghalaya'].map((tag) => (
                                    <Link
                                        key={tag}
                                        href={`/destinations/${tag.toLowerCase()}`}
                                        className="badge text-decoration-none fw-medium px-2.5 py-1.5 rounded-pill border border-light border-opacity-25"
                                        style={{ backgroundColor: 'rgba(255, 255, 255, 0.1)', color: '#FFFFFF', fontSize: '0.78rem' }}
                                    >
                                        {tag}
                                    </Link>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* 2. SMART TRIP SEARCH BAR */}
            <div className="container" style={{ marginTop: '-2.5rem', position: 'relative', zIndex: 20 }}>
                <div className="card border-0 shadow-lg p-3 p-md-4 rounded-4" style={{ backgroundColor: '#FFFFFF', border: '1px solid #E2E8F0' }}>
                    <form onSubmit={handleSearchSubmit}>
                        <div className="row g-3 align-items-end">
                            {/* Destination Field */}
                            <div className="col-12 col-md-6 col-lg-3">
                                <label htmlFor="search-destination" className="form-label small fw-bold text-uppercase mb-1" style={{ color: '#16324F', letterSpacing: '0.04em' }}>
                                    <i className="bi bi-geo-alt-fill me-1" style={{ color: '#0F9D91' }}></i> Destination
                                </label>
                                <select
                                    className="form-select border rounded-3 py-2"
                                    id="search-destination"
                                    value={searchState.destination}
                                    onChange={(e) => setSearchState({ ...searchState, destination: e.target.value })}
                                    style={{ borderColor: '#CBD5E1', fontSize: '0.92rem' }}
                                >
                                    <option value="">Anywhere in India</option>
                                    {destinationOptions.map((d) => (
                                        <option key={d.id} value={d.slug}>
                                            {d.name}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Travel Dates */}
                            <div className="col-6 col-md-3 col-lg-2">
                                <label htmlFor="search-date" className="form-label small fw-bold text-uppercase mb-1" style={{ color: '#16324F', letterSpacing: '0.04em' }}>
                                    <i className="bi bi-calendar3 me-1" style={{ color: '#0F9D91' }}></i> Travel Date
                                </label>
                                <input
                                    type="date"
                                    id="search-date"
                                    className="form-control border rounded-3 py-2"
                                    value={searchState.date}
                                    onChange={(e) => setSearchState({ ...searchState, date: e.target.value })}
                                    style={{ borderColor: '#CBD5E1', fontSize: '0.92rem' }}
                                />
                            </div>

                            {/* Number of Travelers */}
                            <div className="col-6 col-md-3 col-lg-2">
                                <label htmlFor="search-travelers" className="form-label small fw-bold text-uppercase mb-1" style={{ color: '#16324F', letterSpacing: '0.04em' }}>
                                    <i className="bi bi-people-fill me-1" style={{ color: '#0F9D91' }}></i> Travelers
                                </label>
                                <select
                                    className="form-select border rounded-3 py-2"
                                    id="search-travelers"
                                    value={searchState.travelers}
                                    onChange={(e) => setSearchState({ ...searchState, travelers: e.target.value })}
                                    style={{ borderColor: '#CBD5E1', fontSize: '0.92rem' }}
                                >
                                    <option value="1">Solo (1 person)</option>
                                    <option value="2">Couple (2 people)</option>
                                    <option value="3">Family (3–4 people)</option>
                                    <option value="5">Group (5+ people)</option>
                                </select>
                            </div>

                            {/* Target Budget */}
                            <div className="col-6 col-md-4 col-lg-2">
                                <label htmlFor="search-budget" className="form-label small fw-bold text-uppercase mb-1" style={{ color: '#16324F', letterSpacing: '0.04em' }}>
                                    <i className="bi bi-wallet2 me-1" style={{ color: '#0F9D91' }}></i> Target Budget
                                </label>
                                <select
                                    className="form-select border rounded-3 py-2"
                                    id="search-budget"
                                    value={searchState.budget}
                                    onChange={(e) => setSearchState({ ...searchState, budget: e.target.value })}
                                    style={{ borderColor: '#CBD5E1', fontSize: '0.92rem' }}
                                >
                                    <option value="all">Any Budget</option>
                                    <option value="budget">Budget Friendly (Under ₹5k)</option>
                                    <option value="moderate">Moderate (₹5k – ₹15k)</option>
                                    <option value="luxury">Luxury / Premium (₹15k+)</option>
                                </select>
                            </div>

                            {/* Search Submit CTA */}
                            <div className="col-6 col-md-4 col-lg-3">
                                <button
                                    type="submit"
                                    className="btn w-100 fw-bold py-2 rounded-3 text-white d-flex align-items-center justify-content-center gap-2 shadow-sm"
                                    style={{ backgroundColor: '#0F9D91', borderColor: '#0F9D91', height: '42px' }}
                                >
                                    <i className="bi bi-search"></i>
                                    <span>Search Trips</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {/* 3. TRENDING DESTINATIONS SECTION */}
            <section className="py-5" style={{ backgroundColor: '#F7F8F5' }}>
                <div className="container py-3">
                    <div className="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
                        <div>
                            <span className="small fw-bold text-uppercase" style={{ color: '#0F9D91', letterSpacing: '0.08em' }}>
                                Authentic Indian Escapes
                            </span>
                            <h2 className="h2 fw-extrabold mb-1" style={{ color: '#16324F', fontFamily: "'Manrope', 'Inter', sans-serif" }}>
                                Trending Destinations
                            </h2>
                            <p className="text-muted mb-0" style={{ maxWidth: '58ch' }}>
                                Handpicked regions across India with verified season guidelines, rich culture, and ready itineraries.
                            </p>
                        </div>
                        <Link href="/destinations" className="btn btn-outline-dark rounded-pill px-3.5 py-1.5 fw-semibold small" style={{ borderColor: '#CBD5E1', color: '#16324F' }}>
                            View All Destinations <i className="bi bi-arrow-right ms-1"></i>
                        </Link>
                    </div>

                    <div className="row g-4">
                        {trendingDestinations.map((d) => (
                            <div key={d.id || d.slug} className="col-12 col-sm-6 col-lg-3">
                                <div className="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative" style={{ backgroundColor: '#FFFFFF', border: '1px solid #E2E8F0', transition: 'transform 0.22s ease, box-shadow 0.22s ease' }}>
                                    {/* Destination Image with Aspect Ratio */}
                                    <div className="position-relative" style={{ aspectRatio: '4/3', overflow: 'hidden', backgroundColor: '#E2E8F0' }}>
                                        <img
                                            src={d.main_image ? (d.main_image.startsWith('http') || d.main_image.startsWith('/') ? d.main_image : `/${d.main_image}`) : '/photos/destination-goa.jpg'}
                                            alt={d.name}
                                            loading="lazy"
                                            className="w-100 h-100 object-fit-cover"
                                            style={{ transition: 'transform 0.4s ease' }}
                                        />
                                        {/* State tag */}
                                        <span className="position-absolute top-0 start-0 m-2.5 badge rounded-pill px-2.5 py-1 fw-semibold" style={{ backgroundColor: 'rgba(22, 50, 79, 0.85)', backdropFilter: 'blur(4px)', color: '#fff', fontSize: '0.72rem' }}>
                                            {d.state || 'India'}
                                        </span>
                                        {/* Save Button */}
                                        <div className="position-absolute top-0 end-0 m-2">
                                            <WishlistButton itemType="destination" itemId={typeof d.id === 'number' ? d.id : 1} />
                                        </div>
                                    </div>

                                    {/* Card Content */}
                                    <div className="p-3.5 d-flex flex-column flex-grow-1">
                                        <h3 className="h5 fw-bold mb-1" style={{ color: '#16324F' }}>
                                            <Link href={`/destinations/${d.slug}`} className="text-decoration-none text-dark">
                                                {d.name}
                                            </Link>
                                        </h3>
                                        <p className="small text-muted mb-2.5 text-truncate-2" style={{ minHeight: '38px', lineHeight: 1.45, fontSize: '0.86rem' }}>
                                            {d.short_description || `Discover the enchanting beauty, stays and cultural experiences in ${d.name}.`}
                                        </p>

                                        {/* Verified Best Season Tag */}
                                        {d.best_time_to_visit && (
                                            <div className="small mb-3 d-flex align-items-center gap-1.5" style={{ color: '#0F9D91', fontSize: '0.8rem' }}>
                                                <i className="bi bi-brightness-high-fill"></i>
                                                <span className="text-truncate">Best: <strong>{d.best_time_to_visit}</strong></span>
                                            </div>
                                        )}

                                        <div className="mt-auto pt-2 border-top d-flex align-items-center justify-content-between" style={{ borderColor: '#F1F5F9' }}>
                                            <Link
                                                href={`/destinations/${d.slug}`}
                                                className="fw-bold small text-decoration-none d-inline-flex align-items-center gap-1"
                                                style={{ color: '#0F9D91' }}
                                            >
                                                Explore Guide <i className="bi bi-chevron-right" style={{ fontSize: '0.75rem' }}></i>
                                            </Link>
                                            <Link
                                                href={`/trips?action=create&destination=${d.slug}`}
                                                className="btn btn-sm py-1 px-2.5 rounded-pill fw-semibold text-white"
                                                style={{ backgroundColor: '#16324F', fontSize: '0.76rem' }}
                                            >
                                                Plan Trip
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* 4. EXPLORE BY TRAVEL STYLE (9 Categories) */}
            <section className="py-5" id="styles" style={{ backgroundColor: '#FFFFFF', borderTop: '1px solid #E2E8F0', borderBottom: '1px solid #E2E8F0' }}>
                <div className="container py-2">
                    <div className="text-center mb-5">
                        <span className="small fw-bold text-uppercase" style={{ color: '#0F9D91', letterSpacing: '0.08em' }}>
                            Find Your Rhythm
                        </span>
                        <h2 className="h2 fw-extrabold mb-2" style={{ color: '#16324F', fontFamily: "'Manrope', 'Inter', sans-serif" }}>
                            Explore By Travel Style
                        </h2>
                        <p className="text-muted mx-auto mb-0" style={{ maxWidth: '58ch' }}>
                            Whether you seek peaceful beach sunrises, challenging mountain treks, or immersive heritage walks, we have a journey mapped for you.
                        </p>
                    </div>

                    <div className="row g-3 g-md-4">
                        {travelStyles.map((style) => (
                            <div key={style.id} className="col-6 col-md-4 col-lg-4">
                                <Link
                                    href={`/destinations?style=${style.id}`}
                                    className="card h-100 p-3 p-md-4 rounded-4 text-decoration-none border shadow-sm d-flex flex-row align-items-center gap-3 transition-all"
                                    style={{ backgroundColor: '#F8FAFC', borderColor: '#E2E8F0', transition: 'all 0.2s ease' }}
                                >
                                    <div
                                        style={{
                                            width: '48px',
                                            height: '48px',
                                            minWidth: '48px',
                                            borderRadius: '12px',
                                            backgroundColor: 'rgba(15, 157, 145, 0.1)',
                                            color: style.color,
                                            display: 'inline-flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            fontSize: '1.4rem'
                                        }}
                                    >
                                        <i className={`bi ${style.icon}`}></i>
                                    </div>
                                    <div className="min-w-0">
                                        <span className="badge rounded-pill mb-1" style={{ backgroundColor: '#E2E8F0', color: '#64748B', fontSize: '0.68rem', fontWeight: 600 }}>
                                            {style.tag}
                                        </span>
                                        <h4 className="h6 fw-bold mb-0 text-truncate" style={{ color: '#16324F' }}>
                                            {style.name}
                                        </h4>
                                    </div>
                                    <i className="bi bi-chevron-right ms-auto text-muted" style={{ fontSize: '0.8rem' }}></i>
                                </Link>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* 5. FEATURED ITINERARIES (2, 3, 5, 7 Days) */}
            <section className="py-5" style={{ backgroundColor: '#F7F8F5' }}>
                <div className="container py-3">
                    <div className="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
                        <div>
                            <span className="small fw-bold text-uppercase" style={{ color: '#0F9D91', letterSpacing: '0.08em' }}>
                                Ready-To-Follow Roadmaps
                            </span>
                            <h2 className="h2 fw-extrabold mb-1" style={{ color: '#16324F', fontFamily: "'Manrope', 'Inter', sans-serif" }}>
                                Featured Itineraries
                            </h2>
                            <p className="text-muted mb-0" style={{ maxWidth: '58ch' }}>
                                Expertly crafted day-by-day plans with transparent budget assumptions and instant customization.
                            </p>
                        </div>
                        <Link href="/trips" className="btn btn-outline-dark rounded-pill px-3.5 py-1.5 fw-semibold small" style={{ borderColor: '#CBD5E1', color: '#16324F' }}>
                            Explore All Itineraries <i className="bi bi-arrow-right ms-1"></i>
                        </Link>
                    </div>

                    <div className="row g-4">
                        {featuredItineraries.map((itin) => (
                            <div key={itin.id} className="col-12 col-md-6 col-lg-3">
                                <div className="card h-100 border-0 shadow-sm rounded-4 overflow-hidden d-flex flex-column" style={{ backgroundColor: '#FFFFFF', border: '1px solid #E2E8F0' }}>
                                    {/* Cover Media */}
                                    <div className="position-relative" style={{ aspectRatio: '16/10', overflow: 'hidden' }}>
                                        <img
                                            src={itin.image}
                                            alt={itin.destination}
                                            loading="lazy"
                                            className="w-100 h-100 object-fit-cover"
                                        />
                                        <span className="position-absolute top-0 start-0 m-2.5 badge rounded-pill px-2.5 py-1" style={{ backgroundColor: '#0F9D91', color: '#fff', fontSize: '0.72rem', fontWeight: 700 }}>
                                            {itin.daysBadge}
                                        </span>
                                        <span className="position-absolute bottom-0 end-0 m-2.5 badge rounded-pill px-2.5 py-1" style={{ backgroundColor: 'rgba(22, 50, 79, 0.9)', backdropFilter: 'blur(4px)', color: '#fff', fontSize: '0.72rem' }}>
                                            {itin.style}
                                        </span>
                                    </div>

                                    {/* Details */}
                                    <div className="p-3.5 d-flex flex-column flex-grow-1">
                                        <h3 className="h6 fw-bold mb-1" style={{ color: '#16324F' }}>
                                            {itin.destination}
                                        </h3>
                                        <div className="small text-muted mb-2 d-flex align-items-center gap-1">
                                            <i className="bi bi-geo-alt" style={{ color: '#0F9D91' }}></i>
                                            <span className="text-truncate">{itin.route}</span>
                                        </div>

                                        {/* Key Experiences List */}
                                        <div className="my-2 p-2.5 rounded-3" style={{ backgroundColor: '#F8FAFC', border: '1px solid #F1F5F9' }}>
                                            <div className="small fw-bold text-uppercase mb-1 text-muted" style={{ fontSize: '0.68rem', letterSpacing: '0.04em' }}>
                                                Highlights Included:
                                            </div>
                                            <ul className="list-unstyled mb-0" style={{ fontSize: '0.8rem', color: '#334E68', lineHeight: 1.5 }}>
                                                {itin.experiences.map((exp, idx) => (
                                                    <li key={idx} className="d-flex align-items-start gap-1.5 mb-1 text-truncate">
                                                        <i className="bi bi-check2 text-teal mt-0.5" style={{ color: '#0F9D91' }}></i>
                                                        <span className="text-truncate">{exp}</span>
                                                    </li>
                                                ))}
                                            </ul>
                                        </div>

                                        {/* Budget Breakdown */}
                                        <div className="mt-auto pt-2">
                                            <div className="d-flex align-items-baseline justify-content-between mb-1">
                                                <span className="small text-muted">Est. Budget:</span>
                                                <strong className="h6 fw-extrabold mb-0" style={{ color: '#16324F' }}>{itin.estimatedBudget}</strong>
                                            </div>
                                            <div className="small text-muted fst-italic mb-3" style={{ fontSize: '0.72rem', lineHeight: 1.35 }}>
                                                {itin.budgetAssumption}
                                            </div>

                                            {/* Customize CTA */}
                                            <Link
                                                href={`/trips?action=create&destination=${itin.slug}&duration=${itin.id}`}
                                                className="btn w-100 fw-bold py-2 rounded-3 text-white d-flex align-items-center justify-content-center gap-1.5 shadow-sm"
                                                style={{ backgroundColor: '#0F9D91', borderColor: '#0F9D91', fontSize: '0.88rem' }}
                                            >
                                                <i className="bi bi-sliders"></i>
                                                <span>Customize Itinerary</span>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* 6. WHY TRIP SATHI (Platform Benefits Preview) */}
            <section className="py-5" style={{ backgroundColor: '#FFFFFF', borderTop: '1px solid #E2E8F0' }}>
                <div className="container py-3">
                    <div className="text-center mb-5">
                        <span className="small fw-bold text-uppercase" style={{ color: '#0F9D91', letterSpacing: '0.08em' }}>
                            Designed for Modern Explorers
                        </span>
                        <h2 className="h2 fw-extrabold mb-2" style={{ color: '#16324F', fontFamily: "'Manrope', 'Inter', sans-serif" }}>
                            Why Plan With Trip Sathi?
                        </h2>
                        <p className="text-muted mx-auto mb-0" style={{ maxWidth: '62ch' }}>
                            Everything you need to turn a travel dream into a smooth, organized, memorable reality across India.
                        </p>
                    </div>

                    <div className="row g-4">
                        {[
                            {
                                icon: 'bi-calendar-range',
                                title: 'Personalized Trip Planning',
                                text: 'Organize daily stops, hotels, dining, and sightseeing schedules tailored specifically to your group pace.',
                                badge: 'Live Feature',
                            },
                            {
                                icon: 'bi-calculator',
                                title: 'Smart Budget Estimation',
                                text: 'Real-time estimated costs for stays, transport, meals, and tickets with transparent assumption breakdowns.',
                                badge: 'Built-in',
                            },
                            {
                                icon: 'bi-compass',
                                title: 'Destination Discovery',
                                text: 'Curated Indian locations from Kashmir to Kerala with verified travel seasons, local food, and safety notes.',
                                badge: 'Verified Content',
                            },
                            {
                                icon: 'bi-map',
                                title: 'Interactive Itinerary Stops',
                                text: 'Reorder day-by-day stops, add custom activities, and keep all vouchers and ticket timings in one place.',
                                badge: 'Live Builder',
                            },
                            {
                                icon: 'bi-heart',
                                title: 'Saved Trips & Wishlist',
                                text: 'Bookmark your favourite stays, hidden gems, and roadmaps to revisit and fine-tune whenever you are ready.',
                                badge: 'Auto-Sync',
                            },
                            {
                                icon: 'bi-share',
                                title: 'Shareable & Printable Plans',
                                text: 'Share read-only itinerary links with your fellow travel companions or export clean PDF summaries for offline access.',
                                badge: 'Export Ready',
                            },
                        ].map((feature, i) => (
                            <div key={i} className="col-12 col-md-6 col-lg-4">
                                <div className="card h-100 p-4 rounded-4 border shadow-sm" style={{ backgroundColor: '#F8FAFC', borderColor: '#E2E8F0' }}>
                                    <div className="d-flex align-items-center justify-content-between mb-3">
                                        <div
                                            style={{
                                                width: '46px',
                                                height: '46px',
                                                borderRadius: '12px',
                                                backgroundColor: 'rgba(15, 157, 145, 0.12)',
                                                color: '#0F9D91',
                                                display: 'inline-flex',
                                                alignItems: 'center',
                                                justifyContent: 'center',
                                                fontSize: '1.35rem'
                                            }}
                                        >
                                            <i className={`bi ${feature.icon}`}></i>
                                        </div>
                                        <span className="badge rounded-pill px-2.5 py-1" style={{ backgroundColor: '#E2E8F0', color: '#16324F', fontSize: '0.7rem' }}>
                                            {feature.badge}
                                        </span>
                                    </div>
                                    <h3 className="h6 fw-bold mb-2" style={{ color: '#16324F' }}>
                                        {feature.title}
                                    </h3>
                                    <p className="small text-muted mb-0" style={{ lineHeight: 1.6 }}>
                                        {feature.text}
                                    </p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* 7. TRAVEL GUIDES & EDITORIALS */}
            <section className="py-5" id="guides" style={{ backgroundColor: '#F7F8F5' }}>
                <div className="container py-3">
                    <div className="d-flex flex-wrap justify-content-between align-items-end mb-4 gap-3">
                        <div>
                            <span className="small fw-bold text-uppercase" style={{ color: '#0F9D91', letterSpacing: '0.08em' }}>
                                Editorial Insights
                            </span>
                            <h2 className="h2 fw-extrabold mb-1" style={{ color: '#16324F', fontFamily: "'Manrope', 'Inter', sans-serif" }}>
                                Handpicked Travel Guides
                            </h2>
                            <p className="text-muted mb-0" style={{ maxWidth: '58ch' }}>
                                Practical advice, local etiquette, permit checklists, and hidden spots verified by Indian travelers.
                            </p>
                        </div>
                        <Link href="/destinations#guides" className="btn btn-outline-dark rounded-pill px-3.5 py-1.5 fw-semibold small" style={{ borderColor: '#CBD5E1', color: '#16324F' }}>
                            Read All Guides <i className="bi bi-arrow-right ms-1"></i>
                        </Link>
                    </div>

                    <div className="row g-4">
                        {travelGuides.map((guide) => (
                            <div key={guide.id} className="col-12 col-md-4">
                                <div className="card h-100 border-0 shadow-sm rounded-4 overflow-hidden" style={{ backgroundColor: '#FFFFFF', border: '1px solid #E2E8F0' }}>
                                    <div style={{ aspectRatio: '16/10', overflow: 'hidden' }}>
                                        <img
                                            src={guide.image}
                                            alt={guide.title}
                                            loading="lazy"
                                            className="w-100 h-100 object-fit-cover"
                                        />
                                    </div>
                                    <div className="p-3.5 d-flex flex-column flex-grow-1">
                                        <div className="d-flex align-items-center justify-content-between gap-2 mb-2">
                                            <span className="badge rounded-pill" style={{ backgroundColor: 'rgba(15, 157, 145, 0.1)', color: '#0F9D91', fontSize: '0.72rem' }}>
                                                {guide.category}
                                            </span>
                                            <small className="text-muted" style={{ fontSize: '0.74rem' }}>{guide.readTime}</small>
                                        </div>
                                        <h3 className="h6 fw-bold mb-2" style={{ color: '#16324F', lineHeight: 1.35 }}>
                                            <Link href={`/destinations/${guide.slug}`} className="text-decoration-none text-dark">
                                                {guide.title}
                                            </Link>
                                        </h3>
                                        <p className="small text-muted mb-3 flex-grow-1" style={{ fontSize: '0.85rem', lineHeight: 1.5 }}>
                                            {guide.description}
                                        </p>
                                        <div className="pt-2 border-top d-flex align-items-center justify-content-between" style={{ borderColor: '#F1F5F9' }}>
                                            <span className="small text-muted" style={{ fontSize: '0.75rem' }}>{guide.publishedDate}</span>
                                            <Link href={`/destinations/${guide.slug}`} className="fw-bold small text-decoration-none" style={{ color: '#0F9D91' }}>
                                                Read Guide <i className="bi bi-arrow-right"></i>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* 8. TRAVELER STORIES & TRUST (Genuine Reviews Only) */}
            {testimonials.length > 0 && (
                <section className="py-5" style={{ backgroundColor: '#FFFFFF', borderTop: '1px solid #E2E8F0' }}>
                    <div className="container py-3">
                        <div className="text-center mb-5">
                            <span className="small fw-bold text-uppercase" style={{ color: '#0F9D91', letterSpacing: '0.08em' }}>
                                Real Feedback
                            </span>
                            <h2 className="h2 fw-extrabold mb-2" style={{ color: '#16324F', fontFamily: "'Manrope', 'Inter', sans-serif" }}>
                                Stories From Travelers
                            </h2>
                            <p className="text-muted mx-auto mb-0" style={{ maxWidth: '58ch' }}>
                                Genuine thoughts from travelers who explored Indian destinations with Trip Sathi.
                            </p>
                        </div>

                        <div className="row g-4">
                            {testimonials.slice(0, 3).map((t) => (
                                <div key={t.id} className="col-12 col-md-4">
                                    <div className="card h-100 border p-4 rounded-4 shadow-sm d-flex flex-column" style={{ backgroundColor: '#F8FAFC', borderColor: '#E2E8F0' }}>
                                        <div className="mb-3 d-flex gap-1" style={{ color: '#F59E0B' }}>
                                            {[...Array(t.rating || 5)].map((_, i) => (
                                                <i key={i} className="bi bi-star-fill" style={{ fontSize: '0.85rem' }}></i>
                                            ))}
                                        </div>
                                        <blockquote className="fst-italic text-muted mb-4 flex-grow-1" style={{ fontSize: '0.92rem', lineHeight: 1.6 }}>
                                            "{t.quote}"
                                        </blockquote>
                                        <div className="d-flex align-items-center gap-3 pt-2 border-top" style={{ borderColor: '#E2E8F0' }}>
                                            <div
                                                style={{
                                                    width: '40px',
                                                    height: '40px',
                                                    borderRadius: '50%',
                                                    backgroundColor: '#16324F',
                                                    color: '#fff',
                                                    fontWeight: 700,
                                                    display: 'inline-flex',
                                                    alignItems: 'center',
                                                    justifyContent: 'center',
                                                    fontSize: '0.95rem'
                                                }}
                                            >
                                                {t.author_name ? t.author_name.charAt(0) : 'T'}
                                            </div>
                                            <div>
                                                <strong className="d-block text-dark small" style={{ fontSize: '0.88rem' }}>{t.author_name}</strong>
                                                <small className="text-muted" style={{ fontSize: '0.78rem' }}>{t.location || t.author_designation || 'Verified Traveler'}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {/* 9. FINAL CALL TO ACTION BANNER */}
            <section className="py-5" style={{ backgroundColor: '#F7F8F5' }}>
                <div className="container">
                    <div
                        className="p-4 p-md-5 rounded-4 text-white position-relative overflow-hidden shadow-lg"
                        style={{
                            background: 'linear-gradient(135deg, #16324F 0%, #0F2338 100%)',
                            border: '1px solid rgba(255, 255, 255, 0.12)'
                        }}
                    >
                        <div className="row align-items-center justify-content-between g-4">
                            <div className="col-lg-7">
                                <span className="small fw-bold text-uppercase px-2.5 py-1 rounded-pill mb-3 d-inline-block" style={{ backgroundColor: 'rgba(15, 157, 145, 0.25)', color: '#16B8AB' }}>
                                    Start Exploring
                                </span>
                                <h2 className="h2 fw-extrabold mb-3" style={{ fontFamily: "'Manrope', 'Inter', sans-serif" }}>
                                    Ready to Build Your Perfect Itinerary?
                                </h2>
                                <p className="text-light text-opacity-85 mb-0" style={{ maxWidth: '52ch', lineHeight: 1.6 }}>
                                    Create a custom day-by-day plan with transparent costs, curated stays, and local experiences. No credit card required to start planning.
                                </p>
                            </div>

                            <div className="col-lg-4 text-lg-end">
                                <div className="d-flex flex-column flex-sm-row flex-lg-column gap-2.5 justify-content-lg-end">
                                    <Link
                                        href="/trips?action=create"
                                        className="btn btn-lg fw-bold px-4 py-2.5 rounded-pill text-white shadow-sm"
                                        style={{ backgroundColor: '#0F9D91', borderColor: '#0F9D91' }}
                                    >
                                        <i className="bi bi-send-fill me-2"></i>
                                        Plan My Trip Now
                                    </Link>
                                    <Link
                                        href="/destinations"
                                        className="btn btn-lg fw-semibold px-4 py-2.5 rounded-pill text-white border border-light border-opacity-25"
                                        style={{ backgroundColor: 'rgba(255, 255, 255, 0.08)' }}
                                    >
                                        Browse Destinations
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* 10. NEWSLETTER SECTION */}
            <section className="py-4 py-md-5" style={{ backgroundColor: '#FFFFFF', borderTop: '1px solid #E2E8F0' }}>
                <div className="container">
                    <div className="row align-items-center justify-content-between g-4">
                        <div className="col-lg-6">
                            <span className="small fw-bold text-uppercase" style={{ color: '#0F9D91', letterSpacing: '0.08em' }}>
                                Stay In Touch
                            </span>
                            <h3 className="h4 fw-bold mb-2" style={{ color: '#16324F' }}>
                                Get Authentic Indian Travel Discoveries
                            </h3>
                            <p className="text-muted mb-0 small" style={{ maxWidth: '50ch' }}>
                                Receive verified seasonal guides, newly added destinations, and exclusive festival travel updates directly in your inbox.
                            </p>
                        </div>
                        <div className="col-lg-5">
                            {subscribedSuccess ? (
                                <div className="alert alert-success d-flex align-items-center gap-2 mb-0 py-2.5 rounded-3">
                                    <i className="bi bi-check-circle-fill fs-5"></i>
                                    <span className="small">Thank you for subscribing! Check your email for our welcome guide.</span>
                                </div>
                            ) : (
                                <form onSubmit={handleNewsletterSubmit} className="d-flex gap-2">
                                    <input
                                        type="email"
                                        className="form-control rounded-pill px-3 py-2 border"
                                        placeholder="Enter your email address"
                                        value={newsletterEmail}
                                        onChange={(e) => setNewsletterEmail(e.target.value)}
                                        required
                                        style={{ borderColor: '#CBD5E1', fontSize: '0.9rem' }}
                                    />
                                    <button
                                        type="submit"
                                        className="btn rounded-pill px-4 text-white fw-semibold"
                                        disabled={subscribing}
                                        style={{ backgroundColor: '#16324F', borderColor: '#16324F', whiteSpace: 'nowrap', fontSize: '0.9rem' }}
                                    >
                                        {subscribing ? 'Subscribing...' : 'Subscribe'}
                                    </button>
                                </form>
                            )}
                            <small className="text-muted d-block mt-2" style={{ fontSize: '0.74rem' }}>
                                Zero spam. Unsubscribe with one click at any time.
                            </small>
                        </div>
                    </div>
                </div>
            </section>
        </AppLayout>
    );
}
