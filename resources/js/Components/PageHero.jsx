import React from 'react';
import { Link } from '@inertiajs/react';

export default function PageHero({
    eyebrow,
    title,
    lede,
    image,
    stats = [],
    crumbs = [],
    accent = '#16B8AB',
}) {
    const heroStyle = image ? { '--ts-hero-img': `url('${image}')` } : {};

    return (
        <section className={`ts-hero ts-hero-compact ${image ? 'ts-hero-photo' : ''}`} style={heroStyle}>
            <div className="container">
                {crumbs.length > 0 && (
                    <nav aria-label="breadcrumb" className="mb-3">
                        <ol className="breadcrumb ts-breadcrumb-ondark mb-0">
                            {crumbs.map((crumb, idx) => {
                                const isLast = idx === crumbs.length - 1;
                                return (
                                    <li key={idx} className={`breadcrumb-item ${isLast ? 'active' : ''}`}>
                                        {isLast || !crumb.url ? (
                                            crumb.label
                                        ) : (
                                            <Link href={crumb.url}>{crumb.label}</Link>
                                        )}
                                    </li>
                                );
                            })}
                        </ol>
                    </nav>
                )}

                {eyebrow && <span className="ts-eyebrow" style={{ color: accent }}>{eyebrow}</span>}

                <h1 className="ts-hero-title text-white">{title}</h1>

                {lede && <p className="ts-hero-lede">{lede}</p>}

                {stats.length > 0 && (
                    <div className="ts-hero-stats">
                        {stats.map((stat, idx) => (
                            <div key={idx}>
                                <strong>{stat[0]}</strong>
                                <span>{stat[1]}</span>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </section>
    );
}
