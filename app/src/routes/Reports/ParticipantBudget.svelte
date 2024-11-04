<script>
  import { onMount } from "svelte";
  import SortableColumn from "./SortableColumn.svelte";
  import Container from "@shared/Container.svelte";
  import JSON2CSV from "@shared/JSON2CSV.svelte";
  import { jspa } from "@shared/jspa.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import {
    formatCurrency,
    getDaysUntilDate,
    formatDate,
    convertMinutesToHoursAndMinutes,
  } from "@shared/utilities.js";

  BreadcrumbStore.set({
    path: [
      { url: "/reports", name: "Reports" },
      { url: null, name: "Participant Budget" },
    ],
  });

  let clients = [];
  let sum_of_remaining = 0;
  let sum_of_budget = 0;
  let sum_of_total_billed = 0;
  let sortColumn = "ndis_plan_end_date"; // Default sort column
  let sortDirection = "desc"; // Default sort direction

  onMount(async () => {
    const result = await jspa("/Report", "getParticipantBudget", {});
    clients = result.result;
    sum_of_remaining = calculateTotal("remaining");
    sum_of_total_billed = calculateTotal("total_billed");
    sum_of_budget = calculateTotal("budget");
    sortClients(sortColumn);
  });

  function calculateTotal(key) {
    return clients.reduce(
      (total, client) => total + parseFloat(client[key]),
      0
    );
  }

  function sortClients(column) {
    if (sortColumn === column) {
      sortDirection = sortDirection === "asc" ? "desc" : "asc";
    } else {
      sortColumn = column;
      sortDirection = "asc";
    }

    clients = [...clients].sort((a, b) => {
      let comparison = 0;

      if (column === "rate" || column === "budget" || column === "remaining") {
        // Currency or numeric sort
        comparison = parseFloat(a[column]) - parseFloat(b[column]);
      } else {
        // String or character sort
        comparison = a[column].localeCompare(b[column]);
      }

      return sortDirection === "asc" ? comparison : -comparison;
    });
  }

  function handleSort(event) {
    const { column } = event.detail;
    sortClients(column);
  }
</script>

<div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  Participant Budget Report
</div>

<div>
  <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3 mb-4">
    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
      <dt class="truncate text-sm font-medium text-gray-500">
        Combined Budget Total
      </dt>
      <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
        {formatCurrency(sum_of_budget)}
      </dd>
    </div>
    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
      <dt class="truncate text-sm font-medium text-gray-500">Spent</dt>
      <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
        {formatCurrency(sum_of_total_billed)}
      </dd>
    </div>
    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
      <dt class="truncate text-sm font-medium text-gray-500">Available</dt>
      <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
        {formatCurrency(sum_of_remaining)}
      </dd>
    </div>
  </dl>
</div>

<JSON2CSV
  filename="participantbudgets.csv"
  bind:json_data={clients}
  label="Export results to CSV"
/>

<div class="hidden xs:block sticky-header sticky top-14 bg-white z-10">
  <div
    class="grid grid-cols-7 items-center py-1 text-xs font-medium text-gray-500"
    style="grid-template-columns: 2fr 2fr 1fr 1fr 1fr 1fr 1fr;"
  >
    <SortableColumn
      column="name"
      label="Participant"
      {sortColumn}
      {sortDirection}
      on:sort={handleSort}
    />

    <SortableColumn
      column="ndis_plan_end_date"
      label="Plan Expires"
      {sortColumn}
      {sortDirection}
      on:sort={handleSort}
    />

    <SortableColumn
      column="code"
      label="Service"
      {sortColumn}
      {sortDirection}
      on:sort={handleSort}
    />

    <SortableColumn
      column="rate"
      label="Rate"
      {sortColumn}
      {sortDirection}
      on:sort={handleSort}
    />

    <SortableColumn
      column="budget"
      label="Budget"
      {sortColumn}
      {sortDirection}
      on:sort={handleSort}
    />

    <SortableColumn
      column="remaining"
      label="Remaining Budget"
      {sortColumn}
      {sortDirection}
      on:sort={handleSort}
    />

    <SortableColumn
      column="available_time"
      label="Available Time"
      {sortColumn}
      {sortDirection}
      on:sort={handleSort}
    />
  </div>
</div>

{#each clients as client}
  <div
    class="grid grid-cols-7 gap-4 items-center hover:text-indigo-600 hover:bg-indigo-50 border-t border-gray-200 py-2 px-2"
    style="grid-template-columns: 2fr 2fr 1fr 1fr 1fr 1fr 1fr;"
  >
    <div class="font-semibold">
      <a class="text-indigo-600" href="/#/clients/{client.id}">{client.name}</a>
    </div>
    <div>
      {formatDate(client.ndis_plan_end_date)}
      <span class="text-xs"
        >({getDaysUntilDate(client.ndis_plan_end_date)})</span
      >
    </div>
    <div>{client.code}</div>
    <div>{formatCurrency(client.rate)}</div>
    <div>{formatCurrency(client.budget)}</div>
    <div>{formatCurrency(client.remaining)}</div>
    <div>
      {@html convertMinutesToHoursAndMinutes(
        Math.round((client.remaining / client.rate) * 60)
      )}
    </div>
  </div>
{/each}

<style>
  .sortable-column {
    cursor: pointer;
    display: flex;
    align-items: center;
  }

  .sortable-column svg {
    width: 1em;
    height: 1em;
    margin-left: 0.25rem;
  }
</style>
