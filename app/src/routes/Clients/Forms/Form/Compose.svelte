<script>
    // based on https://docs.google.com/forms/d/e/1FAIpQLSe5iLaoN-YnabjQ9hr2gkrTaIWvvYJnEtG1rbW2JDKLgZaz-Q/viewform

    import { push } from "svelte-spa-router";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import { ArrowLeftIcon } from "heroicons-svelte/24/outline";
    import { jspa } from "@shared/jspa.js";
    import { StafferStore, HouseStore } from "@shared/stores.js";

    const date = new Date();

    export let staff_id = null;
    export let participant_id = null;
    export let form = {
        date: `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, "0")}-${date.getDate().toString().padStart(2, "0")}`,
        time: date.toTimeString().slice(0, 5),
        staff_id: staff_id,
        participant_id: participant_id,
        report_type: "Note",
        note: null,
    };

    $: houseStore = $HouseStore;
    $: stafferStore = $StafferStore;

    function saveForm(form) {
        jspa("/SIL/House/Form", "saveForm", form)
            .then((result) => push("/"))
            .catch((error) => {});
    }
</script>

<div class="flex min-h-screen bg-white text-gray-400">
    <div>
        <div class="text-xs text-black/50 bg-white px-4 py-0 pt-2">
            {form.date}
            {form.time} - {stafferStore.name}
        </div>

        <textarea
            class="w-full h-full resize-none border border-none px-4 py-2 focus:outline-none"
            placeholder="write note here"
            style="font-size:1.25em;"
            bind:value={form.note}
            autofocus
        ></textarea>
    </div>
</div>

<!-- 
<div class="" >
<div class="min-h-screen flex flex-col">
    <header class="bg-white">
        
        <div class="text-xs text-black/50 bg-white px-4 py-0">
            {form.date} {form.time} - {stafferStore.name}</div>
    </header>
    <main class="flex-1 flex bg-white" >
        <textarea class="w-full resize-none border border-none px-4 py-2 focus:outline-none" placeholder="write note here" style="font-size:1.25em;" bind:value={form.note} autofocus ></textarea>
    </main>
    <footer ></footer>
</div> 
</div> -->
