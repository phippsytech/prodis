<script>
  import ServiceCombo from "@app/routes/Service/ServiceCombo.svelte";
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
  import PlanManagerCombo from "@app/routes/Accounts/PlanManagers/PlanManagerCombo.svelte";
  import Container from "@shared/Container.svelte";
  import { slide } from "svelte/transition";
  import { PlusIcon, XMarkIcon } from "heroicons-svelte/24/outline";
  import Role from "@shared/Role.svelte";

  import IconMenu from "@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte";
  import {
    EnvelopeIcon,
    ArchiveBoxIcon,
    DocumentTextIcon,
    DocumentArrowUpIcon,
    TrashIcon,
    UserIcon,
  } from "heroicons-svelte/24/outline";

  import FileSignatureIcon from "@shared/PhippsyTech/svelte-ui/icons/FileSignature.svelte";
  import FileContractIcon from "@shared/PhippsyTech/svelte-ui/icons/FileContract.svelte";
  import { CloudArrowUpIcon } from "heroicons-svelte/24/outline";
  import FileUploader from "@shared/FileUploader.svelte";

  export let displayServiceAgreement = false;

  export let props = {};

  let addedRows = [
    {
      service_name: "SBIS",
      rate: 244.22,
      budget: 20000,
      plan_manager_name: "National Disability Insurance Agency",
    },
    {
      service_name: "SBIS",
      rate: 244.22,
      budget: 20000,
      plan_manager_name: "National Disability Insurance Agency",
    },
    {
      service_name: "SBIS",
      rate: 244.22,
      budget: 20000,
      plan_manager_name: "National Disability Insurance Agency",
    },
  ];

  function hideGenerator() {
    displayServiceAgreement = false;
  }
</script>

{#if displayServiceAgreement}
  <ul
    class=" bg-white rounded-xl border border-b border-indigo-600 text-slate-800 mb-4"
  >
    <li
      class=" text-sm rounded-t-md group flex justify-between items-center px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full"
    >
      <div class="flex justify-between items-center w-full gap-x-2 font-bold">
        <span>Draft: 9 May 2024 - 29 Sept 2024</span>
        <a
          class="flex p-1 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300 cursor-pointer"
          on:click={() => hideGenerator()}
        >
          <XMarkIcon class="h-4 w-4 inline m-0 p-0" />
        </a>
      </div>
    </li>

    <li
      class="rounded-t-lg group items-center px-2 pt-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500
border-b border-indigo-100 w-full gap-x-2
"
    >
      <div class="flex flex-wrap space-x-2 items-center md:flex-no-wrap">
        <FloatingDate
          bind:value={props.service_agreement_signed_date}
          label="Start Date"
        />

        <FloatingDate
          bind:value={props.service_agreement_end_date}
          label="End Date"
        />
      </div>
    </li>
    <li
      class="rounded-t-lg group flex justify-between items-center px-2 pt-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500
border-b border-indigo-100 w-full gap-x-2
"
    >
      <div class="flex justify-between items-center w-full gap-x-2">
        <div class="flex-grow">
          <div class="grid grid-cols-12 gap-x-2 items-center w-full">
            <div class="col-span-3">
              <ServiceCombo bind:value={props.service_id} />
            </div>
            <div class="col-span-2">
              <FloatingInput
                bind:value={props.rate}
                label="Rate"
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
            <div class="col-span-5">
              <PlanManagerCombo bind:value={props.plan_manager_id} />
            </div>
          </div>
        </div>

        <div class="flex-shrink">
          <a
            class="flex justify-center items-center rounded-md bg-indigo-50 w-8 h-8 text-center text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-600 hover:text-white cursor-pointer"
          >
            <PlusIcon class="h-5 w-5 inline-block" />
          </a>
        </div>
      </div>
    </li>

    {#each addedRows as row, index (index)}
      <li
        in:slide={{ duration: 200 }}
        out:slide={{ duration: 200 }}
        class="bg-white group flex justify-between items-center hover:bg-indigo-50 px-2 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full
  gap-x-2"
      >
        <div class="flex-grow">
          <div class="grid grid-cols-12 gap-x-2 items-center w-full">
            <div class="col-span-3 px-2">
              {row.service_name}
            </div>
            <div class="col-span-2 px-2">
              {row.rate}
            </div>
            <div class="col-span-2 px-2">
              {row.budget}
            </div>
            <div class="col-span-5 px-2">
              {row.plan_manager_name}
            </div>
          </div>
        </div>

        <div class="flex-shrink">
          <a
            class="flex p-1 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300 cursor-pointer"
            on:click={() => removeRow(row)}
          >
            <XMarkIcon class="h-4 w-4 inline m-0 p-0" />
          </a>
        </div>
      </li>
    {/each}
    <li
      class="bg-white rounded-b-xl group flex justify-between items-center px-2 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full
gap-x-2"
    >
      <div
        class="w-full flex justify-end
"
      >
        <button
          on:click={() => addService()}
          type="button"
          class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >Generate</button
        >
      </div>
    </li>
  </ul>
{/if}
