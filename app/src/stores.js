import { writable, readable } from 'svelte/store'

let hostname = window.location.hostname;
let _api_url = "https://api.ndismate.app"
if (hostname.includes("phippsy.tech")) {
    _api_url = "https://api.prodis.phippsy.phippsy.tech"
}

export const API_URL = readable(_api_url);

export const AppData = writable(localStorage.getItem("AppData") || JSON.stringify({}))

AppData.subscribe((value) => localStorage.AppData = value);



export const StafferStore = writable({});

export const HouseStore = writable({});

export const RolesStore = writable({});

export const ParamsStore = writable({});