import React from 'react';
import { Link } from '@inertiajs/react';

export default function Pagination({ links = [] }) {
    if (!links || links.length <= 3) return null;

    return (
        <nav aria-label="Page navigation" className="mt-4">
            <ul className="pagination justify-content-center">
                {links.map((link, idx) => {
                    const cleanLabel = link.label
                        .replace('&laquo;', '«')
                        .replace('&raquo;', '»')
                        .replace('Previous', '« Previous')
                        .replace('Next', 'Next »');

                    if (!link.url) {
                        return (
                            <li key={idx} className="page-item disabled">
                                <span className="page-link" dangerouslySetInnerHTML={{ __html: link.label }}></span>
                            </li>
                        );
                    }

                    return (
                        <li key={idx} className={`page-item ${link.active ? 'active' : ''}`}>
                            <Link
                                className="page-link"
                                href={link.url}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                                preserveScroll
                            ></Link>
                        </li>
                    );
                })}
            </ul>
        </nav>
    );
}
