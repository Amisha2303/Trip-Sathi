import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import AppLayout from './AppLayout';

export default function AccountLayout({ title, heading, children }) {
    const { url, props } = usePage();
    const user = props.auth?.user;

    const isActive = (path) => {
        if (path === '/dashboard') return url === '/dashboard';
        return url.startsWith(path);
    };

    return (
        <AppLayout title={title || heading || 'Account'}>
            <div className="container py-4 py-lg-5">
                <div className="ts-account">
                    <aside className="ts-layout-side">
                        <nav className="ts-account-nav card border-0 shadow-sm p-3 rounded-4" aria-label="Account sections">
                            <div className="d-flex align-items-center gap-2 p-2 mb-3 border-bottom pb-3">
                                <div
                                    className="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center"
                                    style={{ width: 42, height: 42 }}
                                >
                                    {user?.name?.charAt(0) || 'U'}
                                </div>
                                <div className="text-truncate">
                                    <div className="fw-bold text-dark text-truncate">{user?.name}</div>
                                    <small className="text-muted text-truncate d-block">{user?.email}</small>
                                </div>
                            </div>

                            <Link
                                href="/dashboard"
                                className={`d-flex align-items-center gap-2 p-2 rounded-3 text-decoration-none mb-1 ${isActive('/dashboard') ? 'bg-primary text-white fw-semibold' : 'text-secondary'}`}
                            >
                                <i className="bi bi-speedometer2"></i> Dashboard
                            </Link>

                            <Link
                                href="/bookings"
                                className={`d-flex align-items-center gap-2 p-2 rounded-3 text-decoration-none mb-1 ${isActive('/bookings') ? 'bg-primary text-white fw-semibold' : 'text-secondary'}`}
                            >
                                <i className="bi bi-ticket-perforated"></i> My bookings
                            </Link>

                            <Link
                                href="/trips?scope=mine"
                                className={`d-flex align-items-center gap-2 p-2 rounded-3 text-decoration-none mb-1 ${url.includes('scope=mine') ? 'bg-primary text-white fw-semibold' : 'text-secondary'}`}
                            >
                                <i className="bi bi-map"></i> My trips
                            </Link>

                            <Link
                                href="/wishlist"
                                className={`d-flex align-items-center gap-2 p-2 rounded-3 text-decoration-none mb-1 ${isActive('/wishlist') ? 'bg-primary text-white fw-semibold' : 'text-secondary'}`}
                            >
                                <i className="bi bi-heart"></i> Wishlist
                            </Link>

                            <Link
                                href="/profile/edit"
                                className={`d-flex align-items-center gap-2 p-2 rounded-3 text-decoration-none mb-1 ${isActive('/profile/edit') ? 'bg-primary text-white fw-semibold' : 'text-secondary'}`}
                            >
                                <i className="bi bi-person"></i> Profile & security
                            </Link>

                            <Link
                                href="/profile/preferences"
                                className={`d-flex align-items-center gap-2 p-2 rounded-3 text-decoration-none mb-1 ${isActive('/profile/preferences') ? 'bg-primary text-white fw-semibold' : 'text-secondary'}`}
                            >
                                <i className="bi bi-sliders"></i> Preferences
                            </Link>

                            {user?.is_admin && (
                                <>
                                    <hr className="my-2" />
                                    <Link
                                        href="/admin/dashboard"
                                        className="d-flex align-items-center gap-2 p-2 rounded-3 text-decoration-none text-danger fw-semibold"
                                    >
                                        <i className="bi bi-shield-lock"></i> Admin panel
                                    </Link>
                                </>
                            )}
                        </nav>
                    </aside>

                    <div className="ts-account-main">
                        {heading && (
                            <div className="mb-4">
                                <h1 className="h3 fw-bold mb-1">{heading}</h1>
                            </div>
                        )}
                        {children}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
