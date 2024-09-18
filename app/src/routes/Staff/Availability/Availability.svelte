<script>
    import DowntimeForm from "./DowntimeForm.svelte";
    import ExceptionForm from "./ExceptionForm.svelte";

    import { ModalStore } from "@app/Overlays/stores";

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

    let downtime = {};

    downtime.recurring = "once";
    downtime.all_day = true;

    const date = new Date();

    let min_date = date
        .toLocaleString()
        .slice(0, 10)
        .split("/")
        .reverse()
        .join("-");
</script>

<div class="flex items-center mb-2">
    <div class="flex-auto">
        <div class="text-xl">Unavailable Dates & Times</div>
    </div>
    <button
        type="button"
        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Add</button
    >
</div>

<div class="font-medium px-2">Once off</div>

<ul
    role="list"
    class="divide-y divide-gray-200 bg-white border border-gray-200 rounded mb-4"
>
    <li
        class="relative p-2 hover:text-indigo-600 hover:bg-gray-50 cursor-pointer"
    >
        <div>Friday 28th July 2023 4pm - 6pm</div>
        <div class="text-xs font-light text-gray-600">Dentist Appointment</div>
    </li>

    <li
        class="relative p-2 hover:text-indigo-600 hover:bg-gray-50 cursor-pointer"
    >
        <div>Monday 24th July 2023 - Friday 28th July 2023</div>
        <div class="text-xs font-light text-gray-600">Camping</div>
    </li>
</ul>

<div class="font-medium px-2">Recurring</div>
<ul
    role="list"
    class="divide-y divide-gray-200 bg-white border border-gray-200 rounded"
>
    <li
        class="relative p-2 hover:text-indigo-600 hover:bg-gray-50 cursor-pointer"
    >
        <div class="flex justify-between items-center mb-2">
            <div>
                <div>Mondays 4pm - 6pm</div>
                <div class="text-xs font-light text-gray-600">
                    Volunteering <span class="text-gray-200">●</span> 24 Jul 2023
                    - 24 Jul 2024
                </div>
            </div>
        </div>
        <div class="text-xs text-gray-600">
            <span class="font-medium">Exceptions:</span> 24th July 2023
            <button
                type="button"
                class="rounded-md px-1 text-center text-xs text-indigo-600 hover:text-white hover:bg-indigo-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >Add Exception</button
            >
        </div>
    </li>

    <li
        class="relative p-2 hover:text-indigo-600 hover:bg-gray-50 cursor-pointer"
    >
        <div>Wednesday Fortnights All Day</div>
        <div class="text-xs font-light text-gray-600">
            Playing WoW <span class="text-gray-200">●</span> 24 Jul 2023 - 24 Jul
            2024
        </div>
    </li>
</ul>
