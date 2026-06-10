const CACHE_NAME = 'meliana-jaya-pwa-v1';
const urlsToCache = [
  '/',
  '/offline.html',
  '/build/manifest.json'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        if (response) {
          return response;
        }
        return fetch(event.request).catch(
          () => caches.match('/offline.html')
        );
      }
    )
  );
});
