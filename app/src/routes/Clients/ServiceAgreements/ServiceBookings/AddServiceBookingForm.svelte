<script>
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
  import ServiceCombo from "@app/routes/Service/ServiceCombo.svelte";
  import PlanManagerCombo from "@app/routes/Accounts/PlanManagers/PlanManagerCombo.svelte";

  export let props = {};

  // Defaulting max_claimable_travel_duration to 30 if it's not already set
  if (props.max_claimable_travel_duration === undefined) {
    props.max_claimable_travel_duration = 30;
  }

  let services = [];

  let selectedServiceName = null;
  let selectedServiceRecordTravelledKilometers = false;

  $: {
    if (props) {
      let serviceIndex = services.findIndex(
        (element) => element.id == props.service_id
      );
      if (serviceIndex > -1) {
        selectedServiceName = services[serviceIndex].name;
        if (services[serviceIndex].record_travelled_kilometers == "1") {
          selectedServiceRecordTravelledKilometers = true;
        } else {
          selectedServiceRecordTravelledKilometers = false;
        }
        props.rate = services[serviceIndex].rate;
      }
    }
  }

  // $: {
  //   if (props.mode == "add") {
  //     props.allocated_funding = props.budget;
  //   }
  // }
</script>

<div class="flex justify-between gap-x-2">
  <div class="flex-grow">
    <ServiceCombo bind:value={props.service_id} />
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

<!-- {#if props.mode == "update"}
  <div class="flex justify-between gap-x-2">
    <div class="flex-grow">
      <FloatingInput
        bind:value={props.budget}
        label="Remaining Funding"
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
{/if} 

{#if props.mode == "add"}
  <div class="flex justify-between gap-x-2">
    <div class="flex-grow">
      <FloatingInput
        bind:value={props.budget}
        label="Funding"
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
{/if}
-->
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
    <label for="adjustWeeklyTime" class="ml-2 text-sm font-medium text-gray-900"
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

<PlanManagerCombo bind:value={props.plan_manager_id} />
