<script>
    import { jspa } from "@shared/jspa.js";
    import { StafferStore, BreadcrumbStore } from "@shared/stores.js";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";
    import Container from "@shared/Container.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import StaffSelector from "@app/routes/Billables/StaffSelector.svelte";
    import ClientSelector from "@app/routes/Billables/ClientSelector.svelte";
    import ServiceSelector from "@app/routes/Billables/ServiceSelector.svelte";
    import ClaimSelector from "@app/routes/Billables/ClaimSelector.svelte";

    export let timetracking = {};
    export let mode = "add";

    let billing = [];

    let readOnly = false;
    let date = new Date();

    timetracking.staff_id = $StafferStore.id;
    timetracking.session_date = date.toISOString().slice(0, 10);

    BreadcrumbStore.set({
        path: [
            { url: "/billing", name: "Billing" },
            { url: null, name: "Add Entry" },
        ],
    });

    function addTimeTracking() {
        // validation
        if (
            timetracking.staff_id &&
            timetracking.client_id &&
            timetracking.participant_service_id
        ) {
            jspa("/TimeTracking", "addTimeTracking", timetracking)
                .then((result) => {
                    // billing.push(timetracking);
                    billing = [...billing, { ...timetracking }];
                    toastSuccess("Added billing");
                })
                .catch((error) => {
                    toastError("There was an error adding billing item");
                });
        } else {
            toastError("Please fill in all fields");
        }
    }

    let sta_calc_hours = 0;
    // let sta_calc_mins = Math.floor(timetracking.session_duration/60);

    $: sta_calc_mins = Math.floor((sta_calc_hours / 24) * 60);
</script>

<div class=" mb-2">
    <div
        class="text-3xl tracking-tight font-fredoka-one-regular"
        style="color:#220055;"
    >
        Add Billing
    </div>

    <div class="bg-yellow-50 p-2 mt-1">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg
                    class="h-5 w-5 text-yellow-400"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <b>Note:</b> This billing form bypasses some of the validation
                    designed to protect eroneous data entry.
                </p>
            </div>
        </div>
    </div>
</div>

<Container>
    <div class="text-sm">STA Hour Calculator</div>

    <div
        class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 items-center"
    >
        <div class="flex-grow">
            <FloatingInput label="STA Hours" bind:value={sta_calc_hours} />
        </div>
        <div class="flex-grow">
            <FloatingInput label="Minutes" bind:value={sta_calc_mins} />
        </div>
    </div>
</Container>

<Container>
    <div
        class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 items-center"
    >
        <div class="flex-grow">
            <StaffSelector bind:staff_id={timetracking.staff_id} {readOnly} />
        </div>

        <div class="flex-grow">
            <ClientSelector
                bind:client_id={timetracking.client_id}
                bind:staff_id={timetracking.staff_id}
                {readOnly}
            />
        </div>

        <div class="flex-grow">
            <ServiceSelector
                bind:participant_service_id={timetracking.participant_service_id}
                bind:client_id={timetracking.client_id}
                {readOnly}
            />
        </div>

        <div class="flex-grow">
            <FloatingDate
                bind:value={timetracking.session_date}
                label="Date"
                {readOnly}
            />
        </div>

        <div class="flex-grow">
            <ClaimSelector
                bind:claim_type={timetracking.claim_type}
                bind:cancellation_reason={timetracking.cancellation_reason}
                {readOnly}
            />
        </div>

        <div class="flex-grow">
            <FloatingInput
                bind:value={timetracking.session_duration}
                label="Duration (in mins)"
                placeholder="eg: 90"
                inputmode="numeric"
                inputmask="xxxx"
                {readOnly}
            />
        </div>

        <button
            on:click={() => addTimeTracking()}
            type="button"
            class="w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 mb-2"
            >Add
        </button>
    </div>
</Container>

<Container>
    {#each billing as item}
        <div
            class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 items-center"
        >
            <div class="flex-grow">{item.session_date}</div>
            <div class="flex-grow">{item.staff_id}</div>
            <div class="flex-grow">{item.client_id}</div>
            <div class="flex-grow">{item.service_id}</div>
            <div class="flex-grow">{item.claim_type}</div>
            <div class="flex-grow">{item.session_duration}</div>
        </div>
    {/each}
</Container>
