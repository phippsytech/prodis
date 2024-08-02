<script>
    import Container from "@shared/Container.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import ClientSelector from "@app/routes/Billables/ClientSelector.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";

    let trip = {
        client_id: null,
        staff_id: $StafferStore.id,
        trip_date: new Date().toISOString().split("T")[0],
        vehicle_type: "private",
        kms: null,
    };

    BreadcrumbStore.set({
        path: [{ url: null, name: "Trip Tracking" }],
    });

    function add(trip) {
        jspa("/Trip", "addTrip", trip)
            .then((result) => {
                trip = result.result;

                toastSuccess("Trip added");
                // push('/clients/'+client.id);
            })
            .catch((error) => {
                let error_message = error.error_message;

                toastError(error_message);
            });
    }
</script>

<Container>
    <div
        class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
        style="color:#220055;"
    >
        Expenses
    </div>

    <ClientSelector bind:client_id={trip.client_id} />
    <FloatingDate bind:value={trip.trip_date} label="Trip Date" />

    <div class="bg-white px-3 pt-2 pb-4 mb-2 border border-gray-300 rounded-md">
        <div class="text-xs opacity-50 mb-2">Type of Expense</div>
        <RadioButtonGroup
            options={[
                { value: "participant", option: "Participant" },
                { value: "house", option: "House" },
            ]}
            bind:value={trip.vehicle_type}
        />
    </div>

    <FloatingInput label="KMs" type="number" bind:value={trip.kms} />

    <div class="flex justify-end">
        <button
            on:click={() => add(trip)}
            type="button"
            class="rounded-md bg-indigo-600 px-5 py-4 text-sm font-semibold text-white hover:bg-indigo-500 mb-2"
            >Add KMs</button
        >
    </div>
</Container>
