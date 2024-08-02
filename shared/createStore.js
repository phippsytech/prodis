import { writable } from 'svelte/store';
import { jspa } from '@shared/jspa.js'; // Adjust the import path to your jspa function
import { toastError } from "@shared/toastHelper";

function createStore(endpoint, actions, transformations = {}) {
    const { subscribe, set, update } = writable([]);

    const handleTransformation = async (action, data) => {
        if (transformations[action]) {
            return await transformations[action](data);
        }
        return data;
    };

    const handleError = (error, message) => {
        toastError(message);
        console.error(error);
    };

    async function load(params) {
        try {
            const result = await jspa(endpoint, actions.list, params);
            const transformedResult = await handleTransformation('load', result.result);
            set(transformedResult);
        } catch (error) {
            handleError(error, "There was an error loading items.");
        }
    }

    async function add(item) {
        try {
            const result = await jspa(endpoint, actions.add, item);
            const transformedResult = await handleTransformation('add', result.result);
            if (transformedResult.id != 0) {
                update(items => [...items, transformedResult]);
            }
        } catch (error) {
            handleError(error, "There was an error adding this item.");
        }
    }

    async function updateItem(item) {
        try {
            await jspa(endpoint, actions.update, item);
            const transformedItem = await handleTransformation('update', item);
            update(items =>
                items.map(existingItem =>
                    transformedItem.id === existingItem.id
                        ? { ...existingItem, ...transformedItem }
                        : existingItem
                )
            );
        } catch (error) {
            handleError(error, "There was an error updating this item.");
        }
    }

    async function remove(item) {
        try {
            await jspa(endpoint, actions.delete, item);
            const transformedItem = await handleTransformation('remove', item);
            update(items =>
                items.filter(existingItem => existingItem.id !== transformedItem.id)
            );
        } catch (error) {
            handleError(error, "There was an error deleting this item.");
        }
    }

    return {
        subscribe,
        load,
        add,
        updateItem,
        remove,
        set,
        update
    };
}

export default createStore;
