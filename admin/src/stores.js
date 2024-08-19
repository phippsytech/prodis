import { writable, readable } from 'svelte/store'

export const AppData = writable(localStorage.getItem("AppData") || JSON.stringify({}))

AppData.subscribe((value) => localStorage.AppData = value);

export const RolesStore = writable({});

export const ParamsStore = writable({});
