import { writable, readable } from 'svelte/store'


const _api_url = import.meta.env.VITE_API_URL;

export const API_URL = readable(_api_url);

export const AppData =writable(localStorage.getItem("AppData")||JSON.stringify({}))

AppData.subscribe((value) => localStorage.AppData = value );



export const StafferStore = writable({});

export const HouseStore = writable({});

export const RolesStore = writable({});
