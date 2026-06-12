// ApplyToGerman Service Worker — v3 (2026-06-12) — PWA installability + safe caching.
//
// v1 bayat CSS/JS sorununu YAPMAZ: HTML her zaman NETWORK-FIRST (asla bayat sayfa),
// yalnızca hash'li (immutable) Vite asset'leri cache-first cache'lenir. Boş fetch
// handler yüklenebilirliği bozuyordu (v2 kill-switch) → bu gerçek handler ile çözülür.

const VERSION = 'v3';
const CACHE = 'atg-pwa-' + VERSION;
const OFFLINE_URL = '/offline.html';
const PRECACHE = [OFFLINE_URL, '/img/icons/icon-192.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE)
            .then((c) => c.addAll(PRECACHE))
            .then(() => self.skipWaiting())
            .catch(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        // Eski sürüm cache'lerini sil (v1/v2 dahil)
        const keys = await caches.keys();
        await Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)));
        await self.clients.claim();
    })());
});

self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') return;

    let url;
    try { url = new URL(req.url); } catch (e) { return; }
    if (url.origin !== self.location.origin) return;

    // 1) HTML sayfa gezinmeleri → NETWORK-FIRST. Çevrimdışıysa offline sayfası.
    //    (Sayfaları ASLA cache-first verme → bayatlama yok.)
    if (req.mode === 'navigate') {
        event.respondWith((async () => {
            try {
                return await fetch(req);
            } catch (e) {
                const cache = await caches.open(CACHE);
                return (await cache.match(OFFLINE_URL)) || new Response('Offline', { status: 503 });
            }
        })());
        return;
    }

    // 2) Hash'li Vite build asset'leri (immutable) → CACHE-FIRST.
    if (url.pathname.startsWith('/build/')) {
        event.respondWith((async () => {
            const cache = await caches.open(CACHE);
            const hit = await cache.match(req);
            if (hit) return hit;
            try {
                const res = await fetch(req);
                if (res && res.ok) cache.put(req, res.clone());
                return res;
            } catch (e) {
                return hit || Response.error();
            }
        })());
        return;
    }

    // 3) Görseller / ses / font → STALE-WHILE-REVALIDATE (hızlı + arka planda taze).
    if (/^\/(img|storage|audio|fonts)\//.test(url.pathname)) {
        event.respondWith((async () => {
            const cache = await caches.open(CACHE);
            const hit = await cache.match(req);
            const network = fetch(req).then((res) => {
                if (res && res.ok) cache.put(req, res.clone());
                return res;
            }).catch(() => hit);
            return hit || network;
        })());
        return;
    }

    // 4) Diğer (API, manifest, vb.) → doğrudan network.
});
