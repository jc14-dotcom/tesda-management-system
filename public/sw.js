/**
 * Alcatt System — Service Worker
 *
 * Strategy: cache-first for all Vite build assets and static public assets.
 * HTML responses are intentionally excluded (they must stay dynamic/fresh).
 *
 * Cache versioning: bump CACHE_VERSION whenever a breaking asset change is deployed
 * so old caches are cleanly evicted on the next page load.
 */

const CACHE_VERSION = 'alcatt-v1';

// ─── Install ──────────────────────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    // Skip waiting so this SW activates immediately (no waiting for old clients).
    self.skipWaiting();
});

// ─── Activate ─────────────────────────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches
            .keys()
            .then((names) =>
                Promise.all(
                    names
                        .filter((name) => name !== CACHE_VERSION)
                        .map((name) => caches.delete(name)),
                ),
            )
            .then(() => self.clients.claim()),
    );
});

// ─── Fetch ────────────────────────────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Only intercept same-origin GET requests.
    if (request.method !== 'GET') return;

    const url = new URL(request.url);

    // Only cache Vite build assets and public static assets.
    // Never cache HTML pages — they must always be fresh from the server.
    const isStaticAsset =
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/assets/');

    if (!isStaticAsset) return;

    // Cache-first: serve from cache, fall back to network and cache the response.
    event.respondWith(
        caches.open(CACHE_VERSION).then((cache) =>
            cache.match(request).then((cached) => {
                if (cached) return cached;

                return fetch(request).then((response) => {
                    // Only cache successful responses.
                    if (response.ok) {
                        cache.put(request, response.clone());
                    }
                    return response;
                });
            }),
        ),
    );
});
