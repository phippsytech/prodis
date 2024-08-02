<script>
    import { jspa } from "@shared/jspa.js";
    import { formatDate } from "@shared/utilities.js";
    import { onMount } from "svelte";
    import { getStaffer } from "@shared/api.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    export let params;

    let staff_id = params.staff_id;

    let staffer = {};

    onMount(async () => {
        staffer = await getStaffer(staff_id);

        BreadcrumbStore.set({
            path: [
                { url: "/staff", name: "Staff" },
                { url: null, name: staffer.name },
            ],
        });
    });

    let shifts = [];

    jspa("/SIL/House/Roster", "getShiftsByStaffId", {
        staff_id: staff_id,
    }).then((result) => {
        shifts = result.result;
    });
</script>

<div class="flex justify-between">
    <h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
        Shifts
    </h1>
</div>

<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#if !shifts || shifts.length == 0}
        <li
            class="px-3 py-2 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default text-sm"
        >
            No rostered shifts have been recorded for this staff member
        </li>
    {:else}
        {#each shifts as shift, index (shift.id)}
            <li
                in:slide={{ duration: 200 }}
                out:slide={{ duration: 200 }}
                class="px-3 py-2 border-b border-gray-200 w-full rounded-t-lg hover:bg-gray-200"
            >
                <div class="flex justify-between">
                    <div class="text-sm text-gray-900">
                        {formatDate(shift.start_date, {
                            day: "numeric",
                            month: "short",
                        })} - <span class="font-bold">{shift.start_time}</span>
                        to <span class="font-bold">{shift.end_time}</span>
                        {#if shift.start_date != shift.end_date}
                            ({formatDate(shift.end_date, {
                                day: "numeric",
                                month: "short",
                            })})
                        {/if}
                    </div>
                    <div class="text-sm text-gray-500">
                        {shift.client_name}
                    </div>
                </div>
            </li>
        {/each}
    {/if}
</ul>
