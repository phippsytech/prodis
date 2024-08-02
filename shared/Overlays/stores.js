import { writable } from 'svelte/store';

export const SpinnerStore = writable({});
export const ModalStore = writable({});
export const SlideOverStore = writable({});
export const NotificationStore = writable({});
export const ContextMenuStore = writable({});

// This is for the console
export const consoleVisible = writable(false);
export function openConsole() {
    consoleVisible.set(true);
}
export function closeConsole() {
    consoleVisible.set(false);
}