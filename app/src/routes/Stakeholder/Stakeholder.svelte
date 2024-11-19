<script>
  import StaffCard from "./StaffCard.svelte";
</script>

<article>
  <!-- Profile header -->
  <div>
    <!-- <div>
        <img class="h-32 w-full object-cover lg:h-48" src="https://images.unsplash.com/photo-1444628838545-ac4016a5418a?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="">
      </div> -->

    <div class="flex">
      <!-- <img class="h-20 w-20 rounded-full ring-4 ring-white mr-4" src="https://images.unsplash.com/photo-1590341328520-63256eb32bc3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OXx8c3VwZXJoZXJvfGVufDB8fDB8fHww&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt=""> -->
      <div class="text-xs">
        <div
          class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
        >
          Logan Walker
        </div>
        <div><span class="text-xs text-gray-600">NDIS #</span>430018663</div>
        <div class="flex items-center">
          <svg
            class="h-3 w-3 inline text-gray-600"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"
            ></path>
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"
            ></path>
          </svg>
          18 Hodge Road, Para Hills
        </div>
        <div class="flex items-center">
          <svg
            class="h-3 w-3 inline text-gray-600"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.87c1.355 0 2.697.055 4.024.165C17.155 8.51 18 9.473 18 10.608v2.513m-3-4.87v-1.5m-6 1.5v-1.5m12 9.75l-1.5.75a3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0L3 16.5m15-3.38a48.474 48.474 0 00-6-.37c-2.032 0-4.034.125-6 .37m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.17c0 .62-.504 1.124-1.125 1.124H4.125A1.125 1.125 0 013 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 016 13.12M12.265 3.11a.375.375 0 11-.53 0L12 2.845l.265.265zm-3 0a.375.375 0 11-.53 0L9 2.845l.265.265zm6 0a.375.375 0 11-.53 0L15 2.845l.265.265z"
            ></path>
          </svg>

          18 Dec 2007
        </div>
      </div>
    </div>
  </div>

  <!-- Tabs -->

  <div class=" border-b border-gray-200">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
      <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <!-- Current: "border-pink-500 text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
        <a
          href="/#/"
          class="border-indigo-600 text-gray-900 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium"
          aria-current="page">Staff</a
        >
        <a
          href="/#/"
          class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium"
          aria-current="page">Timeline</a
        >

        <a
          href="/#/"
          class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium"
          >Calendar</a
        >
        <a
          href="/#/"
          class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium"
          >Billing</a
        >
      </nav>
    </div>
  </div>

  <!-- Therapist list -->
  <div class="mx-auto mt-4 max-w-5xl px-4 pb-4 sm:px-6 lg:px-8">
    <!-- <h2 class="text-sm font-medium text-gray-500">Staff</h2> -->

    <div
      class="mt-1 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
    >
      <StaffCard staffer={{ name: "Chloe Stockdale" }} />

      <StaffCard staffer={{ name: "Dean" }} />
      <StaffCard staffer={{ name: "Hannah" }} />
      <StaffCard staffer={{ name: "Owen" }} />
      <StaffCard staffer={{ name: "Robert" }} />
    </div>
  </div>
</article>
