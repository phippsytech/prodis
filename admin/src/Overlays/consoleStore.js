import { writable } from 'svelte/store';

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
        logs.push({ type: type, message: message });
        return logs;
    });
}

// Override console methods to capture messages from the very beginning
const originalConsole = {
    log: console.log,
    error: console.error,
    warn: console.warn,
    info: console.info,
};

function appendMessage(type, args) {
    const message = Array.from(args).join(' ');
    logMessage(type, message);
}



console.log = function (...args) {
    appendMessage('log', args);
    originalConsole.log.apply(console, args);
};

console.error = function (...args) {
    appendMessage('error', args);
    originalConsole.error.apply(console, args);
};

console.warn = function (...args) {
    appendMessage('warn', args);
    originalConsole.warn.apply(console, args);
};

console.info = function (...args) {
    appendMessage('info', args);
    originalConsole.info.apply(console, args);
};


// Catch browser errors
window.onerror = function (message, source, lineno, colno, error) {
    const errorMessage = `${message} at ${source}:${lineno}:${colno}`;
    appendMessage('error', [errorMessage]);
    return false; // Let the default handler run as well
};

// Catch unhandled promise rejections
window.onunhandledrejection = function (event) {
    appendMessage('error', [`Unhandled promise rejection: ${event.reason}`]);
};
