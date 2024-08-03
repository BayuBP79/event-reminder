import './bootstrap';
import { indexedDBService } from './services/indexedDB-service';
import './handlers/offline-handler';

window.indexedDBService = indexedDBService;

if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').then(function(registration) {
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function(err) {
            console.log('ServiceWorker registration failed: ', err);
        });
    });
}
