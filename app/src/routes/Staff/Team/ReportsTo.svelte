<script>
    import { onMount } from "svelte";
    import { slide } from "svelte/transition";
    import { XMarkIcon } from "heroicons-svelte/24/outline";
    import StaffSelector from "./StaffSelector.svelte";
    import { jspa } from "@shared/jspa.js";

    export let staff_id;

    let selectedValue;
    let staff = [];

    function selectStaffer(event) {
        console.log(event);
        let newstaffer = {};
        newstaffer.name = event.detail.label;
        newstaffer.id = event.detail.value;

        jspa("/Staff/Team", "addTeamMember", {
            supervisor_id: newstaffer.id,
            staff_id: staff_id,
        }).then((result) => {
            // Check if the staffer is already in the list
            if (!staff.some((staffer) => staffer.id === newstaffer.id)) {
                staff = [...staff, newstaffer];
            }
        });

        selectedValue = null; // clears the select ready for the next selection
    }

    function removeTeamMember(supervisor_id) {
        jspa("/Staff/Team", "removeTeamMember", {
            supervisor_id: supervisor_id,
            staff_id: staff_id,
        }).then((result) => {
            staff = staff.filter((staffer) => staffer.id !== staff_id);
        });
    }

    onMount(async () => {
        jspa("/Staff/Team", "listSupervisorsByStaffId", {
            staff_id: staff_id,
        }).then((result) => {
            staff = result.result;
        });
    });
</script>

<div class="bg-white px-3 pt-2 pb-4 mb-2 border border-gray-300 rounded-md">
    <h1 class="text-black text-1xl font-bold mt-0 mb-2">Reports to</h1>

    <div class="flex justify-between items-center gap-x-1 mb-2">
        <div class="flex-grow">
            <StaffSelector bind:selectedValue on:change={selectStaffer} />
        </div>
    </div>

    {#if staff.length > 0}
        <ul
            class="bg-white rounded-lg border border-gray-200 w-full text-gray-900"
        >
            {#each staff as staffer, index (index)}
                <li
                    in:slide={{ duration: 200 }}
                    out:slide={{ duration: 200 }}
                    class="flex justify-between hover:bg-indigo-50 px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-gray-600 transition duration-500 {staff.length -
                        1 ==
                    index
                        ? 'rounded-b-lg'
                        : ''}border-b border-gray-200 w-full {staffer.archived ==
                    1
                        ? 'text-gray-400 cursor-default'
                        : ''}"
                >
                    <div>{staffer.name}</div>
                    <div
                        class="hover:text-indigo-600 cursor-pointer"
                        on:click={() => removeTeamMember(staffer.id)}
                    >
                        <XMarkIcon class="h-5 w-5" />
                    </div>
                </li>
            {/each}
        </ul>
    {/if}
</div>
