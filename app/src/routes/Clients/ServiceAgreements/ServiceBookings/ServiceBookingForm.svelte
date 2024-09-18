<script>
    // import { onMount } from 'svelte';
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import PayerWidget from "@app/routes/Accounts/PlanManagers/PayerWidget.svelte";
    import { jspa } from "@shared/jspa.js";

    export let props = {};

    // Defaulting max_claimable_travel_duration to 30 if it's not already set
    if (props.max_claimable_travel_duration === undefined) {
        props.max_claimable_travel_duration = 30;
    }

    let services = [];
    let serviceList = [];
    let selectedServiceName = null;
    let selectedServiceRecordTravelledKilometers = false;
    // let enableProviderTravel = false;

    // onMount(async ()=>{
    jspa("/Service", "listServices", {}).then(
        (result) => (services = result.result),
    );
    // });

    // get Service List for Select
    $: {
        services.forEach((service) => {
            if (service.archived != 1)
                serviceList.push({ option: service.code, value: service.id });
        });
        serviceList = serviceList;
    }

    $: {
        if (props) {
            let serviceIndex = services.findIndex(
                (element) => element.id == props.service_id,
            );
            if (serviceIndex > -1) {
                selectedServiceName = services[serviceIndex].name;
                if (services[serviceIndex].record_travelled_kilometers == "1") {
                    selectedServiceRecordTravelledKilometers = true;
                } else {
                    selectedServiceRecordTravelledKilometers = false;
                }
            }
        }
    }
</script>

<div class="flex items-center mb-2">
    <input
        type="checkbox"
        id="isActive"
        bind:checked={props.is_active}
        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
    />
    <label for="isActive" class="ml-2 text-sm font-medium text-gray-900"
        >Active Service</label
    >
</div>

<div class="flex justify-between gap-x-2">
    <div class="flex-grow">
        <FloatingSelect
            bind:value={props.service_id}
            label="Service"
            instruction="Choose service"
            options={serviceList}
            hideValidation={true}
        />
    </div>
    <div class="flex-shrink">
        <FloatingInput
            bind:value={props.rate}
            label="Service Rate (per unit)"
            placeholder="eg: 193.99"
        />
    </div>
</div>

<FloatingInput
    bind:value={props.allocated_funding}
    label="Total Allocated Funding"
    placeholder="eg: 12000.00"
/>

<div class="flex justify-between gap-x-2">
    <div class="flex-grow">
        <FloatingInput
            bind:value={props.budget}
            label="Current Service Budget"
            placeholder="eg: 12000"
        />
    </div>
    <div class="flex-grow">
        <FloatingDate
            bind:value={props.budget_start_date}
            label="As of Date"
            placeholder="eg: 2021-01-01"
        />
    </div>
</div>

<!-- <FloatingInput
    bind:value={props.xero_account_code}
    label="Xero Account Code"
    placeholder="eg: 200"
/> -->

{#if props.budget_display == "weekly"}
    <div class="flex items-center mb-2">
        <input
            type="checkbox"
            id="adjustWeeklyTime"
            bind:checked={props.adjust_weekly_time}
            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
        />
        <label
            for="adjustWeeklyTime"
            class="ml-2 text-sm font-medium text-gray-900"
            >Calculate weekly time using remaining funds.</label
        >
    </div>
{/if}

<div class="flex items-center mb-2">
    <input
        type="checkbox"
        id="includeTravel"
        bind:checked={props.include_travel}
        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
    />
    <label for="includeTravel" class="ml-2 text-sm font-medium text-gray-900"
        >Enable Provider Travel</label
    >
</div>

{#if props.include_travel}
    <FloatingInput
        bind:value={props.kilometer_budget}
        label="Travel KM Budget"
        placeholder="eg: 769"
    />
    <FloatingInput
        bind:value={props.max_claimable_travel_duration}
        label="Maximum Claimable Travel Duration (minutes)"
        placeholder="eg: 30"
    />
{/if}

<PayerWidget bind:service={props} />
