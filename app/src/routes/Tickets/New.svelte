<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import RTE from "@shared/RTE/RTE.svelte";

    import { jspa } from "@shared/jspa.js";

    let ticket = {};

    function createTicket() {
        jspa("/Ticket", "createTicket", ticket).then((result) => {
            console.log(result);
        });
    }
</script>

<div class="flex min-h-full">
    <div class="flex w-0 flex-1 flex-col">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Create New Ticket</h1>
            <p class="mt-2 text-sm text-gray-500">
                #400 opened by
                <a href="/#/" class="font-medium text-gray-900">Hilary Mahy</a>
                in
                <a href="/#/" class="font-medium text-gray-900"
                    >Customer Portal</a
                >
            </p>
        </div>

        <FloatingInput
            label="Title"
            placeholder="A short descriptive title"
            bind:value={ticket.title}
        />
        <RTE height="250" bind:content={ticket.content} />

        <div class="mt-6 flex items-center justify-end space-x-4">
            <button
                on:click={() => createTicket()}
                type="submit"
                class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-900"
                >Create Ticket</button
            >
        </div>
    </div>
</div>
