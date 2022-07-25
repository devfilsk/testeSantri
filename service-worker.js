var cacheName = "Santri";

//.html, .js, .css
var filesToCache = [
    "index.html",
];

self.addEventListener("install", function (event) {
    event.waitUntil(caches.open(cacheName).then((cache) => {
        return cache.addAll(filesToCache);
    }));
    console.log("service worker instalado...")
});

self.addEventListener('fetch', function (event) {
    console.log(event.request.url);
    event.respondWith(
        caches.match(event.request).then(function (response) {
            return response || fetch(event.request);
        })
    );
});

self.addEventListener('activate', function (e) {
    console.log('service worker: Ativado');
    e.waitUntil(
        caches.keys().then(function (keyList) {
            return Promise.all(keyList.map(function (key) {
                if (key !== cacheName) {
                    console.log('service worker: Removendo o cache antigo', key);
                    return caches.delete(key);
                }
            }));
        })
    );
    return self.clients.claim();
});