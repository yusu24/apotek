const CACHE_NAME = 'sim-apotek-pos-v5';
const URLS_TO_PRECACHE = [
    '/cashier',
    '/pos/offline-catalog'
];

self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(async (cache) => {
            await Promise.allSettled(
                URLS_TO_PRECACHE.map((url) => cache.add(url))
            );
            // Also extract and precache build & livewire scripts referenced in /cashier
            try {
                const res = await cache.match('/cashier');
                if (res) {
                    const html = await res.clone().text();
                    const assetUrls = [];
                    const regex = /(?:src|href)=["']([^"']*(?:\/build\/|\/livewire\/)[^"']*)["']/g;
                    let match;
                    while ((match = regex.exec(html)) !== null) {
                        assetUrls.push(match[1]);
                    }
                    await Promise.allSettled(assetUrls.map((u) => cache.add(u)));
                }
            } catch (e) {}
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

    // Do not cache live ping endpoint
    if (url.pathname === '/user/ping') {
        return;
    }

    // Handle Cashier page, Offline Catalog API, Livewire JS, Build Assets (CSS/JS), Fonts, and Storage Images
    const isCacheableTarget =
        url.pathname === '/cashier' ||
        url.pathname === '/pos/offline-catalog' ||
        url.pathname.startsWith('/livewire/') ||
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/storage/') ||
        url.hostname.includes('fonts.googleapis.com') ||
        url.hostname.includes('fonts.gstatic.com') ||
        url.hostname.includes('fonts.bunny.net') ||
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
                    return caches.match(event.request, { ignoreSearch: true }).then((fallbackResponse) => {
                        if (fallbackResponse) {
                            return fallbackResponse;
                        }
                        if (url.pathname === '/cashier') {
                            return caches.match('/cashier');
                        }
                        return new Response('Offline - Data belum di-cache', {
                            status: 503,
                            headers: { 'Content-Type': 'text/plain' }
                        });
                    });
                });
            })
    );
});
