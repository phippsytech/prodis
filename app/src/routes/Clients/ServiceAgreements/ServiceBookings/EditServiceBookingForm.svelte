<script>
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
  import ServiceCombo from "@app/routes/Service/ServiceCombo.svelte";
  import PlanManagerCombo from "@app/routes/Accounts/PlanManagers/PlanManagerCombo.svelte";

  import { formatCurrency } from "@shared/utilities.js";

  export let props = {};

  // Defaulting max_claimable_travel_duration to 30 if it's not already set
  if (props.max_claimable_travel_duration === undefined) {
    props.max_claimable_travel_duration = 30;
  }

  let services = [];

  let selectedServiceName = null;
  let selectedServiceRecordTravelledKilometers = false;

  $: console.log("props", props);

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

  $: {
    if (props.mode == "add") {
      props.allocated_funding = props.budget;
    }
  }
</script>

<div
  class="flex justify-between gap-x-2 bg-slate-50 border border-indigo-100 p-2 pt-3 px-4 rounded-md"
>
  <div>
    <div class="text-xs text-slate-400">Service</div>
    <div class="text-lg">{props.code}</div>
  </div>

  <div>
    <div class="text-xs text-slate-400">Service Rate (per unit)</div>
    <div class="text-lg">{formatCurrency(props.rate)}</div>
  </div>

  <div>
    <div class="text-xs text-slate-400">Budget</div>
    <div class="text-lg">{formatCurrency(props.budget)}</div>
  </div>
</div>

<div class="flex justify-between gap-x-2 p-2 pt-3 px-4 rounded-md">
  <div>
    <div class="text-xs text-slate-400">Spent Budget</div>
    <div class="text-lg">{formatCurrency(props.spent)}</div>
  </div>

  <div>
    <div class="text-xs text-slate-400">Remaining Budget</div>
    <div class="text-lg">{formatCurrency(props.remainingBudget)}</div>
  </div>
</div>

{#if props.mode == "update"}
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

{#if props.include_travel}
  <div class="flex justify-between gap-x-2">
    <FloatingInput
      bind:value={props.kilometer_budget}
      label="Travel KM Budget"
      placeholder="eg: 769"
    />
    <FloatingInput
      bind:value={props.max_claimable_travel_duration}
      label="Maximum Claimable Travel Duration (minutes)"
      readOnly={true}
    />
  </div>
{/if}

<PlanManagerCombo bind:value={props.plan_manager_id} />

<div class="flex justify-between gap-x-2 mx-4">
  <div class="flex items-center mb-2">
    <input
      type="checkbox"
      id="isActive"
      bind:checked={props.is_active}
      class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
    />
    <label for="isActive" class="ml-2 text-sm font-medium text-gray-900"
      >Active</label
    >
  </div>

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
</div>
