const dbName = "EventReminders";
const dbVersion = 1;

let db;

const dbPromise = indexedDB.open(dbName, dbVersion);

dbPromise.onerror = (event) => console.error('IndexedDB error:', event.target.error);

dbPromise.onsuccess = (event) => {
    db = event.target.result;
};

dbPromise.onupgradeneeded = (event) => {
    db = event.target.result;
    db.createObjectStore("events", { keyPath: "id" });
};

export const indexedDBService = {
    async add(storeName, data) {
        const tx = db.transaction(storeName, 'readwrite');
        const store = tx.objectStore(storeName);
        await store.add(data);
    },

    async get(storeName, id) {
        const tx = db.transaction(storeName, 'readonly');
        const store = tx.objectStore(storeName);
        return await store.get(id);
    },

    async getAll(storeName) {
        const tx = db.transaction(storeName, 'readonly');
        const store = tx.objectStore(storeName);
        return await store.getAll();
    },

    async update(storeName, data) {
        const tx = db.transaction(storeName, 'readwrite');
        const store = tx.objectStore(storeName);
        await store.put(data);
    },

    async delete(storeName, id) {
        const tx = db.transaction(storeName, 'readwrite');
        const store = tx.objectStore(storeName);
        await store.delete(id);
    }
};
