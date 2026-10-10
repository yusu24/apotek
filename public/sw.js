const CACHE_NAME = 'sim-apotek-pos-v3';
const URLS_TO_PRECACHE = [
    '/cashier',
    '/pos/offline-catalog'
];

self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return Promise.allSettled(
                URLS_TO_PRECACHE.map((url) => cache.add(url))
            );
        })
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((name) => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    // Only cache GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);

    // Handle Cashier page, Offline Catalog API, Build Assets (CSS/JS), Fonts, and Storage Images
    const isCacheableTarget =
        url.pathname === '/cashier' ||
        url.pathname === '/pos/offline-catalog' ||
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/storage/') ||
        url.hostname.includes('fonts.googleapis.com') ||
        url.hostname.includes('fonts.gstatic.com') ||
        url.hostname.includes('cdn.jsdelivr.net');

    if (!isCacheableTarget) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200) {
                    const responseClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseClone);
                        // Also store clean /cashier key if query params were present
                        if (url.pathname === '/cashier') {
                            cache.put('/cashier', networkResponse.clone());
                        }
                    });
                }
                return networkResponse;
            })
            .catch(() => {
                return caches.match(event.request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    if (url.pathname === '/cashier') {
                        return caches.match('/cashier');
                    }
                    return new Response('Offline - Data belum di-cache', {
                        status: 503,
                        headers: { 'Content-Type': 'text/plain' }
                    });
                });
            })
    );
});
