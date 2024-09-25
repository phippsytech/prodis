<script>

import ServiceCombo from "@app/routes/Service/ServiceCombo.svelte";
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
  import PlanManagerCombo from "@app/routes/Accounts/PlanManagers/PlanManagerCombo.svelte";

  import { PlusIcon, XMarkIcon } from "heroicons-svelte/24/outline";


export let props={};

let addedRows=[
    {
    service_name: "SBIS",
    rate: 244.22,
    budget: 20000,
    plan_manager_name: "National",
  },
  {
    service_name: "SBIS",
    rate: 244.22,
    budget: 20000,
    plan_manager_name: "National",
  },
  {
    service_name: "SBIS",
    rate: 244.22,
    budget: 20000,
    plan_manager_name: "National",
  },
];

</script>









  <div class="space-y-4">
    <!-- Form row -->
    <div class="grid grid-cols-12 gap-x-2 items-center">
      <div class="col-span-3">
        <ServiceCombo bind:value={props.service_id} />
      </div>
      <div class="col-span-2">
        <FloatingInput
          bind:value={props.rate}
          label="Service Rate (per unit)"
          placeholder="eg: 193.99"
        />
      </div>
      <div class="col-span-2">
        <FloatingInput
          bind:value={props.budget}
          label="Funding"
          placeholder="eg: 12000"
        />
      </div>
      <div class="col-span-4">
        <PlanManagerCombo bind:value={props.plan_manager_id} />
      </div>
      <div class="col-span-1 text-center">
        <a
          class="flex justify-center items-center rounded-md bg-indigo-50 w-8 h-8 text-center text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-600 hover:text-white cursor-pointer"
        >
          <PlusIcon class="h-5 w-5 inline-block" />
        </a>
      </div>
    </div>
  
    <!-- Added rows -->
    {#each addedRows as row}
    <div class="grid grid-cols-12 gap-x-2 items-center">
      <div class="col-span-3 p-2">
        {row.service_name}
      </div>
      <div class="col-span-2 p-2">
        {row.rate}
      </div>
      <div class="col-span-2 p-2">
        {row.budget}
      </div>
      <div class="col-span-4 p-2">
        {row.plan_manager_name}
      </div>
      <div class="col-span-1 text-center">
        <a
          class="flex justify-center items-center rounded-md bg-red-50 w-8 h-8 text-center text-sm font-semibold text-red-600 shadow-sm hover:bg-red-600 hover:text-white cursor-pointer"
          on:click={() => removeRow(row)}
        >
          <XMarkIcon class="h-5 w-5 inline-block" />
        </a>
      </div>
    </div>
    {/each}
  </div>
  
  
  