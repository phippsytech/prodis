<script>
    import { onDestroy, createEventDispatcher } from "svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte"; // Keep only FloatingCombo
    import { jspa } from "@shared/jspa.js";
    import { StafferStore, RolesStore } from "@shared/stores.js";
    import { haveCommonElements } from "@shared/utilities.js";

    export let on_hold = false;
    export let client_id = null;
    export let staff_id = null;
    export let readOnly = false;
    export let clearable = false;
    export let is_not_valid = tru; // Flag to check if client_id is valid

    let clients = [];
    let clientList = [];

    $: rolesStore = $RolesStore;

    const unsubscribe = StafferStore.subscribe(($staffer) => {
        if ($staffer.id && staff_id === null) {
            staff_id = $staffer.id;
            loadClients(staff_id);
        }
    });

    const dispatch = createEventDispatcher();
    $: if (staff_id !== null) {
        loadClients(staff_id);
    }

    onDestroy(() => {
        unsubscribe();
    });

    $: {
        on_hold = false;
        if (client_id && client_id != null) {
            checkClientOnHold(client_id);
        }
    }

    function loadClients(staff_id) {
        let action = "listStaffClientsByStaffId";
        let endpoint = "/Client/Staff";
        if (haveCommonElements(rolesStore, ["accounts", "admin"])) {
            action = "listClients";
            endpoint = "/Client";
        }

        jspa(endpoint, action, { staff_id: staff_id })
            .then((result) => {
                clientList = [];
                clients = result.result;

                clients.forEach((client) => {
                    let options = {
                        label: client.client_name, 
                        value: client.client_id,
                    };

                    if (client.archived != 1) clientList.push(options);
                });

                clientList.sort((a, b) => a.label.localeCompare(b.label));
                validateClientId(client_id); // Revalidate after loading clients
            })
            .catch((error) => {});
    }

    function handleChange(event) {
        checkClientOnHold(event.detail.value);
        dispatch("change", { value: event.detail.value });
    }

    function checkClientOnHold(client_id) {
        const client = clients.find((client) => client.client_id == client_id);
        if (client) {
            on_hold = client.on_hold == "1";
        }
    }

    function validateClientId(client_id) {
        is_not_valid = !clientList.some((client) => client.value == client_id);
    }
</script>

<FloatingCombo
    label="Client"
    items={clientList}
    bind:value={client_id}
    placeholderText="Select or type client name ..."
    on:change={handleChange}
    {readOnly}
/>