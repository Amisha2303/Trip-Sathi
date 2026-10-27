import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AccountLayout from '@/Layouts/AccountLayout';
import Pagination from '@/Components/Pagination';

export default function Wishlist({ items, type, types, counts, total }) {
    const list = items?.data || [];

    const handleRemove = (e, id) => {
        e.preventDefault();
        if (confirm('Remove this item from your wishlist?')) {
            router.delete(`/wishlist/${id}`, {
                preserveScroll: true,
            });
        }
    };

    const getItemUrl = (entry) => {
        const item = entry.wishlistable;
        if (!item) return '/contact';
        const t = entry.type?.value || entry.type;
        switch (t) {
            case 'destination':
                return `/hotels?destination=${encodeURIComponent(item.slug || '')}`;
            case 'hotel':
                return `/hotels?q=${encodeURIComponent(item.name || '')}`;
            case 'event':
                return `/events?q=${encodeURIComponent(item.name || '')}`;
            case 'adventure':
                return `/adventures?q=${encodeURIComponent(item.name || '')}`;
            case 'darshan':
                return `/darshan?q=${encodeURIComponent(item.name || '')}`;
            case 'pooja':
                return `/pooja?q=${encodeURIComponent(item.name || '')}`;
            case 'restaurant':
                return `/contact?subject=Restaurant:%20${encodeURIComponent(item.name || '')}`;
            default:
                return '/contact';
        }
    };

    const getTypeLabel = (t) => {
        const val = typeof t === 'object' ? t.value : t;
        const found = types.find(item => item.value === val);
        return found ? found.label : (val || 'Item');
    };

    return (
        <AccountLayout>
            <Head title="Wishlist — Trip Sathi" />

            <div className="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h1 className="ts-title" style={{ fontSize: '1.4rem' }}>Wishlist</h1>
                    <p className="ts-subtitle">
                        {total} saved item{total === 1 ? '' : 's'} — tap the heart anywhere to add or remove.
                    </p>
                </div>
                <Link className="btn btn-accent btn-sm" href="/destinations">
                    <i className="bi bi-compass me-1"></i> Find more
                </Link>
            </div>

            <div className="ts-tabs mb-4">
                <Link
                    href="/wishlist"
                    className={`btn btn-sm ${type === null ? 'btn-primary' : 'btn-light'} me-2`}
                >
                    All <span className="badge text-bg-light ms-1">{total}</span>
                </Link>
                {types.map((cat) => {
                    const count = counts[cat.value] || 0;
                    if (count === 0 && type !== cat.value) return null;
                    return (
                        <Link
                            key={cat.value}
                            href={`/wishlist?type=${cat.value}`}
                            className={`btn btn-sm ${type === cat.value ? 'btn-primary' : 'btn-light'} me-2`}
                        >
                            {cat.label} <span className="badge text-bg-light ms-1">{count}</span>
                        </Link>
                    );
                })}
            </div>

            {list.length === 0 ? (
                <div className="text-center py-5 my-4">
                    <div className="fs-1 text-muted mb-3">
                        <i className="bi bi-heart"></i>
                    </div>
                    <h3 className="h5 fw-bold mb-2">
                        {type ? 'Nothing saved in this category yet' : 'Your wishlist is empty'}
                    </h3>
                    <p className="text-muted mb-4">
                        Tap the heart on any destination, hotel, event, adventure, darshan or pooja to keep it here.
                    </p>
                    <Link href="/destinations" className="btn btn-primary">
                        Start exploring
                    </Link>
                </div>
            ) : (
                <>
                    <div className="row g-4">
                        {list.map((entry) => {
                            const item = entry.wishlistable;
                            const typeVal = entry.type?.value || entry.type;
                            const typeLabel = getTypeLabel(entry.type);
                            const url = getItemUrl(entry);
                            const fallbackImg = `/images/${typeVal}-placeholder.svg`;

                            return (
                                <div key={entry.id} className="col-md-6 col-xl-4">
                                    <article className="ts-card h-100">
                                        <div className="ts-card-media position-relative">
                                            <img
                                                src={entry.image_url || fallbackImg}
                                                alt={entry.label}
                                                loading="lazy"
                                                onError={(e) => {
                                                    e.target.onerror = null;
                                                    e.target.src = fallbackImg;
                                                }}
                                            />
                                            <span className="ts-card-media-tag">{typeLabel}</span>
                                            <button
                                                type="button"
                                                className="ts-wish is-active"
                                                onClick={(e) => handleRemove(e, entry.id)}
                                                aria-label="Remove from wishlist"
                                            >
                                                <i className="bi bi-heart-fill"></i>
                                            </button>
                                        </div>

                                        <div className="ts-card-body d-flex flex-column">
                                            <h3 className="ts-card-title">{entry.label}</h3>
                                            <p className="ts-card-text text-muted flex-grow-1">
                                                {item ? (
                                                    item.short_description || item.description || ''
                                                ).substring(0, 110) : 'This listing is no longer available.'}
                                            </p>

                                            <div className="ts-card-foot d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                                <span className="ts-card-meta small text-muted">
                                                    Saved {new Date(entry.created_at).toLocaleDateString()}
                                                </span>
                                                <div className="ts-card-actions d-flex gap-2">
                                                    <Link className="btn btn-sm btn-soft" href={url}>
                                                        View
                                                    </Link>
                                                    <button
                                                        className="btn btn-sm btn-outline-danger"
                                                        type="button"
                                                        onClick={(e) => handleRemove(e, entry.id)}
                                                        title="Remove from wishlist"
                                                    >
                                                        <i className="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            );
                        })}
                    </div>

                    <div className="mt-4">
                        <Pagination links={items.links} />
                    </div>
                </>
            )}
        </AccountLayout>
    );
}
