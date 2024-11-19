<script>
  import Container from "@shared/Container.svelte";
  import { jspa } from "@shared/jspa.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import { STAFF_ROLES } from "@shared/constants.js";
  import { slide } from "svelte/transition";
  import { XMarkIcon, PlusIcon } from "heroicons-svelte/24/outline";

  BreadcrumbStore.set({
    path: [
      { url: "/settings", name: "Settings" },
      { url: null, name: "Report Settings" },
    ],
  });
</script>

<div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  Staff Settings
</div>

<Container>
  <h3 class="text-slate-800 text-1xl font-bold mt-0">Staff Roles</h3>
  <p class="mb-2 text-sm text-slate-500">
    These staff roles are hard coded into the system for now. If you need
    additional staff roles please let the developer know.
  </p>
  <!-- <div class="flex justify-between gap-2 items-stretch">
        <div class="flex-grow">
            <FloatingInput
                bind:value={staff_role.name}
                label="Report Type"
                placeholder="eg: Interim Report"
            />
        </div>

        <button
            class="flex items-center justify-center mb-2 block rounded-md bg-indigo-600 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >
            <PlusIcon class="h-5 w-5 inline-block" />
        </button>
    </div> -->

  {#if STAFF_ROLES.length > 0}
    <ul
      class="bg-white rounded-lg border border-indigo-100 w-full text-gray-900"
    >
      {#each STAFF_ROLES as staff_role, index (index)}
        <li
          in:slide={{ duration: 200 }}
          out:slide={{ duration: 200 }}
          class="flex justify-between hover:bg-indigo-50 px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-gray-600 transition duration-500 {STAFF_ROLES.length -
            1 ==
          index
            ? 'rounded-b-lg'
            : ''}border-b border-indigo-100 w-full"
        >
          <div>{staff_role.option}</div>
        </li>
      {/each}
    </ul>
  {/if}
</Container>
