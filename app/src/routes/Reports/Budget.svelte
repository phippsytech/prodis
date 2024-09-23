<script>
    import { push } from "svelte-spa-router";
    import Container from "@shared/Container.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import ServiceBooking from "@app/routes/Clients/ServiceAgreements/ServiceBookings/ServiceBooking.svelte";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import QueryManager from "@shared/QueryManager.svelte";
    import { getQueryParams } from "@shared/utilities.js";

    // let staff_id = null;
    let clients = [];
    let staff = [];
    let staffList = [];

    let clientList = [];

    let queryParams = getQueryParams();
    let staff_id = queryParams.staff_id;
    $: queryParams = { staff_id };

    BreadcrumbStore.set({
        path: [
            { url: "/reports", name: "Reports" },
            { url: null, name: "Budget Report" },
        ],
    });

    jspa("/Staff", "listStaff", {}).then((result) => (staff = result.result));

    $: {
        staff.forEach((staffer) => {
            if (staffer.archived != 1)
                staffList.push({
                    option: staffer.staff_name,
                    value: staffer.id,
                });
        });

        staffList.sort(function (a, b) {
            const nameA = a.option.toUpperCase(); // ignore upper and lowercase
            const nameB = b.option.toUpperCase(); // ignore upper and lowercase
            if (nameA < nameB) return -1;
            if (nameA > nameB) return 1;
            return 0; // names must be equal
        });

        staffList = staffList;
    }

    $: {
        handleStaffChange(staff_id);
    }

    function handleStaffChange(staff_id) {
        // changeFilter(staff_id); // to make the select box show the selected staff
        clients = [];
        clientList = [];

        jspa("/Client/Staff", "listStaffClientsByStaffId", {
            staff_id: staff_id,
        }).then((result) => {
            clients = result.result;
            clients.forEach((client, index) => {
                // only get the services
                if (client.is_primary == "1") {
                    jspa("/Participant/ServiceBooking", "listServiceBookings", {
                        participant_id: client.client_id,
                    }).then((result) => {
                        // filter out inactive services
                        const filteredResult = result.result.filter(
                            (service) => service.is_active === true,
                        );
                        clients[index].services = filteredResult;
                        client.services = filteredResult;
                        console.log(client);
                        clientList = [...clientList, client];

                        // sort clientList by client_name
                        clientList.sort(function (a, b) {
                            const nameA = a.client_name.toUpperCase(); // ignore upper and lowercase
                            const nameB = b.client_name.toUpperCase(); // ignore upper and lowercase
                            if (nameA < nameB) return -1;
                            if (nameA > nameB) return 1;
                            return 0; // names must be equal
                        });
                    });
                }
            });
        });
    }
</script>

<!-- <QueryManager params={queryParams} /> -->
<QueryManager
    params={{ ...queryParams }}
    onParamsChange={(params) => (staff_id = params.staff_id)}
/>

<FloatingSelect
    bind:value={staff_id}
    label="Staff"
    instruction="Choose staff"
    options={staffList}
    hideValidation={true}
/>

{#if staff_id != null}
    {#each clientList as client}
        <Container>
            <h1
                on:click={() => push("/clients/" + client.client_id)}
                class="font-bold mt-0 mb-2"
            >
                {client.client_name}
            </h1>
            {#if client.services && client.services.length > 0}
                {#each client.services as service, index (service.id)}
                    <ServiceBooking service_booking={service} />
                    {#if index < client.services.length - 1}<hr
                            class="my-2"
                        />{/if}
                {/each}
            {/if}
        </Container>
    {:else}
        {#if staff_id != "Choose staff"}
            <Container>
                <h1 class="font-bold mt-0 mb-2">
                    This staff member has not been set as the primary therapist
                    for any clients
                </h1>
            </Container>
        {/if}
    {/each}
{:else}
    Select a staff member
{/if}
