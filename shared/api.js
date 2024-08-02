import { jspa } from "./jspa.js"
import { StafferStore, ClientStore } from "./stores.js"


// stores
let client;
let staffer;

ClientStore.subscribe((value) => { client = value });
StafferStore.subscribe((value) => { staffer = value });

export async function getClient(client_id) {
    if (client && client.id == client_id) return client;
    let result = await jspa("/Client", "getClient", { id: client_id })
    ClientStore.set(result.result)
    return result.result
}

// This is an alias of getClient.  I eventually want to rename getClient getParticipant.
export async function getParticipant(client_id) {
    return getClient(client_id);
}

export async function getStaffer(staff_id) {
    if (staffer && staffer.id == staff_id) return staffer;
    let result = await jspa("/Staff", "getStaffer", { id: staff_id })
    // StafferStore.set(result.result)
    return result.result
}
