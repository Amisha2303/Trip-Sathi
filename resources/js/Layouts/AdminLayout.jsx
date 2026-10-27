import React from 'react';
import { Head, Link, usePage, router } from '@inertiajs/react';
import ToastContainer from '../Components/ToastContainer';

export default function AdminLayout({ title = 'Admin', heading = 'Dashboard', children }) {
    const { auth, appSettings, flash } = usePage().props;
    const currentUrl = usePage().url;
    const currentUser = auth?.user;

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

    return (
        <div className="ts-admin-shell">
            <Head title={`${title} — ${appSettings?.site_name || 'Trip Sathi'}`} />

            <aside className="ts-admin-side">
                <div className="ts-admin-brand">
                    <span className="admin-brand-logo">
                        <img src="/images/logo.png" alt="Trip Sathi" />
                    </span>
                    <span className="ms-2 fw-bold">Admin</span>
                </div>

                <div className="ts-admin-group">Overview</div>
                <nav className="ts-admin-nav">
                    <Link
                        href="/admin"
                        className={isActive('/admin') && !currentUrl.startsWith('/admin/users') && !currentUrl.startsWith('/admin/destinations') && !currentUrl.startsWith('/admin/hotels') && !currentUrl.startsWith('/admin/events') && !currentUrl.startsWith('/admin/adventures') && !currentUrl.startsWith('/admin/darshan') && !currentUrl.startsWith('/admin/pooja') && !currentUrl.startsWith('/admin/categories') && !currentUrl.startsWith('/admin/bookings') && !currentUrl.startsWith('/admin/payments') && !currentUrl.startsWith('/admin/contact-messages') ? 'is-active' : ''}
                    >
                        <i className="bi bi-speedometer2"></i> Dashboard
                    </Link>
                </nav>

                <div className="ts-admin-group">Management</div>
                <nav className="ts-admin-nav">
                    <Link href="/admin/users" className={isActive('/admin/users') ? 'is-active' : ''}>
                        <i className="bi bi-people"></i> Users
                    </Link>
                    <Link href="/admin/destinations" className={isActive('/admin/destinations') ? 'is-active' : ''}>
                        <i className="bi bi-geo-alt"></i> Destinations
                    </Link>
                    <Link href="/admin/hotels" className={isActive('/admin/hotels') ? 'is-active' : ''}>
                        <i className="bi bi-building"></i> Hotels
                    </Link>
                    <Link href="/admin/events" className={isActive('/admin/events') ? 'is-active' : ''}>
                        <i className="bi bi-calendar-event"></i> Events
                    </Link>
                    <Link href="/admin/adventures" className={isActive('/admin/adventures') ? 'is-active' : ''}>
                        <i className="bi bi-signpost-split"></i> Adventures
                    </Link>
                    <Link href="/admin/darshan" className={isActive('/admin/darshan') ? 'is-active' : ''}>
                        <i className="bi bi-gem"></i> VIP Darshan
                    </Link>
                    <Link href="/admin/pooja" className={isActive('/admin/pooja') ? 'is-active' : ''}>
                        <i className="bi bi-flower1"></i> Pooja Vidhi
                    </Link>
                    <Link href="/admin/categories" className={isActive('/admin/categories') ? 'is-active' : ''}>
                        <i className="bi bi-tags"></i> Categories
                    </Link>
                </nav>

                <div className="ts-admin-group">Bookings</div>
                <nav className="ts-admin-nav">
                    <Link href="/admin/bookings" className={isActive('/admin/bookings') ? 'is-active' : ''}>
                        <i className="bi bi-ticket-perforated"></i> All bookings
                    </Link>
                    <Link href="/admin/payments" className={isActive('/admin/payments') ? 'is-active' : ''}>
                        <i className="bi bi-credit-card"></i> Payments
                    </Link>
                    <Link href="/admin/contact-messages" className={isActive('/admin/contact-messages') ? 'is-active' : ''}>
                        <i className="bi bi-inbox"></i> Contact inbox
                    </Link>
                </nav>

                <div className="ts-admin-group">Storefront</div>
                <nav className="ts-admin-nav">
                    <Link href="/"><i className="bi bi-box-arrow-up-right"></i> View website</Link>
                    <Link href="/account"><i className="bi bi-person"></i> My account</Link>
                </nav>
            </aside>

            <div className="ts-admin-main">
                <div className="ts-admin-bar">
                    <div className="d-flex align-items-center gap-2">
                        <strong className="text-dark">{heading}</strong>
                        <span className="badge text-bg-light d-none d-sm-inline-block">
                            {new Date().toLocaleDateString('en-IN', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' })}
                        </span>
                    </div>

                    <div className="d-flex align-items-center gap-3">
                        <span className="small text-muted d-none d-md-inline">
                            {currentUser?.name}
                        </span>

                        <button className="btn btn-sm btn-outline-secondary" type="button" onClick={handleLogout}>
                            <i className="bi bi-box-arrow-right me-1"></i> Sign out
                        </button>
                    </div>
                </div>

                <div className="ts-admin-body p-4">
                    {flash?.success && (
                        <div className="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                            <i className="bi bi-check-circle-fill"></i> {flash.success}
                        </div>
                    )}

                    {flash?.error && (
                        <div className="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                            <i className="bi bi-x-circle-fill"></i> {flash.error}
                        </div>
                    )}

                    {children}
                </div>
            </div>

            <ToastContainer />
        </div>
    );
}
