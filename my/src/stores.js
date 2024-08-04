import { writable, readable } from 'svelte/store'

export const AppData = writable(localStorage.getItem("AppData") || JSON.stringify({}))

AppData.subscribe((value) => localStorage.AppData = value);



export const StafferStore = writable({});

export const HouseStore = writable({});

export const RolesStore = writable({});
