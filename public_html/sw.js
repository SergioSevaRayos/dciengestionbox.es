const CACHE_NAME = 'dcien-pwa-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

// ESTO ES LO QUE BUSCA CHROME: El evento fetch
self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request).catch(() => {
            return new Response('Estás sin conexión.');
        })
    );
});
