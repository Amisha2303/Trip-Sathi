import React from 'react';
import { Link, usePage } from '@inertiajs/react';

export default function AppleDock({ onOpenTheme }) {
    const { auth } = usePage().props;
    const currentUrl = usePage().url;
    const user = auth?.user;

    const isActive = (path) => {
        if (path === '/') return currentUrl === '/';
        return currentUrl.startsWith(path);
    };

    return (
        <aside className="ts-apple-dock-wrap" aria-label="Bottom Quick Navigation">
            <nav className="ts-apple-dock">
                {/* Home */}
                <Link
                    href="/"
                    className={`ts-dock-item ${isActive('/') ? 'is-active' : ''}`}
                    title="Home"
                    aria-label="Home"
                >
                    <i className="bi bi-house-door-fill"></i>
                </Link>

                {/* Destinations */}
                <Link
                    href="/destinations"
                    className={`ts-dock-item ${isActive('/destinations') ? 'is-active' : ''}`}
                    title="Destinations"
                    aria-label="Destinations"
                >
                    <i className="bi bi-compass-fill"></i>
                </Link>

                {/* VIP Darshan */}
                <Link
                    href="/darshan"
                    className={`ts-dock-item ${isActive('/darshan') ? 'is-active' : ''}`}
                    title="VIP Darshan"
                    aria-label="VIP Darshan"
                >
                    <i className="bi bi-gem"></i>
                </Link>

                {/* Hotels */}
                <Link
                    href="/hotels"
                    className={`ts-dock-item ${isActive('/hotels') ? 'is-active' : ''}`}
                    title="Hotels & Stays"
                    aria-label="Hotels & Stays"
                >
                    <i className="bi bi-building"></i>
                </Link>

                {/* Color & Theme Switcher Button */}
                <button
                    type="button"
                    className="ts-dock-item ts-dock-theme-btn"
                    onClick={onOpenTheme}
                    title="Appearance & Theme Colors"
                    aria-label="Appearance & Theme Colors"
                >
                    <i className="bi bi-palette-fill"></i>
                    <span className="ts-dock-dot"></span>
                </button>

                {/* User Account / Profile */}
                <Link
                    href={user ? (user.is_admin ? '/admin/dashboard' : '/dashboard') : '/login'}
                    className={`ts-dock-item ${isActive('/dashboard') || isActive('/admin') ? 'is-active' : ''}`}
                    title={user ? user.name : 'Sign In'}
                    aria-label={user ? user.name : 'Sign In'}
                >
                    {user ? (
                        <span className="ts-dock-avatar">
                            {(user.name || 'U').charAt(0).toUpperCase()}
                        </span>
                    ) : (
                        <i className="bi bi-person-circle"></i>
                    )}
                </Link>
            </nav>
        </aside>
    );
}
