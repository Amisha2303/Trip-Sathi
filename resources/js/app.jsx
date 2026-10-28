import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import 'bootstrap-icons/font/bootstrap-icons.css';

import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Trip Sathi';

createInertiaApp({
    title: (title) => title ? `${title} — ${appName}` : appName,
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
        const page = pages[`./Pages/${name}.jsx`];
        if (!page) {
            console.error(`Page ${name} not found in ./Pages/`);
        }
        return page;
    },
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    progress: {
        color: '#0b6e75',
        showSpinner: true,
    },
});

// Register PWA service worker seamlessly
if (typeof window !== 'undefined' && 'serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').then((reg) => {
            // Service worker registered smoothly
        }).catch((err) => {
            console.debug('ServiceWorker registration omitted:', err);
        });
    });
}

