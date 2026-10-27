import React, { useState } from 'react';
import { Link, usePage, router } from '@inertiajs/react';

export default function Header() {
    const { url, props } = usePage();
    const { auth, appSettings, wishlistCount } = props;
    const user = auth?.user;
    const [navOpen, setNavOpen] = useState(false);

    const isActive = (path) => url.startsWith(path);

    const handleLogout = (e) => {
        e.preventDefault();
        router.post('/logout');
    };

    return (
        <header className="ts-header">
            <nav className="navbar navbar-expand-xl">
                <div className="container">
                    <Link className="navbar-brand" href="/">
                        <img
                            className="brand-logo"
                            src="/images/logo.png"
                            width="116"
                            height="52"
                            alt={appSettings?.site_name || 'Trip Sathi'}
                        />
                    </Link>

                    <button
                        className="navbar-toggler"
                        type="button"
                        onClick={() => setNavOpen(!navOpen)}
                        aria-controls="mainNav"
                        aria-expanded={navOpen}
                        aria-label="Toggle navigation"
                    >
                        <span className="navbar-toggler-icon"></span>
                    </button>

                    <div className={`collapse navbar-collapse ${navOpen ? 'show' : ''}`} id="mainNav">
                        <ul className="navbar-nav mx-auto">
                            <li className="nav-item">
                                <Link className={`nav-link ${isActive('/destinations') ? 'active' : ''}`} href="/destinations">
                                    Destinations
                                </Link>
                            </li>
                            <li className="nav-item">
                                <Link className={`nav-link ${isActive('/hotels') ? 'active' : ''}`} href="/hotels">
                                    Hotels
                                </Link>
                            </li>
                            <li className="nav-item">
                                <Link className={`nav-link ${isActive('/food') ? 'active' : ''}`} href="/food">
                                    Food
                                </Link>
                            </li>
                            <li className="nav-item">
                                <Link className={`nav-link ${isActive('/events') ? 'active' : ''}`} href="/events">
                                    Events
                                </Link>
                            </li>
                            <li className="nav-item">
                                <Link className={`nav-link ${isActive('/adventures') ? 'active' : ''}`} href="/adventures">
                                    Adventures
                                </Link>
                            </li>
                            <li className="nav-item">
                                <Link className={`nav-link ${isActive('/darshan') ? 'active' : ''}`} href="/darshan">
                                    Darshan
                                </Link>
                            </li>
                            <li className="nav-item">
                                <Link className={`nav-link ${isActive('/pooja') ? 'active' : ''}`} href="/pooja">
                                    Pooja
                                </Link>
                            </li>
                            <li className="nav-item">
                                <Link className={`nav-link ${isActive('/trips') ? 'active' : ''}`} href="/trips">
                                    Trips
                                </Link>
                            </li>
                        </ul>

                        <div className="navbar-nav ts-nav-actions ms-auto align-items-xl-center gap-xl-2">
                            <Link className={`nav-link ${isActive('/contact') ? 'active' : ''}`} href="/contact">
                                Contact
                            </Link>

                            {user ? (
                                <div className="nav-item dropdown">
                                    <a
                                        className="nav-link dropdown-toggle"
                                        href="#"
                                        role="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        <i className="bi bi-person-circle me-1"></i>
                                        {user.name.split(' ')[0]}
                                    </a>
                                    <ul className="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <Link className="dropdown-item" href="/dashboard">
                                                <i className="bi bi-speedometer2 me-2"></i>Dashboard
                                            </Link>
                                        </li>
                                        <li>
                                            <Link className="dropdown-item" href="/bookings">
                                                <i className="bi bi-ticket-perforated me-2"></i>My bookings
                                            </Link>
                                        </li>
                                        <li>
                                            <Link className="dropdown-item" href="/trips?scope=mine">
                                                <i className="bi bi-map me-2"></i>My trips
                                            </Link>
                                        </li>
                                        <li>
                                            <Link className="dropdown-item" href="/wishlist">
                                                <i className="bi bi-heart me-2"></i>Wishlist
                                                {wishlistCount > 0 && (
                                                    <span className="badge bg-danger ms-2 rounded-pill">{wishlistCount}</span>
                                                )}
                                            </Link>
                                        </li>
                                        <li>
                                            <Link className="dropdown-item" href="/profile/edit">
                                                <i className="bi bi-person me-2"></i>Profile
                                            </Link>
                                        </li>
                                        <li>
                                            <Link className="dropdown-item" href="/profile/preferences">
                                                <i className="bi bi-sliders me-2"></i>Preferences
                                            </Link>
                                        </li>

                                        {user.is_admin && (
                                            <>
                                                <li><hr className="dropdown-divider" /></li>
                                                <li>
                                                    <Link className="dropdown-item text-primary fw-semibold" href="/admin/dashboard">
                                                        <i className="bi bi-shield-lock me-2"></i>Admin panel
                                                    </Link>
                                                </li>
                                            </>
                                        )}

                                        <li><hr className="dropdown-divider" /></li>
                                        <li>
                                            <button className="dropdown-item text-danger" onClick={handleLogout}>
                                                <i className="bi bi-box-arrow-right me-2"></i>Sign out
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            ) : (
                                <>
                                    <Link className="nav-link" href="/login">
                                        Sign in
                                    </Link>
                                    <Link className="btn btn-accent nav-link-cta" href="/register">
                                        Get started
                                    </Link>
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </nav>
        </header>
    );
}
