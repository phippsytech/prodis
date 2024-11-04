<script>
  import { jspa } from "@shared/jspa.js";
  import List from "./List.svelte";

  import { BreadcrumbStore } from "@shared/stores.js";

  document.title = "Staff Directory";
  BreadcrumbStore.set({
    path: [{ url: null, name: "Staff Directory" }],
  });

  let staff = [];
  let search = "";

  jspa("/Staff", "listStaff", {}).then((result) => {
    staff = result.result;
  });

  // Handle display of archived staff
  let showArchived = false;
  let staffList = [];
  $: {
    staffList = staff.filter(
      (staffer) =>
        staffer.staff_name.toLowerCase().includes(search.toLowerCase()) == true
    );
    if (!showArchived)
      staffList = staffList.filter((staffer) => staffer.archived != 1);

    // sort names alphabetically
    staffList.sort(function (a, b) {
      const nameA = a.staff_name.toUpperCase(); // ignore upper and lowercase
      const nameB = b.staff_name.toUpperCase(); // ignore upper and lowercase
      if (nameA < nameB) return -1;
      if (nameA > nameB) return 1;
      return 0; // names must be equal
    });
  }
</script>

<h2 class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  Staff Directory
</h2>

<div
  class="flex grow flex-col gap-y-2 overflow-y-auto border-r border-gray-200 bg-white"
>
  <div class="flex h-16 shrink-0 items-center">
    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-2 px-6">
      <div class="relative flex flex-1">
        <label for="search-field" class="sr-only">Search</label>
        <svg
          class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400"
          viewBox="0 0 20 20"
          fill="currentColor"
          aria-hidden="true"
        >
          <path
            fill-rule="evenodd"
            d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
            clip-rule="evenodd"
          />
        </svg>
        <input
          bind:value={search}
          id="search-field"
          class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm outline-none"
          placeholder="search by staff name..."
          type="search"
          name="search"
        />
      </div>
    </div>
  </div>

  <List bind:search />
</div>
