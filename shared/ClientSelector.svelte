<script>
    import { onDestroy, createEventDispatcher } from "svelte";

    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";
    import { StafferStore, RolesStore } from "@shared/stores.js";
    import { haveCommonElements } from "@shared/utilities.js";

    export let on_hold = false;
    export let client_id = null;
    export let staff_id = null;
    export let readOnly = false;
    export let clearable = false;

    let clients = [];
    let clientList = [];

    $: rolesStore = $RolesStore;

    const unsubscribe = StafferStore.subscribe(($staffer) => {
        if ($staffer.id && staff_id === null) {
            // Check if staff_id prop is null
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
                clientList = []; // clear the clientList
                clients = result.result;

                let selected = false;

                clients.forEach((client) => {
                    let options = {
                        option: client.client_name,
                        value: client.client_id,
                        selected: false,
                    };

                    if (client.client_id == client_id) {
                        options.selected = true;
                        selected = true;
                    }

                    if (client.archived != 1) clientList.push(options);
                });

                // if (!selected) client_id = null; // unset the selected client_id

                clientList.sort(function (a, b) {
                    const nameA = a.option.toUpperCase(); // ignore upper and lowercase
                    const nameB = b.option.toUpperCase(); // ignore upper and lowercase
                    if (nameA < nameB) return -1;
                    if (nameA > nameB) return 1;
                    return 0; // names must be equal
                });
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
</script>

<FloatingSelect
    on:change={handleChange}
    bind:value={client_id}
    label="Client"
    instruction="Choose client"
    options={clientList}
    hideValidation={true}
    {readOnly}
    {clearable}
/>
