import React, { useState, useEffect } from 'react';
import { Link, usePage, router } from '@inertiajs/react';

export default function Header({ onOpenTheme }) {
    const { url, props } = usePage();
    const { auth, appSettings, wishlistCount } = props;
    const user = auth?.user;
    const [navOpen, setNavOpen] = useState(false);
    const [scrolled, setScrolled] = useState(false);

    const handleThemeClick = () => {
        if (typeof onOpenTheme === 'function') {
            onOpenTheme();
        } else {
            window.dispatchEvent(new CustomEvent('open-theme-customizer'));
        }
    };

    const isActive = (path) => url.startsWith(path);

    useEffect(() => {
        const handleScroll = () => {
            setScrolled(window.scrollY > 20);
        };
        window.addEventListener('scroll', handleScroll, { passive: true });
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    // Close mobile nav on route change
    useEffect(() => {
        setNavOpen(false);
    }, [url]);

    // Prevent scrolling when mobile drawer is open
    useEffect(() => {
        if (navOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
        return () => {
            document.body.style.overflow = '';
        };
    }, [navOpen]);

    const handleLogout = (e) => {
        e.preventDefault();
        router.post('/logout');
    };

    const navLinks = [
        { href: '/destinations', label: 'Destinations', icon: 'bi-geo-alt-fill' },
        { href: '/hotels', label: 'Hotels', icon: 'bi-building-fill' },
        { href: '/food', label: 'Food & Dining', icon: 'bi-cup-hot-fill' },
        { href: '/events', label: 'Events', icon: 'bi-calendar-event-fill' },
        { href: '/adventures', label: 'Adventures', icon: 'bi-compass-fill' },
        { href: '/darshan', label: 'VIP Darshan', icon: 'bi-gem' },
        { href: '/pooja', label: 'Pooja Vidhi', icon: 'bi-flower1' },
        { href: '/trips', label: 'Trip Planner', icon: 'bi-map-fill' },
    ];

    return (
        <>
            <header className={`ts-header ts-glass-header ${scrolled ? 'is-scrolled' : ''}`}>
                <nav className="navbar navbar-expand-xl py-2">
                    <div className="container">
                        {/* Apple Mobile Capsule Top Bar (Image 2 LUXORA Inspiration) */}
                        <div className="d-xl-none w-100">
                            <div className="ts-apple-mobile-capsule">
                                <button
                                    className={`ts-hamburger-btn ${navOpen ? 'is-active' : ''}`}
                                    type="button"
                                    onClick={() => setNavOpen(!navOpen)}
                                    aria-label="Toggle navigation"
                                    aria-expanded={navOpen}
                                >
                                    <span className="hamburger-box">
                                        <span className="hamburger-inner"></span>
                                    </span>
                                </button>

                                <Link className="ts-capsule-brand" href="/">
                                    <img
                                        className="brand-logo"
                                        src="/images/logo.png"
                                        alt={appSettings?.site_name || 'Trip Sathi'}
                                    />
                                </Link>

                                <div className="d-flex align-items-center gap-1">
                                    <button
                                        type="button"
                                        className="ts-capsule-btn"
                                        onClick={handleThemeClick}
                                        title="Personalize Theme & Colors"
                                        aria-label="Personalize Theme & Colors"
                                    >
                                        <i className="bi bi-palette-fill"></i>
                                    </button>

                                    <Link
                                        href={user ? (user.is_admin ? '/admin/dashboard' : '/dashboard') : '/login'}
                                        className="ts-capsule-btn"
                                        title={user ? user.name : 'Sign In'}
                                    >
                                        {user ? (
                                            <span className="ts-dock-avatar" style={{ width: '26px', height: '26px', fontSize: '0.75rem' }}>
                                                {(user.name || 'U').charAt(0).toUpperCase()}
                                            </span>
                                        ) : (
                                            <i className="bi bi-person-circle"></i>
                                        )}
                                        {wishlistCount > 0 && (
                                            <span className="ts-capsule-badge">{wishlistCount}</span>
                                        )}
                                    </Link>
                                </div>
                            </div>
                        </div>

                        {/* Desktop Navbar Brand */}
                        <Link className="navbar-brand d-none d-xl-flex align-items-center" href="/">
                            <img
                                className="brand-logo ts-glass-logo"
                                src="/images/logo.png"
                                width="128"
                                height="46"
                                alt={appSettings?.site_name || 'Trip Sathi'}
                            />
                        </Link>

                        {/* Desktop Nav */}
                        <div className="collapse navbar-collapse d-none d-xl-flex" id="mainNav">
                            <ul className="navbar-nav mx-auto ts-nav-pill-group">
                                {navLinks.map((link) => (
                                    <li className="nav-item" key={link.href}>
                                        <Link
                                            className={`nav-link ts-nav-pill ${isActive(link.href) ? 'active' : ''}`}
                                            href={link.href}
                                        >
                                            {link.label}
                                        </Link>
                                    </li>
                                ))}
                            </ul>

                            <div className="navbar-nav ts-nav-actions ms-auto align-items-center gap-2">
                                <Link
                                    className={`nav-link ts-nav-pill ${isActive('/contact') ? 'active' : ''}`}
                                    href="/contact"
                                >
                                    Contact
                                </Link>

                                {/* Theme Customizer Button for Desktop */}
                                <button
                                    type="button"
                                    className="btn btn-sm btn-glass rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 text-dark"
                                    onClick={handleThemeClick}
                                    title="Personalize Theme & Colors"
                                >
                                    <i className="bi bi-palette-fill text-primary"></i>
                                    <span className="small fw-semibold">Theme</span>
                                </button>

                                {user ? (
                                    <div className="nav-item dropdown">
                                        <button
                                            className="ts-user-pill-btn dropdown-toggle d-flex align-items-center gap-2"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                        >
                                            <span className="ts-user-avatar-mini">
                                                {(user.name || 'U').charAt(0).toUpperCase()}
                                            </span>
                                            <span className="ts-user-name fw-semibold">{user.name.split(' ')[0]}</span>
                                            {wishlistCount > 0 && (
                                                <span className="badge bg-danger rounded-pill px-1.5 py-0.5" style={{ fontSize: '0.65rem' }}>
                                                    {wishlistCount}
                                                </span>
                                            )}
                                        </button>
                                        <ul className="dropdown-menu dropdown-menu-end ts-glass-dropdown shadow-lg animate slideIn">
                                            <li className="px-3 py-2 border-bottom border-light-subtle">
                                                <div className="fw-bold text-dark">{user.name}</div>
                                                <div className="small text-muted text-truncate" style={{ maxWidth: '180px' }}>{user.email}</div>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item" href="/dashboard">
                                                    <i className="bi bi-speedometer2 me-2 text-primary"></i> Dashboard
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item" href="/bookings">
                                                    <i className="bi bi-ticket-perforated me-2 text-success"></i> My Bookings
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item" href="/trips?scope=mine">
                                                    <i className="bi bi-map me-2 text-info"></i> My Trips
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item d-flex justify-content-between align-items-center" href="/wishlist">
                                                    <span><i className="bi bi-heart me-2 text-danger"></i> Wishlist</span>
                                                    {wishlistCount > 0 && (
                                                        <span className="badge bg-danger rounded-pill">{wishlistCount}</span>
                                                    )}
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item" href="/profile/edit">
                                                    <i className="bi bi-person me-2 text-secondary"></i> Profile
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item" href="/profile/preferences">
                                                    <i className="bi bi-sliders me-2 text-secondary"></i> Preferences
                                                </Link>
                                            </li>

                                            {user.is_admin && (
                                                <>
                                                    <li><hr className="dropdown-divider my-1" /></li>
                                                    <li>
                                                        <Link className="dropdown-item text-primary fw-bold" href="/admin/dashboard">
                                                            <i className="bi bi-shield-lock-fill me-2 text-primary"></i> Admin Panel
                                                        </Link>
                                                    </li>
                                                </>
                                            )}

                                            <li><hr className="dropdown-divider my-1" /></li>
                                            <li>
                                                <button className="dropdown-item text-danger" onClick={handleLogout}>
                                                    <i className="bi bi-box-arrow-right me-2"></i> Sign Out
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                ) : (
                                    <div className="d-flex align-items-center gap-2">
                                        <Link className="btn btn-sm btn-glass" href="/login">
                                            Sign In
                                        </Link>
                                        <Link className="btn btn-sm btn-gradient-glow text-white" href="/register">
                                            Get Started
                                        </Link>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </nav>
            </header>

            {/* Mobile Glass Offcanvas Drawer */}
            <div
                className={`ts-glass-drawer-backdrop ${navOpen ? 'is-visible' : ''}`}
                onClick={() => setNavOpen(false)}
                aria-hidden="true"
            />
            <div className={`ts-glass-drawer ${navOpen ? 'is-open' : ''}`} role="dialog" aria-modal="true">
                <div className="ts-drawer-header d-flex justify-content-between align-items-center p-3 border-bottom border-light-subtle">
                    <Link href="/" onClick={() => setNavOpen(false)}>
                        <img src="/images/logo.png" alt="Trip Sathi" height="38" />
                    </Link>
                    <button
                        type="button"
                        className="btn-close btn-close-white"
                        aria-label="Close menu"
                        onClick={() => setNavOpen(false)}
                    />
                </div>

                <div className="ts-drawer-body p-3 overflow-y-auto">
                    {/* User profile card or login banner */}
                    {user ? (
                        <div className="ts-drawer-user-card p-3 mb-3 rounded-3 d-flex align-items-center gap-3">
                            <div className="ts-user-avatar-large">
                                {(user.name || 'U').charAt(0).toUpperCase()}
                            </div>
                            <div className="flex-grow-1 min-w-0">
                                <div className="fw-bold text-white text-truncate">{user.name}</div>
                                <div className="small text-white-50 text-truncate">{user.email}</div>
                                {user.is_admin && (
                                    <span className="badge bg-warning text-dark mt-1" style={{ fontSize: '0.65rem' }}>
                                        Admin
                                    </span>
                                )}
                            </div>
                        </div>
                    ) : (
                        <div className="d-grid gap-2 mb-4">
                            <Link className="btn btn-gradient-glow text-white" href="/register" onClick={() => setNavOpen(false)}>
                                <i className="bi bi-person-plus me-1"></i> Create Account
                            </Link>
                            <Link className="btn btn-glass text-white" href="/login" onClick={() => setNavOpen(false)}>
                                <i className="bi bi-box-arrow-in-right me-1"></i> Sign In
                            </Link>
                        </div>
                    )}

                    <div className="ts-drawer-heading small text-uppercase fw-bold text-white-50 px-2 mb-2">
                        Explore Catalogue
                    </div>
                    <div className="ts-drawer-links d-flex flex-column gap-1 mb-4">
                        {navLinks.map((link) => (
                            <Link
                                key={link.href}
                                href={link.href}
                                className={`ts-drawer-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 ${isActive(link.href) ? 'is-active' : ''}`}
                                onClick={() => setNavOpen(false)}
                            >
                                <span className="ts-drawer-icon">
                                    <i className={`bi ${link.icon}`}></i>
                                </span>
                                <span className="fw-medium text-white">{link.label}</span>
                            </Link>
                        ))}
                    </div>

                    {user && (
                        <>
                            <div className="ts-drawer-heading small text-uppercase fw-bold text-white-50 px-2 mb-2">
                                My Account
                            </div>
                            <div className="ts-drawer-links d-flex flex-column gap-1 mb-3">
                                <Link
                                    href="/dashboard"
                                    className="ts-drawer-link d-flex align-items-center gap-3 px-3 py-2 rounded-3"
                                    onClick={() => setNavOpen(false)}
                                >
                                    <span className="ts-drawer-icon text-primary"><i className="bi bi-speedometer2"></i></span>
                                    <span className="text-white">Dashboard</span>
                                </Link>
                                <Link
                                    href="/bookings"
                                    className="ts-drawer-link d-flex align-items-center gap-3 px-3 py-2 rounded-3"
                                    onClick={() => setNavOpen(false)}
                                >
                                    <span className="ts-drawer-icon text-success"><i className="bi bi-ticket-perforated"></i></span>
                                    <span className="text-white">My Bookings</span>
                                </Link>
                                <Link
                                    href="/wishlist"
                                    className="ts-drawer-link d-flex align-items-center justify-content-between px-3 py-2 rounded-3"
                                    onClick={() => setNavOpen(false)}
                                >
                                    <div className="d-flex align-items-center gap-3">
                                        <span className="ts-drawer-icon text-danger"><i className="bi bi-heart"></i></span>
                                        <span className="text-white">Wishlist</span>
                                    </div>
                                    {wishlistCount > 0 && (
                                        <span className="badge bg-danger rounded-pill">{wishlistCount}</span>
                                    )}
                                </Link>
                                <Link
                                    href="/profile/edit"
                                    className="ts-drawer-link d-flex align-items-center gap-3 px-3 py-2 rounded-3"
                                    onClick={() => setNavOpen(false)}
                                >
                                    <span className="ts-drawer-icon text-secondary"><i className="bi bi-person"></i></span>
                                    <span className="text-white">Profile Settings</span>
                                </Link>

                                {user.is_admin && (
                                    <Link
                                        href="/admin/dashboard"
                                        className="ts-drawer-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 bg-primary bg-opacity-25"
                                        onClick={() => setNavOpen(false)}
                                    >
                                        <span className="ts-drawer-icon text-warning"><i className="bi bi-shield-lock"></i></span>
                                        <span className="text-warning fw-bold">Admin Panel</span>
                                    </Link>
                                )}
                            </div>
                        </>
                    )}

                    <div className="border-top border-light-subtle pt-3 mt-3">
                        <Link
                            href="/contact"
                            className="ts-drawer-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-white mb-2"
                            onClick={() => setNavOpen(false)}
                        >
                            <span className="ts-drawer-icon text-info"><i className="bi bi-envelope"></i></span>
                            <span>Contact & Support</span>
                        </Link>

                        {user && (
                            <button
                                type="button"
                                className="ts-drawer-link w-100 text-start d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-danger border-0 bg-transparent"
                                onClick={handleLogout}
                            >
                                <span className="ts-drawer-icon"><i className="bi bi-box-arrow-right"></i></span>
                                <span>Sign Out</span>
                            </button>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
