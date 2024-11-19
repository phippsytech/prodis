<script>
    import { onMount } from "svelte";
    import { slide } from "svelte/transition";
    import { XMarkIcon } from "heroicons-svelte/24/outline";
    import StaffSelector from "./StaffSelector.svelte";
    import { jspa } from "@shared/jspa.js";

    export let user_id;

    let selectedValue;
    let staffs = [];

    function selectStaff(event) {
        let newStaff = {};
        newStaff.name = event.detail.label;
        newStaff.id = event.detail.value;

        jspa("/User/Staff", "allowUserStaff", {
            user_id: user_id,
            staff_id: newStaff.id,
        }).then((result) => {
            // Check if the staff is already in the list
            if (
                !staffs.some(
                    (staff) => staff.id === newStaff.id,
                )
            ) {
                staffs = [...staffs, newStaff];
            }
        });

        selectedValue = null; // clears the select ready for the next selection
    }

    function removeStaff(staff_id) {
        jspa("/User/Staff", "deleteUserStaff", {
            user_id: user_id,
            staff_id: staff_id,
            allowed: 1,
        }).then((result) => {
            staffs = staffs.filter(
                (staff) => staff.id !== staff_id,
            );
        });
    }

    onMount(async () => {
        jspa("/User/Staff", "listAllowedByUserId", {
            user_id: user_id,
        }).then((result) => {
            staffs = result.result;
        });
    });
</script>

<div class="bg-white px-3 pt-2 pb-4 mb-2 border border-indigo-100 rounded-md">
    <h1 class="text-slate-800 text-1xl font-bold mt-0 mb-2">
        Allow access to the following staffs
    </h1>

    <div class="flex justify-between items-center gap-x-1 mb-2">
        <div class="flex-grow">
            <StaffSelector
                bind:selectedValue
                on:change={selectStaff}
            />
        </div>
    </div>

    {#if staffs.length > 0}
        <ul
            class="bg-white rounded-lg border border-indigo-100 w-full text-gray-900"
        >
            {#each staffs as staff, index (index)}
                <li
                    in:slide={{ duration: 200 }}
                    out:slide={{ duration: 200 }}
                    class="flex justify-between hover:bg-indigo-50 px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-gray-600 transition duration-500 {staffs.length -
                        1 ==
                    index
                        ? 'rounded-b-lg'
                        : ''}border-b border-indigo-100 w-full {staff.archived ==
                    1
                        ? 'text-gray-400 cursor-default'
                        : ''}"
                >
                    <div>{staff.name}</div>
                    <div
                        class="hover:text-indigo-600 cursor-pointer"
                        on:click={() => removeStaff(staff.id)}
                    >
                        <XMarkIcon class="h-5 w-5" />
                    </div>
                </li>
            {/each}
        </ul>
    {/if}
</div>
