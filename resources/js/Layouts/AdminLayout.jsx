import React, { useState, useEffect } from 'react';
import { Head, Link, usePage, router } from '@inertiajs/react';
import ToastContainer from '../Components/ToastContainer';
import Breadcrumb from '../Components/Breadcrumb';
import ThemeCustomizer from '../Components/ThemeCustomizer';

export default function AdminLayout({ title = 'Admin', heading = 'Dashboard', breadcrumbs = [], children }) {
    const { auth, appSettings, flash } = usePage().props;
    const currentUrl = usePage().url;
    const currentUser = auth?.user;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [themeOpen, setThemeOpen] = useState(false);

    // Auto close sidebar on mobile navigation
    useEffect(() => {
        setSidebarOpen(false);
    }, [currentUrl]);

    // Handle body scroll locking on mobile
    useEffect(() => {
        if (sidebarOpen) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
        return () => {
            document.body.style.overflow = '';
        };
    }, [sidebarOpen]);

    const handleLogout = (e) => {
        e.preventDefault();
        router.post('/logout');
    };

    const isActive = (path) => {
        if (path === '/admin' && (currentUrl === '/admin' || currentUrl === '/admin/dashboard')) {
            return true;
        }
        return currentUrl.startsWith(path) && path !== '/admin';
    };

    // Construct default admin breadcrumbs if not explicitly passed
    const activeBreadcrumbs = breadcrumbs.length > 0 ? breadcrumbs : [
        { label: 'Admin', href: '/admin/dashboard' },
        ...(heading !== 'Dashboard' ? [{ label: heading }] : []),
    ];

    return (
        <div className="ts-admin-shell ts-glass-admin-shell">
            <Head title={`${title} — Admin — ${appSettings?.site_name || 'Trip Sathi'}`} />

            {/* Mobile Sidebar Backdrop */}
            <div
                className={`ts-admin-backdrop ${sidebarOpen ? 'is-visible' : ''}`}
                onClick={() => setSidebarOpen(false)}
                aria-hidden="true"
            />

            {/* Glass Admin Sidebar */}
            <aside className={`ts-admin-side ts-glass-admin-side ${sidebarOpen ? 'is-open' : ''}`}>
                <div className="ts-admin-brand d-flex align-items-center justify-content-between">
                    <Link href="/admin/dashboard" className="d-flex align-items-center text-decoration-none">
                        <span className="admin-brand-logo ts-glass-logo-badge">
                            <img src="/images/logo.png" alt="Trip Sathi" height="26" />
                        </span>
                        <span className="ms-2 fw-bold text-white fs-6">Admin Panel</span>
                    </Link>
                    <button
                        type="button"
                        className="btn-close btn-close-white d-lg-none"
                        onClick={() => setSidebarOpen(false)}
                        aria-label="Close sidebar"
                    />
                </div>

                <div className="ts-admin-group">Overview</div>
                <nav className="ts-admin-nav">
                    <Link
                        href="/admin/dashboard"
                        className={isActive('/admin') && !currentUrl.startsWith('/admin/users') && !currentUrl.startsWith('/admin/destinations') && !currentUrl.startsWith('/admin/hotels') && !currentUrl.startsWith('/admin/events') && !currentUrl.startsWith('/admin/adventures') && !currentUrl.startsWith('/admin/darshan') && !currentUrl.startsWith('/admin/pooja') && !currentUrl.startsWith('/admin/categories') && !currentUrl.startsWith('/admin/bookings') && !currentUrl.startsWith('/admin/payments') && !currentUrl.startsWith('/admin/contact-messages') ? 'is-active' : ''}
                    >
                        <i className="bi bi-speedometer2"></i> Dashboard
                    </Link>
                </nav>

                <div className="ts-admin-group">Management</div>
                <nav className="ts-admin-nav">
                    <Link href="/admin/users" className={isActive('/admin/users') ? 'is-active' : ''}>
                        <i className="bi bi-people-fill"></i> Users
                    </Link>
                    <Link href="/admin/destinations" className={isActive('/admin/destinations') ? 'is-active' : ''}>
                        <i className="bi bi-geo-alt-fill"></i> Destinations
                    </Link>
                    <Link href="/admin/hotels" className={isActive('/admin/hotels') ? 'is-active' : ''}>
                        <i className="bi bi-building-fill"></i> Hotels
                    </Link>
                    <Link href="/admin/events" className={isActive('/admin/events') ? 'is-active' : ''}>
                        <i className="bi bi-calendar-event-fill"></i> Events
                    </Link>
                    <Link href="/admin/adventures" className={isActive('/admin/adventures') ? 'is-active' : ''}>
                        <i className="bi bi-compass-fill"></i> Adventures
                    </Link>
                    <Link href="/admin/darshan" className={isActive('/admin/darshan') ? 'is-active' : ''}>
                        <i className="bi bi-gem"></i> VIP Darshan
                    </Link>
                    <Link href="/admin/pooja" className={isActive('/admin/pooja') ? 'is-active' : ''}>
                        <i className="bi bi-flower1"></i> Pooja Vidhi
                    </Link>
                    <Link href="/admin/categories" className={isActive('/admin/categories') ? 'is-active' : ''}>
                        <i className="bi bi-tags-fill"></i> Categories
                    </Link>
                </nav>

                <div className="ts-admin-group">Bookings & Finance</div>
                <nav className="ts-admin-nav">
                    <Link href="/admin/bookings" className={isActive('/admin/bookings') ? 'is-active' : ''}>
                        <i className="bi bi-ticket-perforated-fill"></i> All Bookings
                    </Link>
                    <Link href="/admin/payments" className={isActive('/admin/payments') ? 'is-active' : ''}>
                        <i className="bi bi-credit-card-fill"></i> Payments
                    </Link>
                    <Link href="/admin/contact-messages" className={isActive('/admin/contact-messages') ? 'is-active' : ''}>
                        <i className="bi bi-inbox-fill"></i> Contact Inbox
                    </Link>
                </nav>

                <div className="ts-admin-group">Quick Access</div>
                <nav className="ts-admin-nav">
                    <Link href="/" target="_blank" rel="noopener noreferrer">
                        <i className="bi bi-box-arrow-up-right"></i> View Website
                    </Link>
                    <Link href="/dashboard">
                        <i className="bi bi-person-circle"></i> Traveller View
                    </Link>
                </nav>
            </aside>

            {/* Admin Main Body */}
            <div className="ts-admin-main">
                <header className="ts-admin-bar ts-glass-admin-bar">
                    <div className="d-flex align-items-center gap-2 gap-sm-3 min-w-0">
                        <button
                            className="ts-admin-menu-toggle d-lg-none"
                            type="button"
                            onClick={() => setSidebarOpen(!sidebarOpen)}
                            aria-label="Toggle navigation"
                        >
                            <i className="bi bi-list"></i>
                        </button>

                        <div className="min-w-0">
                            <h1 className="h5 fw-bold mb-0 text-dark ts-admin-title text-truncate">{heading}</h1>
                            <span className="badge text-bg-light border small d-none d-md-inline-block mt-1">
                                {new Date().toLocaleDateString('en-IN', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' })}
                            </span>
                        </div>
                    </div>

                    <div className="d-flex align-items-center gap-2 flex-shrink-0">
                        <div className="d-none d-md-flex align-items-center gap-2">
                            <span className="ts-admin-avatar-mini">
                                {(currentUser?.name || 'A').charAt(0).toUpperCase()}
                            </span>
                            <span className="small fw-semibold text-dark">
                                {currentUser?.name}
                            </span>
                        </div>

                        <button
                            className="btn btn-sm btn-glass text-primary fw-medium d-inline-flex align-items-center gap-1"
                            type="button"
                            onClick={() => setThemeOpen(true)}
                            title="Personalize Theme & Colors"
                        >
                            <i className="bi bi-palette-fill"></i> <span className="d-none d-sm-inline">Theme</span>
                        </button>

                        <button className="btn btn-sm btn-glass text-danger fw-medium d-inline-flex align-items-center gap-1" type="button" onClick={handleLogout}>
                            <i className="bi bi-box-arrow-right"></i> <span className="d-none d-sm-inline">Sign Out</span>
                        </button>
                    </div>
                </header>

                <main className="ts-admin-body p-3 p-md-4">
                    {/* Glass Breadcrumb navigation */}
                    <Breadcrumb items={activeBreadcrumbs} />

                    {flash?.success && (
                        <div className="alert alert-success ts-glass-alert d-flex align-items-center gap-2 mb-4" role="alert">
                            <i className="bi bi-check-circle-fill text-success fs-5"></i>
                            <div>{flash.success}</div>
                        </div>
                    )}

                    {flash?.error && (
                        <div className="alert alert-danger ts-glass-alert d-flex align-items-center gap-2 mb-4" role="alert">
                            <i className="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                            <div>{flash.error}</div>
                        </div>
                    )}

                    {children}
                </main>
            </div>

            <ThemeCustomizer isOpen={themeOpen} onClose={() => setThemeOpen(false)} />
            <ToastContainer />
        </div>
    );
}
