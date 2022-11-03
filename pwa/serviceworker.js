// Perform install steps
let CACHE_NAME = 'my-cache';
let urlsToCache = [
  "/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/pwa/index.html"
];

self.addEventListener('install', function(event) {
  // Perform install steps
  event.waitUntil(
    caches.open(CACHE_NAME)
    .then(function(cache) {
      console.log('Opened cache');
      return cache.addAll(urlsToCache);
    })
  );
});

self.addEventListener('fetch', (event) => {

  event.respondWith(
    caches.match(event.request).then((response) => {

      return response || fetch(event.request);

    })
  );
});