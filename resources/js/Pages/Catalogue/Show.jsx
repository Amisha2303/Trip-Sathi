import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import ItemCard from '../../Components/ItemCard';
import WishlistButton from '../../Components/WishlistButton';

export default function Show({
    detail,
    model,
    reviews = [],
    related = [],
    relatedLabel = 'listings',
    relatedIndexUrl,
    wished = false,
    crumbs = [],
}) {
    const formatPrice = (p) => {
        if (p === null || p === undefined) return null;
        return new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            maximumFractionDigits: 0,
        }).format(p);
    };

    return (
        <AppLayout
            title={detail.seoTitle || `${detail.name} — ${detail.eyebrow}`}
            description={detail.lede || detail.short_description || ''}
        >
            {/* Hero Section */}
            <section
                className="ts-hero ts-hero-photo ts-detail-hero"
                style={{ '--ts-hero-img': `url('${detail.mainImage}')` }}
            >
                <div className="container">
                    {crumbs.length > 0 && (
                        <nav aria-label="breadcrumb" className="mb-3">
                            <ol className="breadcrumb ts-breadcrumb-ondark mb-0">
                                {crumbs.map((crumb, idx) => {
                                    const isLast = idx === crumbs.length - 1;
                                    return (
                                        <li key={idx} className={`breadcrumb-item ${isLast ? 'active' : ''}`}>
                                            {isLast || !crumb.url ? crumb.label : <Link href={crumb.url}>{crumb.label}</Link>}
                                        </li>
                                    );
                                })}
                            </ol>
                        </nav>
                    )}

                    <div className="ts-detail-hero-top d-flex flex-wrap justify-content-between align-items-end gap-3">
                        <div>
                            <span className="ts-eyebrow" style={{ color: '#16B8AB' }}>
                                {detail.eyebrow}
                            </span>
                            <h1 className="ts-hero-title mb-2">{detail.name}</h1>

                            <div className="ts-detail-hero-meta d-flex flex-wrap align-items-center gap-3">
                                {detail.location && (
                                    <span>
                                        <i className="bi bi-geo-alt me-1"></i> {detail.location}
                                    </span>
                                )}
                                {detail.stars && (
                                    <span className="ts-stars" aria-label={`${detail.stars} star`}>
                                        {[1, 2, 3, 4, 5].map((s) => (
                                            <i
                                                key={s}
                                                className={`bi bi-star-fill ${s <= detail.stars ? 'text-warning' : 'text-white-50'}`}
                                            ></i>
                                        ))}
                                    </span>
                                )}
                                {detail.rating !== null && detail.rating !== undefined && (
                                    <span>
                                        <i className="bi bi-star-fill text-warning me-1"></i>
                                        {Number(detail.rating).toFixed(1)}
                                        <span className="opacity-75 ms-1">({detail.reviewCount || 0} reviews)</span>
                                    </span>
                                )}
                            </div>
                        </div>

                        <div className="ts-detail-hero-actions d-flex flex-wrap align-items-center gap-2">
                            <WishlistButton type={detail.wishType} id={detail.wishId} initialWished={wished} />
                            
                            {/* Share button */}
                            <button
                                type="button"
                                className="btn btn-sm btn-outline-light rounded-pill px-3 py-1.5"
                                onClick={() => {
                                    if (navigator.share) {
                                        navigator.share({ title: detail.name, url: window.location.href });
                                    } else {
                                        navigator.clipboard.writeText(window.location.href);
                                        alert('Destination link copied to clipboard!');
                                    }
                                }}
                                title="Share Destination"
                            >
                                <i className="bi bi-share me-1"></i> Share
                            </button>

                            {/* Plan Trip CTA if destination */}
                            {detail.wishType === 'destination' && (
                                <Link
                                    className="btn btn-teal btn-lg px-3.5 py-2 rounded-pill fw-bold text-white shadow"
                                    href={`/trips?action=create&destination=${detail.slug || detail.id}`}
                                    style={{ backgroundColor: '#0F9D91', borderColor: '#0F9D91' }}
                                >
                                    <i className="bi bi-send-fill me-1.5"></i> Add to Itinerary
                                </Link>
                            )}

                            {detail.price !== null && detail.price !== undefined && (
                                <div className="ts-detail-price text-white me-2">
                                    <span className="ts-price fs-4 fw-bold">
                                        {formatPrice(detail.price)}
                                        {detail.priceNote && <small className="fs-6 opacity-75 ms-1">{detail.priceNote}</small>}
                                    </span>
                                </div>
                            )}
                            {detail.cta && (
                                <Link className="btn btn-teal btn-lg px-4 rounded-pill fw-bold text-white" href={detail.cta.url} style={{ backgroundColor: '#0F9D91', borderColor: '#0F9D91' }}>
                                    {detail.cta.label}
                                    <i className={`bi ${detail.cta.icon || 'bi-arrow-right'} ms-2`}></i>
                                </Link>
                            )}
                        </div>
                    </div>
                </div>
            </section>

            {/* Gallery Section */}
            {detail.gallery && detail.gallery.length > 1 && (
                <section className="ts-section ts-section-tight py-4 bg-light">
                    <div className="container">
                        <div className="ts-gallery d-flex gap-3 overflow-auto pb-2">
                            {detail.gallery.map((img, i) => (
                                <figure key={i} className="ts-gallery-item flex-shrink-0 rounded-3 overflow-hidden shadow-sm m-0" style={{ width: 240, height: 160 }}>
                                    <img
                                        src={img.url}
                                        alt={img.caption || detail.name}
                                        className="w-100 h-100 object-fit-cover"
                                        loading="lazy"
                                    />
                                </figure>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {/* Main Content & Sidebar */}
            <section className="ts-section py-5">
                <div className="container">
                    <div className="row g-4 g-lg-5 align-items-start">
                        <div className="col-lg-8">
                            {detail.description && (
                                <div className="ts-detail-copy mb-5">
                                    <h2 className="ts-title mb-3">About this {detail.eyebrow?.toLowerCase()}</h2>
                                    <div
                                        className="lead text-secondary lh-lg"
                                        dangerouslySetInnerHTML={{ __html: detail.description }}
                                    ></div>
                                </div>
                            )}

                            {detail.facts && detail.facts.length > 0 && (
                                <div className="ts-detail-block card border-0 shadow-sm p-4 rounded-4 mb-4">
                                    <h3 className="ts-title h5 mb-3 fw-bold">Good to know</h3>
                                    <div className="row g-3">
                                        {detail.facts.map((fact, idx) => (
                                            <div key={idx} className="col-sm-6 d-flex align-items-center gap-3">
                                                <div className="rounded-3 bg-light p-2 text-primary fs-4">
                                                    <i className={`bi ${fact.icon || 'bi-info-circle'}`}></i>
                                                </div>
                                                <div>
                                                    <span className="text-muted small d-block">{fact.label}</span>
                                                    <span className="fw-semibold">{fact.value}</span>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {detail.sections && detail.sections.map((section, idx) => (
                                <div key={idx} className="ts-detail-block card border-0 shadow-sm p-4 rounded-4 mb-4">
                                    <h3 className="ts-title h5 mb-3 fw-bold">{section.title}</h3>
                                    {section.list && (
                                        <ul className="ts-check-list list-unstyled row g-2">
                                            {section.list.map((li, lidx) => (
                                                <li key={lidx} className="col-sm-6 d-flex align-items-center gap-2">
                                                    <i className="bi bi-check-circle-fill text-success"></i>
                                                    <span>{li}</span>
                                                </li>
                                            ))}
                                        </ul>
                                    )}
                                    {section.body && (
                                        <div
                                            className="ts-detail-copy text-secondary"
                                            dangerouslySetInnerHTML={{ __html: section.body }}
                                        ></div>
                                    )}
                                </div>
                            ))}

                            {/* Reviews Section */}
                            <div className="ts-detail-block card border-0 shadow-sm p-4 rounded-4 mt-5">
                                <div className="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h3 className="h4 fw-bold mb-1">Customer Reviews</h3>
                                        <div className="d-flex align-items-center gap-2">
                                            <i className="bi bi-star-fill text-warning"></i>
                                            <span className="fw-bold">{Number(detail.rating || 0).toFixed(1)}</span>
                                            <span className="text-muted">({reviews.length} verified reviews)</span>
                                        </div>
                                    </div>
                                </div>

                                {reviews.length === 0 ? (
                                    <p className="text-muted fst-italic mb-0">
                                        No reviews yet. Completed bookings are eligible to review.
                                    </p>
                                ) : (
                                    <div className="d-flex flex-column gap-3">
                                        {reviews.map((rev) => (
                                            <div key={rev.id} className="border-bottom pb-3">
                                                <div className="d-flex justify-content-between align-items-center mb-1">
                                                    <strong className="text-dark">{rev.user?.name || 'Verified Traveller'}</strong>
                                                    <div className="text-warning small">
                                                        {[...Array(rev.rating || 5)].map((_, i) => (
                                                            <i key={i} className="bi bi-star-fill me-1"></i>
                                                        ))}
                                                    </div>
                                                </div>
                                                {rev.title && <div className="fw-semibold text-secondary small mb-1">{rev.title}</div>}
                                                <p className="text-muted mb-0">{rev.comment}</p>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Sticky Aside */}
                        <div className="col-lg-4">
                            <aside className="ts-detail-aside position-sticky" style={{ top: '6rem' }}>
                                <div className="card border-0 shadow-sm rounded-4 p-4">
                                    {detail.price !== null && detail.price !== undefined && (
                                        <div className="d-flex justify-content-between align-items-baseline mb-3 pb-3 border-bottom">
                                            <div>
                                                <span className="fs-3 fw-bold text-dark">
                                                    {formatPrice(detail.price)}
                                                </span>
                                                {detail.priceNote && (
                                                    <span className="text-muted ms-1">{detail.priceNote}</span>
                                                )}
                                            </div>
                                            {detail.rating !== null && (
                                                <span className="badge bg-warning bg-opacity-10 text-dark px-2 py-1">
                                                    <i className="bi bi-star-fill text-warning me-1"></i>
                                                    {Number(detail.rating).toFixed(1)}
                                                </span>
                                            )}
                                        </div>
                                    )}

                                    {detail.cta && (
                                        <Link className="btn btn-accent btn-lg w-100 mb-2 py-3" href={detail.cta.url}>
                                            {detail.cta.label}
                                            <i className={`bi ${detail.cta.icon || 'bi-arrow-right'} ms-2`}></i>
                                        </Link>
                                    )}

                                    <ul className="list-unstyled d-flex flex-column gap-2 text-secondary small my-3 pt-2">
                                        <li className="d-flex align-items-center gap-2">
                                            <i className="bi bi-shield-check text-success"></i>
                                            <span>Guaranteed slot & room confirmation</span>
                                        </li>
                                        <li className="d-flex align-items-center gap-2">
                                            <i className="bi bi-headset text-primary"></i>
                                            <span>Real-person customer support, 7 days</span>
                                        </li>
                                        <li className="d-flex align-items-center gap-2">
                                            <i className="bi bi-arrow-counterclockwise text-info"></i>
                                            <span>Easy cancellation within 24h of booking</span>
                                        </li>
                                    </ul>

                                    <div className="bg-light p-3 rounded-3 text-center mt-2">
                                        <p className="mb-1 fw-bold small">Have questions?</p>
                                        <p className="mb-2 text-muted small">Talk it through with our support team.</p>
                                        <Link
                                            className="btn btn-soft btn-sm w-100"
                                            href={`/contact?subject=${encodeURIComponent(`${detail.eyebrow}: ${detail.name}`)}`}
                                        >
                                            <i className="bi bi-chat-dots me-1"></i> Ask a question
                                        </Link>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </section>

            {/* Related Catalogue */}
            {related.length > 0 && (
                <section className="ts-section py-5 bg-light">
                    <div className="container">
                        <div className="d-flex justify-content-between align-items-end mb-4">
                            <div>
                                <span className="ts-eyebrow">Keep exploring</span>
                                <h2 className="ts-title mb-0">More {relatedLabel}</h2>
                            </div>
                            {relatedIndexUrl && (
                                <Link className="btn btn-soft" href={relatedIndexUrl}>
                                    View all <i className="bi bi-arrow-right ms-1"></i>
                                </Link>
                            )}
                        </div>

                        <div className="row g-4">
                            {related.map((card, i) => (
                                <div key={i} className="col-md-6 col-lg-4">
                                    <ItemCard
                                        image={card.image}
                                        title={card.title}
                                        subtitle={card.subtitle}
                                        meta={card.meta}
                                        text={card.text}
                                        price={card.price}
                                        priceNote={card.priceNote}
                                        stars={card.stars}
                                        rating={card.rating}
                                        wishType={card.wishType}
                                        wishId={card.wishId}
                                        wished={card.wished}
                                        cta={card.cta}
                                        ctaUrl={card.ctaUrl}
                                    />
                                </div>
                            ))}
                        </div>
                    </div>
                </section>
            )}
        </AppLayout>
    );
}
