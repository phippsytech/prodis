<script>
    import Role from "@shared/Role.svelte";
    import StaffSelector from "@shared/StaffSelector.svelte";
    import StaffRoleSelector from "@shared/StaffRoleSelector.svelte";
    import { STAFF_ROLES } from "@shared/constants.js";
    import { jspa } from "@shared/jspa.js";
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

    let client_id = params.client_id;
    let client;

    let staff = [];
    let client_staff = [];
    let staff_id;

    let staffer = {
        client_id: client_id,
        staff_id: null,
        staff_role: null,
        is_primary: false,
    };

    let assigned_staff = writable([]);

    jspa("/Client/Staff", "listClientStaffByClientId", {
        client_id: client_id,
    }).then((result) => {
        let staff = convertFieldsToBoolean(result.result, ["is_primary"]);
        assigned_staff.set(staff);
    });

    let groupedStaff = writable({});

    function groupAndSortStaff(staff) {
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
                if (a.is_primary === b.is_primary) {
                    return a.staff_name.localeCompare(b.staff_name);
                }
                return a.is_primary ? -1 : 1;
            });
        }

        return grouped;
    }

    function getRoleName(value) {
        const role = STAFF_ROLES.find((role) => role.value === value);
        return role ? role.option + "s" : value;
    }

    function setAsPrimary(staffId) {
        jspa("/Client/Staff", "makePrimary", { id: staffId }).then((result) => {
            assigned_staff.update((staff) => {
                const updatedStaff = staff.map((member) => ({
                    ...member,
                    is_primary:
                        member.id === staffId
                            ? true
                            : member.staff_role ===
                                staff.find((s) => s.id === staffId).staff_role
                              ? false
                              : member.is_primary,
                }));
                groupedStaff.set(groupAndSortStaff(updatedStaff));
                return updatedStaff;
            });
        });
    }

    function addStaff(newStaff) {
        if (newStaff.staff_id === null) {
            toastError("Please select a staff member");
            return;
        }

        if (newStaff.staff_role === null) {
            toastError("Please select a role for the staff member");
            return;
        }

        jspa("/Client/Staff", "addClientStaffer", newStaff).then((result) => {
            let resultStaff = convertFieldsToBoolean(result.result, [
                "is_primary",
            ]);
            delete resultStaff.update;
            delete resultStaff.created;
            delete resultStaff.archived;
            assigned_staff.update((staff) => {
                const updatedStaff = [...staff, resultStaff];
                groupedStaff.set(groupAndSortStaff(updatedStaff));
                return updatedStaff;
            });
        });
    }

    function removeStaff(staffId) {
        jspa("/Client/Staff", "deleteClientStaffer", { id: staffId }).then(
            (result) => {
                assigned_staff.update((staff) => {
                    const updatedStaff = staff.filter(
                        (member) => member.id !== staffId,
                    );
                    groupedStaff.set(groupAndSortStaff(updatedStaff));
                    return updatedStaff;
                });
            },
        );
    }

    assigned_staff.subscribe((staff) => {
        groupedStaff.set(groupAndSortStaff(staff));
    });

    onMount(async () => {
        client = await getClient(client_id);

        document.title = client.name + " - Staff";
        BreadcrumbStore.set({
            path: [
                { name: "Clients", url: "/clients" },
                { name: client.name, url: "/clients/" + client.id },
            ],
        });
    });
</script>

<div class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular">
    Manage Staff
</div>
<div class="text-sm text-slate-600 mb-2">
    Manage which staff are assigned to this participant.
</div>

<Role roles={["sil.admin", "admin"]}>
    <div class="rounded-md bg-slate-100 pt-3 p-2 mb-2">
        <h3 class="text-sm text-slate-500 font-bold mb-1 mx-1">
            Add staff member to this client
        </h3>

        <div class="flex justify-between items-center gap-x-1 items-stretch">
            <div class="flex-grow">
                <StaffSelector bind:staff_id={staffer.staff_id} />
            </div>

            <div class="flex-grow">
                <StaffRoleSelector bind:staff_role={staffer.staff_role} />
            </div>

            <button
                on:click={() => addStaff(staffer)}
                type="button"
                class="flex items-center justify-center mb-2 block rounded-md bg-indigo-600 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                ><PlusIcon class="h-5 w-5 inline-block" /></button
            >
        </div>
    </div>
</Role>

{#each Object.keys($groupedStaff) as role}
    <h3 class="text-slate-800 font-bold mt-3 mb-1 mx-2">
        {getRoleName(role)}
    </h3>

    <ul
        class="bg-white rounded-lg border border-indigo-100 w-full text-slate-900 mb-4"
    >
        {#each $groupedStaff[role] as member, index (member.id)}
            <li
                animate:flip={{
                    duration: 500,
                    easing: quintOut,
                }}
                in:slide={{ duration: 250 }}
                out:slide|local={{ duration: 250 }}
                class="group flex justify-between items-center hover:bg-indigo-50 px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 {$groupedStaff[
                    role
                ].length -
                    1 ==
                index
                    ? 'rounded-b-lg '
                    : ''}border-b border-indigo-100 w-full
                        
                        
                        {!member.is_primary
                    ? ' transition-height h-10 hover:h-12 '
                    : ' border-l-indigo-600 border-l-4 '}
                        
                        "
            >
                <div class="staff-member">
                    <div class="text-sm {member.is_primary ? 'font-bold' : ''}">
                        {member.staff_name}
                    </div>

                    {#if member.is_primary}
                        <div class="text-xs">Primary</div>
                    {:else}
                        <div class="slide-out group-hover:slide-in text-xs">
                            <button
                                class="px-2 rounded-full text-indigo-500 hover:bg-indigo-600 hover:text-white cursor-pointer"
                                on:click={() => setAsPrimary(member.id)}
                                >Make Primary</button
                            >
                        </div>
                    {/if}
                </div>
                <button
                    on:click={() => removeStaff(member.id)}
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
