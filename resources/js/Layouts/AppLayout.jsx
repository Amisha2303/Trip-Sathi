import React, { useEffect, useState } from 'react';
import { Head, usePage } from '@inertiajs/react';
import Header from '../Components/Header';
import Footer from '../Components/Footer';
import ToastContainer from '../Components/ToastContainer';

export default function AppLayout({ title, description, children }) {
    const { appSettings } = usePage().props;
    const [showBackTop, setShowBackTop] = useState(false);

    useEffect(() => {
        const handleScroll = () => {
            setShowBackTop(window.scrollY > 400);
        };
        window.addEventListener('scroll', handleScroll, { passive: true });
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    const scrollToTop = () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    const siteTitle = appSettings?.site_name || 'Trip Sathi';
    const metaDesc = description || appSettings?.meta_description || 'Trip Sathi — plan trips, book hotels, events, adventures, VIP Darshan and Pooja Vidhi, all in one place.';

    return (
        <div className="d-flex flex-column min-vh-100">
            <Head>
                <title>{title ? `${title} — ${siteTitle}` : siteTitle}</title>
                <meta name="description" content={metaDesc} />
            </Head>

            <Header />
            <ToastContainer />

            <main className="flex-grow-1">
                {children}
            </main>

            <Footer />

            <button
                type="button"
                className={`ts-back-top ${showBackTop ? 'is-visible' : ''}`}
                onClick={scrollToTop}
                aria-label="Back to top"
            >
                <i className="bi bi-arrow-up"></i>
            </button>
        </div>
    );
}
