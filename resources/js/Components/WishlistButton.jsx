import React, { useState } from 'react';
import { usePage, router, Link } from '@inertiajs/react';

export default function WishlistButton({ type, id, initialWished = false }) {
    const { auth } = usePage().props;
    const user = auth?.user;
    const [wished, setWished] = useState(initialWished);
    const [loading, setLoading] = useState(false);

    if (!user) {
        return (
            <Link href="/login" className="ts-wish" aria-label="Sign in to save">
                <i className="bi bi-heart"></i>
            </Link>
        );
    }

    const toggleWishlist = (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (loading) return;

        setLoading(true);
        // Optimistic toggle
        setWished(!wished);

        router.post(
            '/wishlist',
            { type, id },
            {
                preserveScroll: true,
                onFinish: () => setLoading(false),
                onError: () => setWished(wished), // revert on error
            }
        );
    };

    return (
        <button
            type="button"
            className={`ts-wish ${wished ? 'is-active' : ''}`}
            onClick={toggleWishlist}
            disabled={loading}
            aria-label={wished ? 'Remove from wishlist' : 'Add to wishlist'}
            aria-pressed={wished}
        >
            <i className={`bi ${wished ? 'bi-heart-fill' : 'bi-heart'}`}></i>
        </button>
    );
}
