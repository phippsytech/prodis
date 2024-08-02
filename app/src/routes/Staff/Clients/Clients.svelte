<script>
    import Role from "@shared/Role.svelte";
    import StaffSelector from "@shared/StaffSelector.svelte";
    import ClientSelector from "@shared/ClientSelector.svelte";
    import StaffRoleSelector from "@shared/StaffRoleSelector.svelte";
    import { STAFF_ROLES } from "@shared/constants.js";
    import { jspa } from "@shared/jspa.js";
    import { getStaffer } from "@shared/api.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { slide } from "svelte/transition";
    import { flip } from "svelte/animate";
    import { quintOut } from "svelte/easing";
    import { XMarkIcon, PlusIcon } from "heroicons-svelte/24/outline";
    import { onMount } from "svelte";
    import { getClient } from "@shared/api.js";
    import { writable } from "svelte/store";
    import { convertFieldsToBoolean } from "@shared/utilities/convertFieldsToBoolean";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";

    export let params;

    let staff_id = params.staff_id;

    let staffer = {};

    let clients = [];
    let staff_clients = [];
    let client_id;

    let client = {
        client_id: client_id,
        staff_id: staff_id,
        is_primary: false,
        staff_role: null,
    };

    onMount(async () => {
        staffer = await getStaffer(staff_id);

        BreadcrumbStore.set({
            path: [
                { url: "/staff", name: "Staff" },
                { url: null, name: staffer.name },
            ],
        });
    });

    let assigned_clients = writable([]);

    jspa("/Client/Staff", "listStaffClientsByStaffId", {
        staff_id: staff_id,
    }).then((result) => {
        let staff = convertFieldsToBoolean(result.result, ["is_primary"]);
        assigned_clients.set(staff);
    });

    let groupedClients = writable({});

    function groupAndSortClients(staff) {
        // Grouping staff by role
        let grouped = staff.reduce((acc, staff) => {
            if (!acc[staff.staff_role]) {
                acc[staff.staff_role] = [];
            }
            acc[staff.staff_role].push(staff);
            return acc;
        }, {});

        // Sorting each group by primary status and then alphabetically by name
        for (let role in grouped) {
            grouped[role].sort((a, b) => {
                // if (a.is_primary === b.is_primary) {
                return a.client_name.localeCompare(b.client_name);
                // }
                // return a.is_primary ? -1 : 1;
            });
        }

        return grouped;
    }

    function getRoleName(value) {
        const role = STAFF_ROLES.find((role) => role.value === value);
        return role ? role.option : value;
    }

    function addClient(newClient) {
        if (newClient.client_id === null) {
            toastError("Please select a client");
            return;
        }

        if (newClient.staff_role === null) {
            toastError("Please select a role for the staff member");
            return;
        }

        jspa("/Client/Staff", "addClientStaffer", newClient).then((result) => {
            let resultClient = convertFieldsToBoolean(result.result, [
                "is_primary",
            ]);
            delete resultClient.update;
            delete resultClient.created;
            delete resultClient.archived;
            assigned_clients.update((staff) => {
                const updatedStaff = [...staff, resultClient];
                groupedClients.set(groupAndSortClients(updatedStaff));
                return updatedStaff;
            });
        });
    }

    function removeClient(staffId) {
        jspa("/Client/Staff", "deleteClientStaffer", { id: staffId }).then(
            (result) => {
                assigned_clients.update((staff) => {
                    const updatedStaff = staff.filter(
                        (member) => member.id !== staffId,
                    );
                    groupedClients.set(groupAndSortClients(updatedStaff));
                    return updatedStaff;
                });
            },
        );
    }

    assigned_clients.subscribe((staff) => {
        groupedClients.set(groupAndSortClients(staff));
    });
</script>

<div class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular">
    Manage Clients
</div>
<div class="text-sm text-slate-600 mb-2">
    Manage which clients this staff member is assigned to.
</div>

<Role roles={["admin"]}>
    <div class="rounded-md bg-slate-100 pt-3 p-2 mb-2">
        <h3 class="text-sm text-slate-500 font-bold mb-1 mx-1">
            Add client to staff member
        </h3>

        <div class="flex justify-between items-center gap-x-1 items-stretch">
            <div class="flex-grow">
                <ClientSelector bind:client_id={client.client_id} />
            </div>

            <div class="flex-grow">
                <StaffRoleSelector bind:staff_role={client.staff_role} />
            </div>

            <button
                on:click={() => addClient(client)}
                type="button"
                class="flex items-center justify-center mb-2 block rounded-md bg-indigo-600 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                ><PlusIcon class="h-5 w-5 inline-block" /></button
            >
        </div>
    </div>
</Role>

{#each Object.keys($groupedClients) as role}
    <h3 class="text-slate-800 font-bold mt-3 mb-1 mx-2">
        {getRoleName(role)}
    </h3>

    <ul
        class="bg-white rounded-lg border border-indigo-100 w-full text-slate-900 mb-4"
    >
        {#each $groupedClients[role] as member, index (member.id)}
            <li
                animate:flip={{
                    duration: 500,
                    easing: quintOut,
                }}
                in:slide={{ duration: 250 }}
                out:slide|local={{ duration: 250 }}
                class="group flex justify-between items-center hover:bg-indigo-50 px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 {$groupedClients[
                    role
                ].length -
                    1 ==
                index
                    ? 'rounded-b-lg '
                    : ''}border-b border-indigo-100 w-full
                        
                        
                        
                        "
            >
                <div class="staff-member">
                    <div class="text-sm">
                        {member.client_name}
                    </div>
                </div>
                <button
                    on:click={() => removeClient(member.id)}
                    type="button"
                    class="flex p-1 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300"
                    ><XMarkIcon class="h-4 w-4 inline m-0 p-0" /></button
                >
            </li>
        {/each}
    </ul>
{/each}

<style>
    .transition-height {
        transition: height 0.2s ease-in-out;
    }
</style>
