<script>
    // based on https://docs.google.com/forms/d/e/1FAIpQLSe5iLaoN-YnabjQ9hr2gkrTaIWvvYJnEtG1rbW2JDKLgZaz-Q/viewform

    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";

    import FloatingTime from "@shared/PhippsyTech/svelte-ui/forms/FloatingTime.svelte";

    import { XMarkIcon, PlusIcon } from "heroicons-svelte/24/outline";

    import { StafferStore } from "@shared/stores.js";
    $: stafferStore = $StafferStore;
    let breadcrumbs_path = [];

    let note = {
        date: "2023-04-16",
        time: "12:37",
        staff_id: 1,
        participant_id: 1,
        report_type: "Note",
        note: "Additional details to add",
    };
</script>

<h1 class="text-black text-2xl px-2 mt-0 mb-2">Add Medication</h1>

<FloatingInput label="Drug" value="Panadol Tablet 50mg" />

<!-- <FloatingInput label="Max Dose/24 hours" value="4 Tablets" />

    <FloatingInput label="Dose" value="1 Tablet" />

    <FloatingInput label="Route" value="Oral" /> -->

<FloatingTextArea placeholder="write note here" label="Directions" />
<!-- 

    <h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Directions</h1>
    
    
 -->

<h1 class="text-black text-1xl font-bold mt-0 drop-shadow mb-2">Schedule</h1>

<ul
    class="bg-white rounded-lg border border-gray-200 w-full text-gray-900 mb-2"
>
    <!-- {#if (!staff || staff.length ==0) }
        <li class=" px-3 py-2 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default text-sm">
            No staff have been allocated to this client
        </li>
    {:else} -->
    <!-- {#each staff as staffer, index (staffer.id)} -->
    <li class="px-3 py-2 border-b border-gray-200 w-full rounded-t-lg">
        <div class="flex justify-between">
            <div class="">7:00</div>

            <div
                on:click={() => removeClientStaffer(staffer.id)}
                class="cursor-pointer"
            >
                <XMarkIcon class="w-4 h-4 inline" />
            </div>
        </div>
    </li>
    <!-- {/each} -->
    <!-- {/if} -->
</ul>

<div class="flex justify-between items-center">
    <FloatingTime label="Time" />
    <button
        class="inline-block px-6 py-4 bg-gray-400 text-white font-medium text-sm leading-snug uppercase rounded hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out ml-2 mb-2"
        on:click={() => addMedication()}><PlusIcon class="w-6 h-6" /></button
    >
</div>

<div class="flex justify-between">
    <span></span>
    <Button label="Submit" on:click={() => addClient()} />
</div>
