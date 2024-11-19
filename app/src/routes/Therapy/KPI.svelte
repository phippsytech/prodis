<script>
  import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
  import KPIChart from "@app/routes/Reports/KPI/KPIChart.svelte";
  import KPIBillingItems from "@app/routes/Reports/KPI/KPIBillingItems.svelte";
  import { jspa } from "@shared/jspa.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { getMonday, getDatePlus7Days } from "@shared/utilities.js";

  const date = new Date();

  let staff = [];
  let start_date = getMonday();
  let end_date = getDatePlus7Days(start_date);

  BreadcrumbStore.set({
    path: [
      { url: "/therapy", name: "Therapy" },
      { url: null, name: "KPI Report" },
    ],
  });

  $: {
    if (start_date && end_date) {
      const startDateRegex = /^\d{4}-\d{2}-\d{2}$/;
      const endDateRegex = /^\d{4}-\d{2}-\d{2}$/;

      if (!startDateRegex.test(start_date) || !endDateRegex.test(end_date)) {
        // Display an error or handle the invalid date format here
        console.log("Invalid date format");
      } else {
        staff = [];
        jspa("/Staff", "getStaffKPI", {
          start_date: start_date,
          end_date: end_date,
        }).then((result) => {
          staff = result.result;
        });
      }
    }
  }

  function select(id) {
    staff.forEach((staffMember, index) => {
      if (staffMember.id !== id) {
        staff[index].isExpanded = false;
      } else {
        staff[index].isExpanded = !staff[index].isExpanded;
      }
    });
  }
</script>

<div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  KPI Report for period
</div>

<div class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 mb-4">
  <div class="flex-grow">
    <FloatingDate label="Start Date" bind:value={start_date} />
    <FloatingDate label="End Date" bind:value={end_date} />
  </div>
</div>

{#each staff as staffer}
  <div
    on:click={() => select(staffer.id)}
    class="flex justify-between items-center mt-4 cursor-pointer"
  >
    <div class="w-full">
      <KPIChart {staffer} />
    </div>
    <div></div>
  </div>
  {#if staffer.isExpanded}
    <KPIBillingItems staff_id={staffer.id} bind:start_date bind:end_date />
    <br />
  {/if}
{:else}
  No timesheet data is available for this period
{/each}
