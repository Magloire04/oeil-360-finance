/* Service worker — Oeil360 Finance (PWA)
 * Stratégie prudente : aucune page authentifiée ni réponse /api ou /auth n'est mise en cache
 * (minimisation APDP). On met seulement en cache la coquille statique + une page hors-ligne. */

const CACHE = 'oeil360-v1';
const PRECACHE = [
    '/offline.html',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE).then((cache) => cache.addAll(PRECACHE)).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Ne toucher qu'aux GET de même origine.
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);
    if (url.origin !== self.location.origin) {
        return;
    }

    // Jamais de cache pour l'API ni le flux d'authentification (données perso / redirections).
    if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/auth/')) {
        return;
    }

    // Navigations (pages) : réseau d'abord, repli hors-ligne. La réponse HTML n'est PAS mise en cache.
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match('/offline.html'))
        );
        return;
    }

    // Assets statiques : stale-while-revalidate.
    if (/^\/(css|js|images|icons)\//.test(url.pathname)) {
        event.respondWith(
            caches.open(CACHE).then(async (cache) => {
                const cached = await cache.match(request);
                const network = fetch(request)
                    .then((response) => {
                        if (response && response.status === 200) {
                            cache.put(request, response.clone());
                        }
                        return response;
                    })
                    .catch(() => cached);
                return cached || network;
            })
        );
    }
});
