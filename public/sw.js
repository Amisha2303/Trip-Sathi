// Trip Sathi PWA Service Worker
const CACHE_NAME = 'trip-sathi-v1';
const STATIC_ASSETS = [
  '/',
  '/manifest.json',
  '/favicon.png',
  '/images/logo.png',
  '/apple-touch-icon.png',
  '/images/icons/icon-192x192.png',
  '/images/icons/icon-512x512.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(STATIC_ASSETS).catch((err) => {
        console.warn('Trip Sathi SW: Pre-caching partial failure', err);
      });
    }).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
      );
    }).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const request = event.request;
  const url = new URL(request.url);

  // Only handle GET requests from the same origin or static CDN assets
  if (request.method !== 'GET') {
    return;
  }

  // Bypass cache for admin routes, auth, payment callbacks, and POSTs
  if (
    url.pathname.startsWith('/admin') ||
    url.pathname.startsWith('/api') ||
    url.pathname.startsWith('/logout') ||
    url.pathname.includes('/pay') ||
    url.pathname.includes('/callback')
  ) {
    return;
  }

  // For static assets (images, fonts, css, js) try cache first, then network
  if (
    url.pathname.startsWith('/build/') ||
    url.pathname.startsWith('/images/') ||
    url.pathname.startsWith('/photos/') ||
    url.pathname.endsWith('.woff2') ||
    url.pathname.endsWith('.woff') ||
    url.pathname.endsWith('.png') ||
    url.pathname.endsWith('.jpg') ||
    url.pathname.endsWith('.svg') ||
    url.pathname.endsWith('.css') ||
    url.pathname.endsWith('.js')
  ) {
    event.respondWith(
      caches.match(request).then((cachedResponse) => {
        if (cachedResponse) {
          return cachedResponse;
        }
        return fetch(request).then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseToCache = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(request, responseToCache);
            });
          }
          return networkResponse;
        });
      })
    );
    return;
  }

  // Network-first for HTML pages with fallback to cache
  event.respondWith(
    fetch(request)
      .then((networkResponse) => {
        return networkResponse;
      })
      .catch(() => {
        return caches.match(request).then((cachedResponse) => {
          if (cachedResponse) {
            return cachedResponse;
          }
          return caches.match('/');
        });
      })
  );
});
