<script>
  import ServiceCombo from "@app/routes/Service/ServiceCombo.svelte";
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
  import InlineDate from "@shared/PhippsyTech/svelte-ui/forms/InlineDate.svelte";
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
    // {
    //   service_name: "SBIS",
    //   rate: 244.22,
    //   budget: 20000,
    //   plan_manager_name: "National Disability Insurance Agency",
    // },
    // {
    //   service_name: "SBIS",
    //   rate: 244.22,
    //   budget: 20000,
    //   plan_manager_name: "National Disability Insurance Agency",
    // },
    // {
    //   service_name: "SBIS",
    //   rate: 244.22,
    //   budget: 20000,
    //   plan_manager_name: "National Disability Insurance Agency",
    // },
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
      class="rounded-t-lg group items-center px-2 pt-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500
  border-b border-indigo-100 w-full gap-x-2
  "
    >
      <div class="text-indigo-600 px-2 pb-2 font-bold">
        Draft Service Agreement
      </div>

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
      class="rounded-t-lg group items-center px-2 pt-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500
  border-b border-indigo-100 w-full gap-x-2
  "
    >
      <div class="px-2 pb-2 text-xs">
        Participant's Representative (Signatory)
      </div>

      <div class="flex gap-x-2 flex-wrap">
        <div class="w-full sm:w-2/5 sm:flex-1">
          <FloatingInput
            bind:value={props.name}
            label="Representative's Full Name"
            placeholder="eg: Chris Person"
          />
        </div>
        <div
          class="flex flex-wrap xs:flex-nowrap gap-2 justify-between w-full sm:w-3/5"
        >
          <div class="flex-grow">
            <FloatingInput
              bind:value={props.ndis_plan_end_date}
              label="Email Address"
              placeholder="eg: chris@person.com"
            />
          </div>
          <div class="flex-grow-0">
            <FloatingInput
              bind:value={props.ndis_plan_end_date}
              label="Contact Number"
              placeholder="eg: 04XX XXX XXX"
            />
          </div>
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
    {:else}
      <li
        class=" px-3 py-2 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default text-sm"
      >
        No Services have been added to this draft agreement yet.
      </li>
    {/each}
    <li
      class="bg-white group flex justify-between items-center px-2 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full
    gap-x-2"
    >
      <Role roles={["admin"]}>
        <button
          class="text-xs text-indigo-600 hover:underline text-left mx-2"
          on:click={() => (displayServiceAgreement = true)}
        >
          <PlusIcon class="w-4 h-4 inline" /> Add Service
        </button>
      </Role>
    </li>
    <li
      class="bg-white rounded-b-xl group flex justify-between items-center px-2 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 border-b border-indigo-100 w-full
  gap-x-2"
    >
      <div
        class="w-full flex justify-between
  "
      >
        <button
          on:click={() => hideGenerator()}
          type="button"
          class="w-full sm:w-auto sm:mr-3 inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
          >Cancel</button
        >
        <button
          on:click={() => addService()}
          type="button"
          class="w-full sm:w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
          >Generate</button
        >
      </div>
    </li>
  </ul>
{/if}
