// toastHelper.js
import { toast } from "@app/../node_modules/@zerodevx/svelte-toast";

export function toastError(message) {
    toast.push(message, {
        theme: {
            "--toastColor": "white",
            "--toastBackground": "#ef4444",
            "--toastBarBackground": "#dc2626",
        },
    });
}

export function toastSuccess(message) {
    toast.push(message, {
        theme: {
            "--toastColor": "white",
            "--toastBackground": "#16a34a",
            "--toastBarBackground": "#15803d",
        },
    });
}