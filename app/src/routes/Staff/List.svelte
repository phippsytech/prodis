<script>
  import { push } from "svelte-spa-router";
  import { onMount } from "svelte";
  import { jspa } from "@shared/jspa.js";
  import Badge from "@shared/PhippsyTech/Tailwind/App/Elements/Badge.svelte";

  let staff = [];
  let staff_list = [];
  let showArchived = false;
  export let search = "";

  onMount(() => {
    jspa("/Staff", "listStaff", {}).then((result) => {
      staff = result.result;

      staff.sort(function (a, b) {
        if (a.staff_name == null) return -1;
        if (b.staff_name == null) return 1;

        const nameA = a.staff_name.toUpperCase(); // ignore upper and lowercase
        const nameB = b.staff_name.toUpperCase(); // ignore upper and lowercase
        if (nameA < nameB) return -1;
        if (nameA > nameB) return 1;
        return 0; // names must be equal
      });
    });
  });

  $: {
    if (!showArchived) {
      staff_list = staff.filter((staffer) => staffer.archived != 1);
    } else {
      staff_list = staff;
    }

    if (search.length > 0) {
      // filter by client name
      // there is a problem which will mean if client.name is removed it will effectively hide the record
      staff_list = staff_list.filter(
        (staffer) =>
          staffer.staff_name &&
          staffer.staff_name.toLowerCase().includes(search.toLowerCase()) ==
            true,
      );
    }
  }

  function handle(staff_id) {
    staff_id = staff_id;
    push(`/staff/${staff_id}`);
  }
</script>

<label class="text-xs text-gray-400 px-6 flex justify-end">
  <input type="checkbox" bind:checked={showArchived} class="mr-2" />
  Include archived
</label>

<!-- <h2 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight pt-5 px-5">Staff</h2> -->
<ul role="list" class="divide-y divide-gray-100">
  {#each staff_list as staffer}
    <li
      class="relative flex justify-between py-2 px-5 text-gray-700 hover:text-indigo-600 hover:bg-gray-50 cursor-pointer"
    >
      <div on:click={() => handle(staffer.id)} class="min-w-0 flex-auto">
        <p
          class="text-sm font-semibold leading-6 {staffer.archived
            ? 'opacity-50'
            : ''}"
        >
          <span class="absolute inset-x-0 -top-px bottom-0"></span>

          {staffer.staff_name}

          {#if staffer.groups}
            {#each staffer.groups as group}<Badge label={group} />{/each}
          {/if}
        </p>
      </div>

      <div class="flex items-center justify-end gap-x-4 flex-none">
        <svg
          class="h-5 w-5 flex-none text-gray-400"
          viewBox="0 0 20 20"
          fill="currentColor"
          aria-hidden="true"
        >
          <path
            fill-rule="evenodd"
            d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
    </li>
  {/each}
</ul>
