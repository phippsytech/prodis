<script>
    import Role from "@shared/Role.svelte";
    import StaffSelector from "@shared/StaffSelector.svelte";
    import Container from "@shared/Container.svelte";
    import Badge from "@shared/PhippsyTech/Tailwind/App/Elements/Badge.svelte";

    import { onMount } from "svelte";
    import { push, replace } from "svelte-spa-router";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { getClient } from "@shared/api.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { ContextMenuStore } from "@app/Overlays/stores.js";

    export let params;

    let client_id = params.client_id;
    let client;

    let house_lead_id;
    let behaviour_therapist_id;
    let occupational_therapist_id;
    let speech_therapist_id;
    // let staffList=[];
    let staff = [];
    let client_staff = [];
    let staff_id;

    onMount(async () => {
        client = await getClient(client_id);

        BreadcrumbStore.set({
            path: [
                { name: "Clients", url: "/clients" },
                { name: client.name, url: "/clients/" + client.id },
            ],
        });

        jspa("/Client/Staff", "listClientStaffByClientId", {
            client_id: client_id,
        }).then((result) => {
            staff = result.result;
        });
    });

    function removeClientStaffer(client_staff_id) {
        jspa("/Client/Staff", "deleteClientStaffer", {
            id: client_staff_id,
        }).then((result) => {
            staff = staff.filter((staffer) => staffer.id !== client_staff_id);
            // staff=staff
        });
    }

    function addStaffer() {
        jspa("/Client/Staff", "addClientStaffer", {
            client_id: client_id,
            staff_id: staff_id,
        }).then((result) => {
            staff.push(result.result);
            staff = staff;
        });
    }

    function getStaffList() {
        let list = [];
        staff.sort(function (a, b) {
            const nameA = a.name.toUpperCase(); // ignore upper and lowercase
            const nameB = b.name.toUpperCase(); // ignore upper and lowercase
            if (nameA < nameB) return -1;
            if (nameA > nameB) return 1;
            return 0; // names must be equal
        });

        staff.forEach((staffer) => {
            // if(staffer.house_lead==1){
            //     house_lead_id = staffer.staff_id;
            // }
            // if(staffer.primary_therapist==1){
            //     behaviour_therapist_id = staffer.staff_id;
            // }
            // if(staffer.primary_occupational_therapist==1){
            //     occupational_therapist_id = staffer.staff_id;
            // }
            // if(staffer.primary_speech_therapist==1){
            //     speech_therapist_id = staffer.staff_id;
            // }
            // if (staffer.archived!=1) staffList.push({option:staffer.name, value: staffer.staff_id})
            // let options ={
            //     option:staffer.name,
            //     value: staffer.staff_id,
            //     selected: false,
            // }
            // if (staffer.staff_id == house_lead_id) options.selected = true;
            // if (staffer.archived!=1) list.push(options)
        });
        return list;
    }

    $: staffList = getStaffList();

    function togglePrimary(staff_id, type) {
        jspa("/Client/Staff", "togglePrimaryTherapist", {
            client_id: client_id,
            staff_id: staff_id,
            primary_therapist_type: type,
        }).then((result) => {
            staff.find((item, index) => {
                if (item.staff_id == staff_id) {
                    staff[index][type] = 1;
                } else {
                    staff[index][type] = 0;
                }
            });
            staff = staff;
        });
    }

    function handleClick(event, staffer) {
        ContextMenuStore.set({
            show: true,
            title: staffer.name,
            options: [
                {
                    name: "Primary Behavioural Therapist",
                    value: "primary_therapist",
                },
                {
                    name: "Primary Occupational Therapist",
                    value: "primary_occupational_therapist",
                },
                {
                    name: "Primary Speech Therapist",
                    value: "primary_speech_therapist",
                },
                { name: "SIL House Lead", value: "house_lead" },
                { name: "Remove from client", value: "remove" },
            ],
            select: (value) => {
                if (value == "remove") {
                    removeClientStaffer(staffer.id);
                    return;
                }
                togglePrimary(staffer.staff_id, value);
            },
            x: event.clientX, // + 10,
            y: event.clientY, // - top +50,
        });
    }
</script>

<Role roles={["sil.admin", "admin"]}>
    <Container>
        <div class="text-sm mb-1 font-bold opacity-50">
            Add staff member to this client
        </div>

        <div class="flex justify-between items-center gap-x-1">
            <div class="flex-grow">
                <StaffSelector bind:staff_id />
            </div>

            <button
                on:click={() => addStaffer()}
                type="button"
                class="w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
                >Add</button
            >
        </div>
    </Container>
</Role>

<ul
    class="bg-white rounded-lg border border-gray-200 w-full text-gray-900 mb-2"
>
    {#if !staff || staff.length == 0}
        <li
            class=" px-3 py-2 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default text-sm"
        >
            No staff have been allocated to this client
        </li>
    {:else}
        {#each staff as staffer, index (staffer.id)}
            <li
                on:click={(event) => handleClick(event, staffer)}
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                class="px-3 py-2 border-b border-gray-200 w-full hover:bg-indigo-600 hover:text-white cursor-pointer"
            >
                <div class="flex justify-left">
                    <div class="">{staffer.name}</div>

                    {#if staffer.primary_occupational_therapist == 1}
                        <Badge label="Occupational" />
                    {/if}

                    {#if staffer.primary_speech_therapist == 1}
                        <Badge label="Speech" />
                    {/if}

                    {#if staffer.primary_therapist == 1}
                        <Badge label="Behavioural" />
                    {/if}

                    {#if staffer.house_lead == 1}
                        <Badge label="House Lead" />
                    {/if}
                </div>
            </li>
        {/each}
    {/if}
</ul>
