const CACHE_NAME = 'myworld-cache-v1';
const urlsToCache = [
  '/',
  '/assets/css/main.css',
  '/assets/js/engine.js',
  '/assets/logo-myworld.svg',
  '/assets/soundtrack.mp3'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});