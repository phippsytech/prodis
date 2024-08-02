<script>
    import { onMount, afterUpdate } from "svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";

    import { jspa } from "@shared/jspa.js";

    export let staff_id = null;
    export let client_id = null;
    export let readOnly = false;

    let stored_staff_id = staff_id;

    let clients = [];
    let clientList = [];

    onMount(() => {
        if (staff_id) {
            loadClients(staff_id);
        }
    });

    $: {
        if (staff_id && staff_id != stored_staff_id) {
            loadClients(staff_id);
        }
    }

    function loadClients(staff_id) {
        // if (staff_id == "Choose staffer") return;
        jspa("/Client/Staff", "listStaffClientsByStaffId", {
            staff_id: staff_id,
        })
            .then((result) => {
                clientList = []; // clear the clientList
                clients = result.result;

                let selected = false;

                clients.forEach((client) => {
                    let options = {
                        option: client.client_name,
                        value: client.id,
                        selected: false,
                    };

                    if (client.id == client_id) {
                        options.selected = true;
                        selected = true;
                    }
                    if (client.archived != 1) clientList.push(options);
                });

                if (!selected) client_id = "Choose client"; // unset the selected client_id

                clientList.sort(function (a, b) {
                    const nameA = a.option.toUpperCase(); // ignore upper and lowercase
                    const nameB = b.option.toUpperCase(); // ignore upper and lowercase
                    if (nameA < nameB) return -1;
                    if (nameA > nameB) return 1;
                    return 0; // names must be equal
                });

                clientList = clientList;

                stored_staff_id = staff_id;
            })
            .catch((error) => {});
    }
</script>

<FloatingSelect
    on:change
    bind:value={client_id}
    label="Clients"
    instruction="Choose client"
    options={clientList}
    hideValidation={true}
    {readOnly}
/>
