// src/stores/configStore.js
import { writable } from 'svelte/store';

const configStore = writable({});

export const loadConfig = async () => {
    const response = await fetch('/app.conf');
    const data = await response.json();
    configStore.set(data);
};

export default configStore;
