/* Service worker 3x30 : cache le squelette (icônes, manifeste, page hors ligne)
   et sert la page hors ligne quand une navigation dans /3x30 échoue.
   Les pages elles-mêmes ne sont jamais mises en cache : ce que l'homme écrit
   doit toujours partir au serveur. */
const CACHE = '3x30-v1';
const SHELL = [
  '/3x30/hors-ligne',
  '/pwa/manifest.webmanifest',
  '/pwa/icon-192.png',
  '/pwa/icon-512.png',
  '/pwa/icon-180.png'
];

self.addEventListener('install', function (event) {
  event.waitUntil(
    caches.open(CACHE).then(function (cache) { return cache.addAll(SHELL); }).then(function () { return self.skipWaiting(); })
  );
});

self.addEventListener('activate', function (event) {
  event.waitUntil(
    caches.keys().then(function (keys) {
      return Promise.all(keys.filter(function (k) { return k !== CACHE; }).map(function (k) { return caches.delete(k); }));
    }).then(function () { return self.clients.claim(); })
  );
});

self.addEventListener('fetch', function (event) {
  const request = event.request;
  if (request.method !== 'GET') { return; }

  const url = new URL(request.url);
  const isShell = SHELL.indexOf(url.pathname) !== -1;

  if (isShell) {
    event.respondWith(
      caches.match(request).then(function (cached) { return cached || fetch(request); })
    );
    return;
  }

  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request).catch(function () { return caches.match('/3x30/hors-ligne'); })
    );
  }
});
