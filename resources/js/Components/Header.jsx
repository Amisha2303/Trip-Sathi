import React, { useState, useEffect } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import Logo from '@/Components/Logo';

export default function Header() {
    const { url, props } = usePage();
    const { auth, appSettings, wishlistCount } = props;
    const user = auth?.user;
    const [navOpen, setNavOpen] = useState(false);
    const [scrolled, setScrolled] = useState(false);

    const isActive = (path) => {
        if (path === '/') return url === '/';
        return url.startsWith(path);
    };

    useEffect(() => {
        const handleScroll = () => {
            setScrolled(window.scrollY > 15);
        };
        window.addEventListener('scroll', handleScroll, { passive: true });
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    // Close mobile nav on route change
    useEffect(() => {
        setNavOpen(false);
    }, [url]);

    // Prevent background scrolling when mobile drawer is open
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

    // Primary Navigation Items requested in Master Prompt
    const mainNav = [
        { href: '/destinations', label: 'Destinations', icon: 'bi-geo-alt-fill' },
        { href: '/trips', label: 'Explore Trips', icon: 'bi-compass-fill' },
        { href: '/trips?action=create', label: 'Trip Planner', icon: 'bi-map-fill' },
        { href: '/destinations#guides', label: 'Travel Guides', icon: 'bi-journal-bookmark-fill' },
    ];

    // More categories for mobile drawer
    const catalogueNav = [
        { href: '/hotels', label: 'Hotels & Stays', icon: 'bi-building-fill' },
        { href: '/food', label: 'Local Food & Dining', icon: 'bi-cup-hot-fill' },
        { href: '/events', label: 'Fairs & Festivals', icon: 'bi-calendar-event-fill' },
        { href: '/adventures', label: 'Adventures', icon: 'bi-signpost-2-fill' },
        { href: '/darshan', label: 'VIP Darshan', icon: 'bi-gem' },
        { href: '/pooja', label: 'Pooja Vidhi', icon: 'bi-flower1' },
    ];

    return (
        <>
            <header className={`ts-header ${scrolled ? 'is-scrolled shadow-sm' : ''}`} style={{
                position: 'sticky',
                top: 0,
                zIndex: 1030,
                backgroundColor: 'rgba(255, 255, 255, 0.94)',
                backdropFilter: 'saturate(180%) blur(14px)',
                borderBottom: '1px solid #E2E8F0',
                transition: 'all 0.25s ease'
            }}>
                <nav className="navbar navbar-expand-xl py-2 py-lg-2.5">
                    <div className="container">
                        {/* Mobile Bar */}
                        <div className="d-xl-none w-100 d-flex align-items-center justify-content-between">
                            <button
                                className="btn btn-sm btn-light border-0 d-inline-flex align-items-center justify-content-center p-2 rounded-circle"
                                type="button"
                                onClick={() => setNavOpen(!navOpen)}
                                aria-label="Toggle navigation"
                                aria-expanded={navOpen}
                                style={{ width: '40px', height: '40px', background: '#F8FAFC', color: '#16324F' }}
                            >
                                <i className={`bi ${navOpen ? 'bi-x-lg' : 'bi-list'} fs-5`}></i>
                            </button>

                            <Link href="/" className="d-inline-flex align-items-center" aria-label="Trip Sathi Home">
                                <Logo variant="horizontal" height={36} showTagline={false} />
                            </Link>

                            <div className="d-flex align-items-center gap-2">
                                <Link
                                    href="/wishlist"
                                    className="btn btn-sm btn-light border-0 position-relative p-2 rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style={{ width: '40px', height: '40px', background: '#F8FAFC', color: '#16324F' }}
                                    aria-label="Saved Trips"
                                >
                                    <i className="bi bi-heart fs-6"></i>
                                    {wishlistCount > 0 && (
                                        <span className="position-absolute top-0 start-100 translate-middle badge rounded-pill" style={{ backgroundColor: '#FF795D', fontSize: '0.65rem' }}>
                                            {wishlistCount}
                                        </span>
                                    )}
                                </Link>

                                <Link
                                    href={user ? (user.is_admin ? '/admin/dashboard' : '/dashboard') : '/login'}
                                    className="btn btn-sm btn-light border-0 p-2 rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style={{ width: '40px', height: '40px', background: '#F8FAFC', color: '#16324F' }}
                                    title={user ? user.name : 'Sign In'}
                                    aria-label="Account"
                                >
                                    {user ? (
                                        <span className="fw-bold" style={{
                                            width: '28px',
                                            height: '28px',
                                            borderRadius: '50%',
                                            background: 'linear-gradient(135deg, #16324F, #0F9D91)',
                                            color: '#fff',
                                            display: 'inline-flex',
                                            alignItems: 'center',
                                            justifyContent: 'center',
                                            fontSize: '0.78rem'
                                        }}>
                                            {(user.name || 'U').charAt(0).toUpperCase()}
                                        </span>
                                    ) : (
                                        <i className="bi bi-person fs-5"></i>
                                    )}
                                </Link>
                            </div>
                        </div>

                        {/* Desktop Navbar Brand */}
                        <Link className="navbar-brand d-none d-xl-flex align-items-center me-4" href="/" aria-label="Trip Sathi Home">
                            <Logo variant="horizontal" height={42} />
                        </Link>

                        {/* Desktop Navigation Links */}
                        <div className="collapse navbar-collapse d-none d-xl-flex" id="mainNav">
                            <ul className="navbar-nav mx-auto align-items-center" style={{
                                background: '#F8FAFC',
                                padding: '0.25rem 0.45rem',
                                borderRadius: '50rem',
                                border: '1px solid #E2E8F0',
                                gap: '0.2rem'
                            }}>
                                {mainNav.map((link) => {
                                    const active = isActive(link.href);
                                    return (
                                        <li className="nav-item" key={link.href}>
                                            <Link
                                                className={`nav-link px-3 py-1.5 rounded-pill fw-semibold transition-all ${active ? 'text-white' : 'text-slate-600'}`}
                                                href={link.href}
                                                style={{
                                                    fontSize: '0.92rem',
                                                    color: active ? '#FFFFFF' : '#64748B',
                                                    backgroundColor: active ? '#0F9D91' : 'transparent',
                                                    boxShadow: active ? '0 2px 8px rgba(15, 157, 145, 0.25)' : 'none',
                                                    transition: 'all 0.18s ease'
                                                }}
                                            >
                                                {link.label}
                                            </Link>
                                        </li>
                                    );
                                })}

                                {/* Saved Trips in Pill Bar */}
                                <li className="nav-item">
                                    <Link
                                        className={`nav-link px-3 py-1.5 rounded-pill fw-semibold d-inline-flex align-items-center gap-1.5 transition-all ${isActive('/wishlist') ? 'text-white' : 'text-slate-600'}`}
                                        href="/wishlist"
                                        style={{
                                            fontSize: '0.92rem',
                                            color: isActive('/wishlist') ? '#FFFFFF' : '#64748B',
                                            backgroundColor: isActive('/wishlist') ? '#0F9D91' : 'transparent',
                                            boxShadow: isActive('/wishlist') ? '0 2px 8px rgba(15, 157, 145, 0.25)' : 'none',
                                            transition: 'all 0.18s ease'
                                        }}
                                    >
                                        <i className="bi bi-heart" style={{ fontSize: '0.85rem' }}></i>
                                        <span>Saved Trips</span>
                                        {wishlistCount > 0 && (
                                            <span className="badge rounded-pill" style={{ backgroundColor: '#FF795D', color: '#fff', fontSize: '0.7rem', padding: '0.2em 0.5em' }}>
                                                {wishlistCount}
                                            </span>
                                        )}
                                    </Link>
                                </li>
                            </ul>

                            {/* Desktop Right Actions: Account + Plan My Trip CTA */}
                            <div className="navbar-nav ms-auto align-items-center gap-2.5">
                                {user ? (
                                    <div className="nav-item dropdown">
                                        <button
                                            className="btn btn-sm d-flex align-items-center gap-2 border rounded-pill px-2.5 py-1"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            style={{ background: '#FFFFFF', borderColor: '#E2E8F0', color: '#172B3A' }}
                                        >
                                            <span style={{
                                                width: '28px',
                                                height: '28px',
                                                borderRadius: '50%',
                                                background: 'linear-gradient(135deg, #16324F, #0F9D91)',
                                                color: '#fff',
                                                fontWeight: 700,
                                                fontSize: '0.8rem',
                                                display: 'inline-flex',
                                                alignItems: 'center',
                                                justifyContent: 'center'
                                            }}>
                                                {(user.name || 'U').charAt(0).toUpperCase()}
                                            </span>
                                            <span className="fw-semibold small pe-1">{user.name.split(' ')[0]}</span>
                                            <i className="bi bi-chevron-down text-muted" style={{ fontSize: '0.72rem' }}></i>
                                        </button>
                                        <ul className="dropdown-menu dropdown-menu-end shadow-lg p-2" style={{
                                            borderRadius: '1rem',
                                            border: '1px solid #E2E8F0',
                                            minWidth: '220px'
                                        }}>
                                            <li className="px-3 py-2 border-bottom border-light-subtle">
                                                <div className="fw-bold" style={{ color: '#16324F' }}>{user.name}</div>
                                                <div className="small text-muted text-truncate">{user.email}</div>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item py-2 rounded-2 mt-1" href="/dashboard" style={{ color: '#172B3A', fontSize: '0.9rem' }}>
                                                    <i className="bi bi-speedometer2 me-2" style={{ color: '#0F9D91' }}></i> Dashboard
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item py-2 rounded-2" href="/bookings" style={{ color: '#172B3A', fontSize: '0.9rem' }}>
                                                    <i className="bi bi-ticket-perforated me-2" style={{ color: '#10B981' }}></i> My Bookings
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item py-2 rounded-2" href="/trips?scope=mine" style={{ color: '#172B3A', fontSize: '0.9rem' }}>
                                                    <i className="bi bi-map me-2" style={{ color: '#0F9D91' }}></i> My Saved Trips
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item py-2 rounded-2 d-flex justify-content-between align-items-center" href="/wishlist" style={{ color: '#172B3A', fontSize: '0.9rem' }}>
                                                    <span><i className="bi bi-heart me-2" style={{ color: '#FF795D' }}></i> Wishlist</span>
                                                    {wishlistCount > 0 && (
                                                        <span className="badge rounded-pill" style={{ backgroundColor: '#FF795D', color: '#fff' }}>{wishlistCount}</span>
                                                    )}
                                                </Link>
                                            </li>
                                            <li>
                                                <Link className="dropdown-item py-2 rounded-2" href="/profile/edit" style={{ color: '#172B3A', fontSize: '0.9rem' }}>
                                                    <i className="bi bi-person-gear me-2 text-secondary"></i> Profile Settings
                                                </Link>
                                            </li>

                                            {user.is_admin && (
                                                <>
                                                    <li><hr className="dropdown-divider my-1" /></li>
                                                    <li>
                                                        <Link className="dropdown-item py-2 rounded-2 fw-bold" href="/admin/dashboard" style={{ color: '#16324F' }}>
                                                            <i className="bi bi-shield-lock-fill me-2" style={{ color: '#16324F' }}></i> Admin Panel
                                                        </Link>
                                                    </li>
                                                </>
                                            )}

                                            <li><hr className="dropdown-divider my-1" /></li>
                                            <li>
                                                <button className="dropdown-item py-2 rounded-2 text-danger" onClick={handleLogout} style={{ fontSize: '0.9rem' }}>
                                                    <i className="bi bi-box-arrow-right me-2"></i> Sign Out
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                ) : (
                                    <Link
                                        className="btn btn-sm btn-light text-dark fw-semibold px-3 py-1.5 rounded-pill border"
                                        href="/login"
                                        style={{ backgroundColor: '#FFFFFF', borderColor: '#E2E8F0', color: '#16324F' }}
                                    >
                                        Sign In
                                    </Link>
                                )}

                                {/* Prominent Plan My Trip CTA */}
                                <Link
                                    className="btn btn-sm fw-bold px-3.5 py-2 rounded-pill text-white d-inline-flex align-items-center gap-1.5 shadow-sm"
                                    href="/trips?action=create"
                                    style={{
                                        backgroundColor: '#0F9D91',
                                        borderColor: '#0F9D91',
                                        transition: 'all 0.2s ease',
                                    }}
                                >
                                    <i className="bi bi-send-fill" style={{ fontSize: '0.85rem' }}></i>
                                    <span>Plan My Trip</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>

            {/* Mobile Clean Drawer & Backdrop */}
            <div
                className={`ts-glass-drawer-backdrop ${navOpen ? 'is-visible' : ''}`}
                onClick={() => setNavOpen(false)}
                aria-hidden="true"
            />
            <div className={`ts-glass-drawer ${navOpen ? 'is-open' : ''}`} role="dialog" aria-modal="true" style={{ width: 'min(360px, 88vw)' }}>
                <div className="d-flex justify-content-between align-items-center p-3 border-bottom" style={{ borderColor: '#E2E8F0' }}>
                    <Link href="/" onClick={() => setNavOpen(false)} aria-label="Trip Sathi Home">
                        <Logo variant="horizontal" height={36} />
                    </Link>
                    <button
                        type="button"
                        className="btn-close"
                        aria-label="Close menu"
                        onClick={() => setNavOpen(false)}
                    />
                </div>

                <div className="p-3 overflow-y-auto flex-grow-1">
                    {/* Primary Plan My Trip CTA in Drawer */}
                    <div className="mb-3">
                        <Link
                            className="btn w-100 fw-bold py-2.5 rounded-3 text-white d-flex align-items-center justify-content-center gap-2 shadow-sm"
                            href="/trips?action=create"
                            onClick={() => setNavOpen(false)}
                            style={{ backgroundColor: '#0F9D91', borderColor: '#0F9D91' }}
                        >
                            <i className="bi bi-send-fill"></i>
                            <span>Plan My Trip</span>
                        </Link>
                    </div>

                    {/* User profile card or login banner */}
                    {user ? (
                        <div className="p-3 mb-3 rounded-3 d-flex align-items-center gap-3 border" style={{ background: '#F8FAFC', borderColor: '#E2E8F0' }}>
                            <div style={{
                                width: '42px',
                                height: '42px',
                                borderRadius: '50%',
                                background: 'linear-gradient(135deg, #16324F, #0F9D91)',
                                color: '#fff',
                                fontWeight: 800,
                                fontSize: '1.1rem',
                                display: 'inline-flex',
                                alignItems: 'center',
                                justifyContent: 'center'
                            }}>
                                {(user.name || 'U').charAt(0).toUpperCase()}
                            </div>
                            <div className="flex-grow-1 min-w-0">
                                <div className="fw-bold text-truncate" style={{ color: '#16324F' }}>{user.name}</div>
                                <div className="small text-muted text-truncate">{user.email}</div>
                                {user.is_admin && (
                                    <span className="badge mt-1" style={{ backgroundColor: '#16324F', color: '#fff', fontSize: '0.65rem' }}>
                                        Admin
                                    </span>
                                )}
                            </div>
                        </div>
                    ) : (
                        <div className="d-grid gap-2 mb-3">
                            <Link
                                className="btn btn-outline-dark fw-semibold py-2 rounded-3"
                                href="/login"
                                onClick={() => setNavOpen(false)}
                                style={{ borderColor: '#CBD5E1', color: '#16324F' }}
                            >
                                <i className="bi bi-box-arrow-in-right me-1.5"></i> Sign In
                            </Link>
                        </div>
                    )}

                    {/* Primary Navigation */}
                    <div className="small text-uppercase fw-bold px-2 mb-2" style={{ color: '#64748B', letterSpacing: '0.05em' }}>
                        Trip Planning & Guides
                    </div>
                    <div className="d-flex flex-column gap-1 mb-3">
                        {mainNav.map((link) => (
                            <Link
                                key={link.href}
                                href={link.href}
                                className={`d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none ${isActive(link.href) ? 'fw-bold' : ''}`}
                                onClick={() => setNavOpen(false)}
                                style={{
                                    color: isActive(link.href) ? '#0F9D91' : '#172B3A',
                                    backgroundColor: isActive(link.href) ? 'rgba(15, 157, 145, 0.08)' : 'transparent'
                                }}
                            >
                                <span style={{
                                    width: '32px',
                                    height: '32px',
                                    borderRadius: '0.5rem',
                                    backgroundColor: isActive(link.href) ? '#0F9D91' : 'rgba(22, 50, 79, 0.06)',
                                    color: isActive(link.href) ? '#fff' : '#16324F',
                                    display: 'inline-flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    fontSize: '0.95rem'
                                }}>
                                    <i className={`bi ${link.icon}`}></i>
                                </span>
                                <span>{link.label}</span>
                            </Link>
                        ))}

                        <Link
                            href="/wishlist"
                            className="d-flex align-items-center justify-content-between px-3 py-2 rounded-3 text-decoration-none"
                            onClick={() => setNavOpen(false)}
                            style={{ color: '#172B3A' }}
                        >
                            <div className="d-flex align-items-center gap-3">
                                <span style={{
                                    width: '32px',
                                    height: '32px',
                                    borderRadius: '0.5rem',
                                    backgroundColor: 'rgba(255, 121, 93, 0.1)',
                                    color: '#FF795D',
                                    display: 'inline-flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    fontSize: '0.95rem'
                                }}>
                                    <i className="bi bi-heart-fill"></i>
                                </span>
                                <span>Saved Trips</span>
                            </div>
                            {wishlistCount > 0 && (
                                <span className="badge rounded-pill" style={{ backgroundColor: '#FF795D', color: '#fff' }}>
                                    {wishlistCount}
                                </span>
                            )}
                        </Link>
                    </div>

                    {/* Explore Categories */}
                    <div className="small text-uppercase fw-bold px-2 mb-2" style={{ color: '#64748B', letterSpacing: '0.05em' }}>
                        Browse By Category
                    </div>
                    <div className="d-flex flex-column gap-1 mb-3">
                        {catalogueNav.map((cat) => (
                            <Link
                                key={cat.href}
                                href={cat.href}
                                className="d-flex align-items-center gap-3 px-3 py-1.5 rounded-3 text-decoration-none"
                                onClick={() => setNavOpen(false)}
                                style={{ color: '#334E68', fontSize: '0.92rem' }}
                            >
                                <i className={`bi ${cat.icon}`} style={{ color: '#0F9D91', width: '20px' }}></i>
                                <span>{cat.label}</span>
                            </Link>
                        ))}
                    </div>

                    {/* Account Links */}
                    {user && (
                        <>
                            <div className="small text-uppercase fw-bold px-2 mb-2" style={{ color: '#64748B', letterSpacing: '0.05em' }}>
                                My Account
                            </div>
                            <div className="d-flex flex-column gap-1 mb-3">
                                <Link
                                    href="/dashboard"
                                    className="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none"
                                    onClick={() => setNavOpen(false)}
                                    style={{ color: '#172B3A' }}
                                >
                                    <i className="bi bi-speedometer2" style={{ color: '#0F9D91', width: '20px' }}></i>
                                    <span>Dashboard</span>
                                </Link>
                                <Link
                                    href="/bookings"
                                    className="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none"
                                    onClick={() => setNavOpen(false)}
                                    style={{ color: '#172B3A' }}
                                >
                                    <i className="bi bi-ticket-perforated" style={{ color: '#10B981', width: '20px' }}></i>
                                    <span>My Bookings</span>
                                </Link>
                                <Link
                                    href="/profile/edit"
                                    className="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none"
                                    onClick={() => setNavOpen(false)}
                                    style={{ color: '#172B3A' }}
                                >
                                    <i className="bi bi-person-gear text-secondary" style={{ width: '20px' }}></i>
                                    <span>Profile Settings</span>
                                </Link>
                                {user.is_admin && (
                                    <Link
                                        href="/admin/dashboard"
                                        className="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none"
                                        onClick={() => setNavOpen(false)}
                                        style={{ color: '#16324F', fontWeight: 700 }}
                                    >
                                        <i className="bi bi-shield-lock-fill" style={{ color: '#16324F', width: '20px' }}></i>
                                        <span>Admin Panel</span>
                                    </Link>
                                )}
                            </div>
                        </>
                    )}

                    <div className="border-top pt-3 mt-2" style={{ borderColor: '#E2E8F0' }}>
                        <Link
                            href="/contact"
                            className="d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none mb-1"
                            onClick={() => setNavOpen(false)}
                            style={{ color: '#64748B' }}
                        >
                            <i className="bi bi-envelope" style={{ color: '#0F9D91', width: '20px' }}></i>
                            <span>Contact & Support</span>
                        </Link>

                        {user && (
                            <button
                                type="button"
                                className="w-100 text-start d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-danger border-0 bg-transparent"
                                onClick={handleLogout}
                            >
                                <i className="bi bi-box-arrow-right" style={{ width: '20px' }}></i>
                                <span>Sign Out</span>
                            </button>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}
