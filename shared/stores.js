import { writable, readable } from 'svelte/store'
import createStore from './createStore';

let hostname = window.location.hostname;
let _api_url = "https://api.ndismate.app"
if (hostname.includes("phippsy.tech")) {
    _api_url = "https://api.prodis.phippsy.phippsy.tech"
}

export const API_URL = readable(_api_url);

export const AppData = writable(localStorage.getItem("AppData") || JSON.stringify({}))

AppData.subscribe((value) => localStorage.AppData = value);


export const UserStore = writable({});

export const StafferStore = writable({});

export const ClientStore = writable(null);

export const HouseStore = writable({});

export const RolesStore = writable({});

export const ParamsStore = writable({});

export const TabStore = writable({});

export const BreadcrumbStore = writable({});

export const VersionStore = writable({});

export const PulseStore = writable({});

export const SessionIdStore = writable(null);

// export const ServiceAgreements = writable([]);

export const jwt = writable("");

// export const DarkModeStore = writable(localStorage.getItem("DarkModeStore")||JSON.stringify({}));


