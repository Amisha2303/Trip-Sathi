import React from 'react';
import { Link } from '@inertiajs/react';
import WishlistButton from './WishlistButton';

export default function ItemCard({
    image,
    title,
    subtitle,
    meta = {},
    text,
    price,
    priceNote,
    stars,
    rating,
    ratingNote,
    badge,
    wishType,
    wishId,
    wished = false,
    cta = 'Explore',
    ctaUrl = '#',
    extra = null,
}) {
    const placeholder = wishType ? `/images/${wishType}-placeholder.svg` : '/images/placeholder.svg';

    const formatPrice = (p) => {
        if (p === null || p === undefined) return null;
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            maximumFractionDigits: 0,
        }).format(p);
    };

    return (
        <article className="ts-card h-100">
            <div className="ts-card-media">
                <img
                    src={image || placeholder}
                    alt={title}
                    loading="lazy"
                    onError={(e) => {
                        e.target.onerror = null;
                        e.target.src = placeholder;
                    }}
                />

                {badge && <span className="ts-card-media-tag">{badge}</span>}

                {wishType && wishId && (
                    <WishlistButton type={wishType} id={wishId} initialWished={wished} />
                )}
            </div>

            <div className="ts-card-body">
                {Object.keys(meta).length > 0 && (
                    <div className="ts-card-meta mb-1">
                        {Object.entries(meta).map(([icon, label], idx) => (
                            <span key={idx} className="me-2">
                                <i className={`bi ${icon} me-1`}></i>
                                {label}
                            </span>
                        ))}
                    </div>
                )}

                <h3 className="ts-card-title">{title}</h3>

                {subtitle && (
                    <p className="ts-card-meta mb-1">
                        <i className="bi bi-geo-alt me-1"></i>
                        {subtitle}
                    </p>
                )}

                {text && <p className="ts-card-text">{text}</p>}

                {stars && (
                    <div className="ts-stars mb-1" aria-label={`${stars} star`}>
                        {[1, 2, 3, 4, 5].map((star) => (
                            <i
                                key={star}
                                className={`bi bi-star-fill ${star <= stars ? 'text-warning' : 'text-muted opacity-25'}`}
                            ></i>
                        ))}
                    </div>
                )}

                <div className="ts-card-foot mt-auto pt-3">
                    <div>
                        {price !== null && price !== undefined && (
                            <div className="ts-price">
                                {formatPrice(price)}
                                {priceNote && <small className="text-muted ms-1 fs-6">{priceNote}</small>}
                            </div>
                        )}

                        {rating !== null && rating !== undefined && (
                            <div className="ts-card-meta mt-1">
                                <i className="bi bi-star-fill text-warning me-1"></i>
                                <span>
                                    {Number(rating).toFixed(1)}
                                    {ratingNote && <span className="text-muted ms-1">({ratingNote})</span>}
                                </span>
                            </div>
                        )}
                    </div>

                    <div className="ts-card-actions ms-auto">
                        {extra}
                        <Link className="btn btn-sm btn-soft" href={ctaUrl}>
                            {cta}
                        </Link>
                    </div>
                </div>
            </div>
        </article>
    );
}
