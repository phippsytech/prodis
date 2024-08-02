// store.js
import { writable } from 'svelte/store';
import { jspa } from "@shared/jspa.js";


export const payItemsStore = writable([]);

async function loadPayItems() {
    try {
        const result = await jspa("/Payroll", "listPayItems", {});
        payItemsStore.set(result.result);
    } catch (error) {
        console.error("Error loading pay items:", error);
        // Handle error appropriately
    }
}

// Immediately invoke the function to load data
loadPayItems();