import React from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function Footer() {
    const { props } = usePage();
    const { appSettings } = props;
    const year = new Date().getFullYear();

    return (
        <footer className="ts-footer" style={{ '--ts-footer-img': `url('/photos/banners/banner-footer.jpg')` }}>
            <div className="container">
                <div className="row g-4">
                    <div className="col-md-6 col-lg-4">
                        <div className="footer-brand-logo mb-3">
                            <img
                                src="/images/logo.png"
                                width="104"
                                height="46"
                                alt={appSettings?.site_name || 'Trip Sathi'}
                            />
                        </div>
                        <p style={{ maxWidth: '42ch' }}>
                            {appSettings?.footer_about ||
                                'Plan your journey and create beautiful memories — stays, food, events, adventures, darshan and rituals, all in one place.'}
                        </p>
                        <div className="d-flex gap-3 mt-3 fs-5">
                            <a href="#" aria-label="Facebook"><i className="bi bi-facebook"></i></a>
                            <a href="#" aria-label="X"><i className="bi bi-twitter-x"></i></a>
                            <a href="#" aria-label="Instagram"><i className="bi bi-instagram"></i></a>
                            <a href="#" aria-label="LinkedIn"><i className="bi bi-linkedin"></i></a>
                        </div>
                    </div>

                    <div className="col-6 col-md-3 col-lg-2">
                        <div className="footer-title">Explore</div>
                        <ul className="footer-links list-unstyled">
                            <li><Link href="/destinations">Destinations</Link></li>
                            <li><Link href="/hotels">Hotels</Link></li>
                            <li><Link href="/events">Events</Link></li>
                            <li><Link href="/adventures">Adventures</Link></li>
                            <li><Link href="/trips">Trip planner</Link></li>
                        </ul>
                    </div>

                    <div className="col-6 col-md-3 col-lg-2">
                        <div className="footer-title">Spiritual</div>
                        <ul className="footer-links list-unstyled">
                            <li><Link href="/darshan">VIP Darshan</Link></li>
                            <li><Link href="/pooja">Pooja Vidhi</Link></li>
                            <li><Link href="/contact">Temple partnerships</Link></li>
                            <li><Link href="/contact">Group enquiries</Link></li>
                        </ul>
                    </div>

                    <div className="col-md-6 col-lg-4">
                        <div className="footer-title">Contact</div>
                        <ul className="footer-links list-unstyled">
                            <li>
                                <i className="bi bi-person-badge me-2"></i>
                                {appSettings?.owner_name || 'Amisha Chauhan'}
                                <span className="text-muted ms-1">(Owner)</span>
                            </li>
                            <li>
                                <i className="bi bi-geo-alt me-2"></i>
                                {appSettings?.contact_address || 'Bhavnagar, Gujarat, India'}
                            </li>
                            <li>
                                <i className="bi bi-envelope me-2"></i>
                                {appSettings?.contact_email || 'Amisha@tripsathi.com'}
                            </li>
                            <li>
                                <i className="bi bi-telephone me-2"></i>
                                {appSettings?.contact_phone || '+91 9904530641'}
                            </li>
                            <li>
                                <i className="bi bi-clock me-2"></i>
                                Mon – Sat, 9:30 AM – 6:30 PM IST
                            </li>
                        </ul>
                    </div>
                </div>

                <div className="footer-bottom d-flex flex-wrap justify-content-between gap-2 mt-4 pt-3 border-top border-secondary">
                    <p className="mb-0">
                        &copy; {year} <strong>{appSettings?.site_name || 'Trip Sathi'}</strong>. All rights reserved.
                    </p>
                    <p className="mb-0">
                        <Link href="/contact">Privacy Policy</Link>
                        <span className="mx-2">·</span>
                        <Link href="/contact">Terms of Service</Link>
                    </p>
                </div>
            </div>
        </footer>
    );
}
