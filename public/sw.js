const CACHE_NAME = 'event-reminder-cache-v1';
const urlsToCache = [
    '/',
    '/css/app.css',
    '/js/app.js',
];

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
        .then(function(cache) {
            return cache.addAll(urlsToCache);
        })
    );
});

self.addEventListener('fetch', function(event) {
event.respondWith(
    caches.match(event.request)
    .then(function(response) {
        if (response) {
        return response;
        }
        return fetch(event.request);
    })
);
});


self.addEventListener('sync', function(event) {
    if (event.tag === 'sync-data') {
    event.waitUntil(syncData());
    }
});

function syncData() {
    return fetch('/api/sync', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        // Add data to sync
    }),
    }).then(function(response) {
    if (response.ok) {
        return response.json();
    }
    throw new Error('Sync failed');
    }).then(function(data) {
    console.log('Sync successful', data);
    }).catch(function(error) {
    console.error('Sync failed:', error);
    });
}
