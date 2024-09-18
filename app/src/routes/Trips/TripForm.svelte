<script>
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDurationSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDurationSelect.svelte";
    import ClientSelector from "@app/routes/Billables/ClientSelector.svelte";
    import StaffSelector from "@app/routes/Billables/StaffSelector.svelte";
    import ProviderTravelRadioGroup from "./ProviderTravelRadioGroup.svelte";
    import PlanManagerSelector from "@app/routes/Accounts/PlanManagers/PlanManagerSelector.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import Role from "@shared/Role.svelte";
    import Toggle from "@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte";
    import TravelPurpose from "./TravelPurpose.svelte";
    import { StafferStore } from "@shared/stores.js";

    const VEHICLE_OPTIONS = [
        { value: "private", option: "Private" },
        { value: "company", option: "Company" },
    ];

    const INITIAL_TRIP_STATE = {
        client_id: null,
        staff_id: $StafferStore.id,
        trip_date: new Date().toISOString().split("T")[0],
        vehicle_type: "private",
        kms: null,
        do_not_bill: false,
        // service_id: null,
        service_booking_id: null,
        planmanager_id: null,
        max_claimable_travel_duration: null,
        trip_purpose: null,
        trip_duration: 0,
    };

    export let trip = { ...INITIAL_TRIP_STATE };

    let bill_to_participant = !trip.do_not_bill;
    // This function will be used to update both trip.do_not_bill and bill_to_participant
    function updateBillToParticipant(value) {
        bill_to_participant = value;
        trip.do_not_bill = !value;
    }

    // Reactive statement to update bill_to_participant when trip.do_not_bill changes
    $: {
        if (bill_to_participant === trip.do_not_bill) {
            bill_to_participant = !trip.do_not_bill;
        }
    }

    let providerTravelComponent;

    $: {
        if (trip.do_not_bill) {
            // I might need to remember these settings if they toggle back
            trip.client_id = null;
            trip.service_booking_id = null;
            trip.planmanager_id = null;
        }
    }

    export function resetTrip() {
        trip = { ...INITIAL_TRIP_STATE };
        travelPurpose.clear();
    }

    let travelPurpose; // This is the TravelPurpose component

    let support_item_count = 0;

    // $: trip.do_not_bill = !bill_to_participant;

    // function setPlanManagerId(plan_manager_id) {
    //     trip.planmanager_id = plan_manager_id;
    // }

    let isSpeedAcceptable = true;

    $: {
        if (trip.kms > 0 && trip.trip_duration > 0) {
            let speed = trip.kms / (trip.trip_duration / 60);
            if (speed < 40) {
                isSpeedAcceptable = false;
            } else {
                isSpeedAcceptable = true;
            }
        } else {
            isSpeedAcceptable = true;
        }
    }

    // $: if (planmanager_id && trip.planmanager_id == null) {
    //     trip.planmanager_id = planmanager_id;
    // }

    let service = {};
    $: {
        if (service) {
            trip.max_claimable_travel_duration =
                service.max_claimable_travel_duration;
        }
    }

    let changeMade = false;
    function handleChange(e) {
        if (trip.planmanager_id === null) {
            trip.planmanager_id = service.plan_manager_id;
            changeMade = true;
        } else if (changeMade && trip.planmanager_id !== null) {
            trip.planmanager_id = service.plan_manager_id;
        }
        if (!changeMade && trip.planmanager_id !== null) {
            changeMade = true;
        }
    }

    function handleParticipantChange(e) {
        trip.planmanager_id = null;
        //     providerTravelComponent.loadServices(e.detail.value);
        trip.service_booking_id = null;
        trip.planmanager_id = null;
    }
</script>

<Role roles={["accounts", "admin"]}>
    <div class="flex-1">
        <StaffSelector bind:staff_id={trip.staff_id} />
    </div>
</Role>
<div class="flex justify-between flex-row gap-x-2 items-center">
    <div class="flex">
        <FloatingDate bind:value={trip.trip_date} label="Trip Date" />
    </div>

    <div class="flex mb-2 md:mb-0">
        <Toggle
            bind:value={bill_to_participant}
            on:change={() => updateBillToParticipant(!bill_to_participant)}
            label_on="Billable"
            label_off="Billable"
            dispatchEvent={true}
        />
    </div>
</div>

{#if bill_to_participant}
    <ClientSelector
        bind:client_id={trip.client_id}
        on:change={handleParticipantChange}
    />
{/if}

{#if trip.client_id != null}
    {#if bill_to_participant}
        <div
            class="bg-white px-3 pt-2 pb-4 mb-2 border border-indigo-100 rounded-md"
        >
            <div class="text-xs opacity-50 mb-2">Billing Item</div>
            {#if !trip.service_booking_id}<div>
                    Which support item should be billed for this travel?
                </div>{/if}
            <ProviderTravelRadioGroup
                bind:this={providerTravelComponent}
                on:change={handleChange}
                bind:client_id={trip.client_id}
                bind:service
                bind:service_booking_id={trip.service_booking_id}
                bind:support_item_count
            />
        </div>
    {/if}
{/if}

{#if support_item_count == 0 && trip.client_id}
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg
                    class="h-5 w-5 text-red-400"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-red-800">
                    Provider Travel is not available for this participant.
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    Provider Travel needs to be enabled for one of the
                    participant's support items.
                </div>
            </div>
        </div>
    </div>
{:else}
    {#if trip.service_booking_id != null}
        {#if bill_to_participant}
            <Role roles={["accounts"]}>
                <PlanManagerSelector
                    bind:planmanager_id={trip.planmanager_id}
                />
            </Role>
        {/if}
    {/if}

    {#if trip.service_booking_id != null || bill_to_participant == false}
        <div
            class="bg-white px-3 pt-2 pb-4 mb-2 border border-indigo-100 rounded-md"
        >
            <div class="text-xs opacity-50 mb-2">Vehicle Type</div>
            <RadioButtonGroup
                options={VEHICLE_OPTIONS}
                bind:value={trip.vehicle_type}
            />
        </div>

        <FloatingInput
            label="Kilometres Travelled (kms)"
            inputmode="numeric"
            inputmask="xxx"
            placeholder="eg: 100"
            bind:value={trip.kms}
        />

        <FloatingDurationSelect
            label="Travel Duration"
            bind:value={trip.trip_duration}
        />

        {#if isSpeedAcceptable == false}
            <div class="rounded-md bg-indigo-50 p-2 mb-2">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg
                            class="h-5 w-5 text-indigo-500"
                            data-slot="icon"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path
                                clip-rule="evenodd"
                                fill-rule="evenodd"
                                d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z"
                            ></path>
                        </svg>
                    </div>
                    <div class="ml-2 text-indigo-600 text-sm">
                        <div>
                            <span class="font-bold">Remember:</span> Only charge
                            for the
                            <span class="italic underline"
                                >actual travel time</span
                            >
                            spent during support services.
                        </div>
                        <div class="text-xs">
                            This ensures fair billing and transparency in using
                            NDIS funds.
                        </div>
                    </div>
                </div>
            </div>
        {/if}
        <TravelPurpose
            bind:this={travelPurpose}
            bind:selectedValue={trip.trip_purpose}
        />
    {/if}
{/if}
