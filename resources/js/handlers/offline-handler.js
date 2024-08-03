import { indexedDBService } from '../services/indexedDB-service.js';

document.addEventListener('livewire:load', function () {
    Livewire.on('loadEvents', async function () {
        if (!navigator.onLine) {
            try {
                const events = await indexedDBService.getAll('events');
                Livewire.emit('updateUserList', events);
            } catch (error) {
                console.error('Error loading events from IndexedDB:', error);
            }
        }
    });
});


window.addEventListener('online', syncData);
window.addEventListener('offline', () => console.log('App is offline'));

async function syncData() {
    console.log('App is online, syncing data...');
    const events = await indexedDBService.getAll('events');
    // Send events to server for syncing
    fetch('/api/sync-events', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(events)
    });
}
