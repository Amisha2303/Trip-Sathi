import React from 'react';
import { Link } from '@inertiajs/react';

export default function Breadcrumb({ items = [], className = '' }) {
    if (!items || items.length === 0) {
        return null;
    }

    return (
        <nav aria-label="breadcrumb" className={`ts-glass-breadcrumb-wrap mb-4 ${className}`}>
            <ol className="ts-glass-breadcrumb mb-0">
                <li className="ts-breadcrumb-item">
                    <Link href="/" className="ts-breadcrumb-link" title="Home">
                        <i className="bi bi-house-door-fill"></i>
                        <span className="visually-hidden">Home</span>
                    </Link>
                </li>
                {items.map((item, index) => {
                    const isLast = index === items.length - 1;
                    return (
                        <li
                            key={index}
                            className={`ts-breadcrumb-item ${isLast ? 'active' : ''}`}
                            aria-current={isLast ? 'page' : undefined}
                        >
                            <i className="bi bi-chevron-right ts-breadcrumb-sep"></i>
                            {isLast || !item.href ? (
                                <span className="ts-breadcrumb-current">{item.label}</span>
                            ) : (
                                <Link href={item.href} className="ts-breadcrumb-link">
                                    {item.label}
                                </Link>
                            )}
                        </li>
                    );
                })}
            </ol>
        </nav>
    );
}
