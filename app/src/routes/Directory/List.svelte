<script>
    import { push } from "svelte-spa-router";
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { slide } from "svelte/transition";
    import { flip } from "svelte/animate";
    import { quintOut } from "svelte/easing";

    let staff = [];
    let staff_list = [];
    let showArchived = false;
    export let search = "";

    onMount(() => {
        jspa("/Staff", "listStaff", {}).then((result) => {
            staff = result.result;

            staff.sort(function (a, b) {
                const nameA = a.staff_name.toUpperCase(); // ignore upper and lowercase
                const nameB = b.staff_name.toUpperCase(); // ignore upper and lowercase
                if (nameA < nameB) return -1;
                if (nameA > nameB) return 1;
                return 0; // names must be equal
            });
        });
    });

    $: {
        staff_list = staff.filter((staffer) => staffer.archived != 1);

        if (search.length > 0) {
            // filter by client name
            // there is a problem which will mean if client.name is removed it will effectively hide the record
            staff_list = staff_list.filter(
                (staffer) =>
                    staffer.staff_name &&
                    staffer.staff_name
                        .toLowerCase()
                        .includes(search.toLowerCase()) == true,
            );
        }
    }
</script>

<!-- <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight pt-5 px-5">Staff</h2> -->
<div
    class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 mx-4 mb-4"
>
    {#each staff_list as staffer, index (staffer.id)}
        <div
            class="border border-gray-200 rounded rounded-lg relative flex justify-between py-2 px-5 text-gray-700 hover:text-indigo-600 hover:bg-gray-50 bg-white"
        >
            <div>
                <div class="font-medium">{staffer.staff_name}</div>
                {#if staffer.email}<div class="text-sm">
                        {staffer.email}
                    </div>{/if}
                {#if staffer.phone_number}<div class="text-sm">
                        {staffer.phone_number}
                    </div>{/if}
            </div>
        </div>
    {/each}
</div>
