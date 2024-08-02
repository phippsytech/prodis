import { writable } from 'svelte/store';

export const SpinnerStore = writable({});
export const ModalStore = writable({});
export const SlideOverStore = writable({});
export const NotificationStore = writable({});
export const ContextMenuStore = writable({});
export const TooltipStore = writable({});

// This is for the console
export const consoleVisible = writable(false);
export const consoleLogs = writable([]);
export function openConsole() {
    consoleVisible.set(true);
}
export function closeConsole() {
    consoleVisible.set(false);
}
export function logMessage(type, message) {
    consoleLogs.update(logs => {
        logs.push({ type, message });
        return logs;
    });
}