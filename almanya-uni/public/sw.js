// AlmanyaUni Service Worker — KILL SWITCH v2 (deploy 2026-05-26)
// Eski v1 SW'ı kaldırır + tüm cache'leri siler + sayfayı reload eder.
// PWA tekrar açılana kadar tüm istekler doğrudan server'a gider (CSS/JS yeni).

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        // 1) Tüm cache'leri sil (eski almanyauni-v1 dahil)
        const cacheNames = await caches.keys();
        await Promise.all(cacheNames.map((name) => caches.delete(name)));

        // 2) SW kaydını sil (bir sonraki ziyarette artık SW olmayacak)
        await self.registration.unregister();

        // 3) Aktif client'ları reload et (anında temiz yükleme)
        const clients = await self.clients.matchAll({ type: 'window' });
        clients.forEach((client) => client.navigate(client.url));
    })());
});

// Fetch handler — boş, doğrudan network'e bırak
self.addEventListener('fetch', () => {});
