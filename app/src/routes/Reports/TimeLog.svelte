<script>
  import Container from "@shared/Container.svelte";
  import { jspa } from "@shared/jspa.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import {
    getMonday,
    getDatePlus7Days,
    formatDate,
  } from "@shared/utilities.js";

  let times = [];
  let start_date = getMonday();
  let end_date = getDatePlus7Days(start_date);

  BreadcrumbStore.set({
    path: [
      { url: "/reports", name: "Reports" },
      { url: null, name: "TimeLog Report" },
    ],
  });

  $: {
    if (start_date && end_date) {
      const startDateRegex = /^\d{4}-\d{2}-\d{2}$/;
      const endDateRegex = /^\d{4}-\d{2}-\d{2}$/;

      if (!startDateRegex.test(start_date) || !endDateRegex.test(end_date)) {
        // Display console.log("Invalid date format");
      } else {
        times = [];
        jspa("/TimeLog", "listTimes", {
          start_date: start_date,
          end_date: end_date,
        }).then((result) => {
          times = result.result;
        });
      }
    }
  }
</script>

<div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  TimeLog Report
</div>

<Container>
  <div class="text-sm mb-1 font-bold opacity-50">Period</div>
  <div
    class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 items-center"
  >
    <div class="flex-grow">
      <div
        class="bg-white w-full rounded-lg py-1 p-2 border border-gray-200 mb-2"
      >
        <label class="text-xs opacity-50 p-0 m-0 block">Start Date</label>
        <input
          type="date"
          class="w-full p-0 m-0 outline-none bg-white"
          bind:value={start_date}
        />
      </div>
    </div>

    <div class="flex-grow">
      <div
        class="bg-white w-full rounded-lg py-1 p-2 border border-gray-200 mb-2"
      >
        <label class="text-xs opacity-50 p-0 m-0 block">End Date</label>
        <input
          type="date"
          class="w-full p-0 m-0 outline-none bg-white"
          bind:value={end_date}
        />
      </div>
    </div>
  </div>
</Container>

<section class="mt-12">
  <ol class="mt-2 divide-y divide-gray-200 text-sm leading-6 text-gray-500">
    {#each times as time}
      <li class="py-4 sm:flex">
        <time datetime={time.start_date} class="w-28 flex-none"
          >{formatDate(time.start_date)}</time
        >
        <div class="w-28 flex-none">
          <span class="font-semibold">{time.staff_name}</span>
          {#if time.client_name}
            <br /><span class="text-xs">{time.client_name}</span>
          {/if}
        </div>

        {#if time.description}
          <p class="mt-2 flex-auto text-gray-900 sm:mt-0">
            {time.description}
          </p>
        {:else}
          <p class="mt-2 flex-auto font-light text-gray-500 sm:mt-0">No note</p>
        {/if}
        <p class="flex-none sm:ml-6">
          {time.start_time} - {time.end_time}
        </p>
      </li>
    {:else}
      <li class="py-4 sm:flex">
        <p class="mt-2 flex-auto sm:mt-0">Nothing on today’s schedule</p>
      </li>
    {/each}
  </ol>
</section>
