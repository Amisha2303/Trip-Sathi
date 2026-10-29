import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import Logo from '@/Components/Logo';

export default function Footer() {
    const { props } = usePage();
    const { appSettings } = props;
    const year = new Date().getFullYear();

    return (
        <footer style={{
            backgroundColor: '#16324F',
            color: 'rgba(255, 255, 255, 0.78)',
            position: 'relative',
            marginTop: 'auto',
            borderTop: '1px solid rgba(255, 255, 255, 0.08)'
        }}>
            <div className="container py-5">
                <div className="row g-4 g-lg-5">
                    {/* Brand & Mission Column */}
                    <div className="col-lg-4 col-md-6">
                        <div className="mb-3">
                            <Link href="/" aria-label="Trip Sathi Home">
                                <Logo variant="horizontal" theme="dark" height={42} />
                            </Link>
                        </div>
                        <p style={{ maxWidth: '38ch', fontSize: '0.92rem', lineHeight: '1.65', color: '#94A3B8' }}>
                            {appSettings?.footer_about ||
                                'Your trusted discovery and trip-planning companion. Explore authentic Indian destinations, create personalized multi-day itineraries, and make every journey memorable.'}
                        </p>
                        <div className="d-flex gap-2.5 mt-3.5 fs-5">
                            <a href="#" className="d-inline-flex align-items-center justify-content-center text-white-50 p-2 rounded-circle border border-secondary border-opacity-25" style={{ width: '38px', height: '38px', transition: 'all 0.2s ease' }} aria-label="Facebook">
                                <i className="bi bi-facebook" style={{ fontSize: '0.95rem' }}></i>
                            </a>
                            <a href="#" className="d-inline-flex align-items-center justify-content-center text-white-50 p-2 rounded-circle border border-secondary border-opacity-25" style={{ width: '38px', height: '38px', transition: 'all 0.2s ease' }} aria-label="X (Twitter)">
                                <i className="bi bi-twitter-x" style={{ fontSize: '0.95rem' }}></i>
                            </a>
                            <a href="#" className="d-inline-flex align-items-center justify-content-center text-white-50 p-2 rounded-circle border border-secondary border-opacity-25" style={{ width: '38px', height: '38px', transition: 'all 0.2s ease' }} aria-label="Instagram">
                                <i className="bi bi-instagram" style={{ fontSize: '0.95rem' }}></i>
                            </a>
                            <a href="#" className="d-inline-flex align-items-center justify-content-center text-white-50 p-2 rounded-circle border border-secondary border-opacity-25" style={{ width: '38px', height: '38px', transition: 'all 0.2s ease' }} aria-label="LinkedIn">
                                <i className="bi bi-linkedin" style={{ fontSize: '0.95rem' }}></i>
                            </a>
                        </div>
                    </div>

                    {/* Plan & Discover Links */}
                    <div className="col-6 col-lg-2 col-md-3">
                        <div className="text-white fw-bold mb-3 text-uppercase" style={{ fontSize: '0.82rem', letterSpacing: '0.08em' }}>
                            Plan & Discover
                        </div>
                        <ul className="list-unstyled mb-0 d-flex flex-column gap-2" style={{ fontSize: '0.9rem' }}>
                            <li><Link href="/destinations" className="text-light text-opacity-75 text-decoration-none">Destinations</Link></li>
                            <li><Link href="/trips" className="text-light text-opacity-75 text-decoration-none">Explore Trips</Link></li>
                            <li><Link href="/trips?action=create" className="text-light text-opacity-75 text-decoration-none">Trip Planner</Link></li>
                            <li><Link href="/destinations#guides" className="text-light text-opacity-75 text-decoration-none">Travel Guides</Link></li>
                            <li><Link href="/wishlist" className="text-light text-opacity-75 text-decoration-none">Saved Trips</Link></li>
                            <li><Link href="/#styles" className="text-light text-opacity-75 text-decoration-none">Travel Styles</Link></li>
                        </ul>
                    </div>

                    {/* Categories Column */}
                    <div className="col-6 col-lg-2 col-md-3">
                        <div className="text-white fw-bold mb-3 text-uppercase" style={{ fontSize: '0.82rem', letterSpacing: '0.08em' }}>
                            Experience
                        </div>
                        <ul className="list-unstyled mb-0 d-flex flex-column gap-2" style={{ fontSize: '0.9rem' }}>
                            <li><Link href="/hotels" className="text-light text-opacity-75 text-decoration-none">Hotels & Stays</Link></li>
                            <li><Link href="/food" className="text-light text-opacity-75 text-decoration-none">Food & Dining</Link></li>
                            <li><Link href="/adventures" className="text-light text-opacity-75 text-decoration-none">Adventures</Link></li>
                            <li><Link href="/events" className="text-light text-opacity-75 text-decoration-none">Events & Fairs</Link></li>
                            <li><Link href="/darshan" className="text-light text-opacity-75 text-decoration-none">VIP Darshan</Link></li>
                            <li><Link href="/pooja" className="text-light text-opacity-75 text-decoration-none">Pooja Vidhi</Link></li>
                        </ul>
                    </div>

                    {/* Contact & Support Column */}
                    <div className="col-lg-4 col-md-6">
                        <div className="text-white fw-bold mb-3 text-uppercase" style={{ fontSize: '0.82rem', letterSpacing: '0.08em' }}>
                            Contact & Support
                        </div>
                        <ul className="list-unstyled mb-0 d-flex flex-column gap-2.5" style={{ fontSize: '0.9rem' }}>
                            <li className="d-flex align-items-start gap-2.5">
                                <i className="bi bi-geo-alt-fill text-teal mt-0.5" style={{ color: '#0F9D91' }}></i>
                                <span>{appSettings?.contact_address || 'Bhavnagar, Gujarat, India'}</span>
                            </li>
                            <li className="d-flex align-items-center gap-2.5">
                                <i className="bi bi-envelope-fill text-teal" style={{ color: '#0F9D91' }}></i>
                                <a href={`mailto:${appSettings?.contact_email || 'Amisha@tripsathi.com'}`} className="text-light text-opacity-75 text-decoration-none">
                                    {appSettings?.contact_email || 'Amisha@tripsathi.com'}
                                </a>
                            </li>
                            <li className="d-flex align-items-center gap-2.5">
                                <i className="bi bi-telephone-fill text-teal" style={{ color: '#0F9D91' }}></i>
                                <a href={`tel:${appSettings?.contact_phone || '+919904530641'}`} className="text-light text-opacity-75 text-decoration-none">
                                    {appSettings?.contact_phone || '+91 9904530641'}
                                </a>
                            </li>
                            <li className="d-flex align-items-center gap-2.5 text-white-50">
                                <i className="bi bi-clock-fill text-teal" style={{ color: '#0F9D91' }}></i>
                                <span>Mon – Sat, 9:30 AM – 6:30 PM IST</span>
                            </li>
                        </ul>

                        {/* Verified Platform Badge */}
                        <div className="mt-3.5 p-2.5 rounded-3 d-inline-flex align-items-center gap-2" style={{ backgroundColor: 'rgba(255, 255, 255, 0.06)', border: '1px solid rgba(255, 255, 255, 0.1)' }}>
                            <i className="bi bi-shield-check" style={{ color: '#0F9D91', fontSize: '1.2rem' }}></i>
                            <div className="small text-white-50">
                                <strong className="text-white d-block" style={{ fontSize: '0.82rem' }}>Verified Travel Discovery</strong>
                                Verified Indian itineraries & local guides
                            </div>
                        </div>
                    </div>
                </div>

                {/* Bottom Legal & Copyright Bar */}
                <div className="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-5 pt-4 border-top border-secondary border-opacity-25" style={{ fontSize: '0.86rem', color: '#94A3B8' }}>
                    <p className="mb-0">
                        &copy; {year} <strong className="text-white">{appSettings?.site_name || 'Trip Sathi'}</strong>. Discover, plan, and explore India with confidence.
                    </p>
                    <div className="d-flex align-items-center gap-3">
                        <Link href="/contact" className="text-light text-opacity-75 text-decoration-none">Privacy Policy</Link>
                        <span>&bull;</span>
                        <Link href="/contact" className="text-light text-opacity-75 text-decoration-none">Terms of Service</Link>
                        <span>&bull;</span>
                        <Link href="/contact" className="text-light text-opacity-75 text-decoration-none">FAQs & Help</Link>
                    </div>
                </div>
            </div>
        </footer>
    );
}
