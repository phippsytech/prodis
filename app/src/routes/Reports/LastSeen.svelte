<script>
  import Container from "@shared/Container.svelte";
  import { slide } from "svelte/transition";
  import { jspa } from "@shared/jspa.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import {
    formatCurrency,
    getDaysUntilDate,
    formatDate,
    convertMinutesToHoursAndMinutes,
  } from "@shared/utilities.js";
  import { fade } from "svelte/transition";

  let clients = [];
  let reportList = [];

  let staffClients = [];

  BreadcrumbStore.set({
    path: [
      { url: "/reports", name: "Reports" },
      { url: null, name: "Last Seen" },
    ],
  });

  jspa("/Report", "getLastSeen", {}).then((result) => {
    clients = result.result;

    clients.sort(function (a, b) {
      return b.days_ago - a.days_ago;
    });

    staffClients = clients.reduce((acc, item) => {
      if (!acc[item.staff_name]) {
        acc[item.staff_name] = [];
      }
      acc[item.staff_name].push({
        id: item.client_id,
        name: item.client_name,
        days_ago: item.days_ago,
      });
      return acc;
    }, {});
  });
  function getWidth(days_ago) {
    let percentage = (Math.abs(days_ago) / 90) * 100;
    if (percentage > 100) {
      percentage = 100;
    }
    return percentage;
  }

  function getBackgroundColor(days_ago) {
    if (days_ago > 14) {
      // I want to use this color #f96743 instead of red.
      return "bg-red-600";
    }
    return "bg-indigo-600";
  }
</script>

<h1 class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  Last Seen
</h1>

<p>
  A summary showing the last time therapists provided direct service (face to
  face) services to participants.
</p>

{#each Object.entries(staffClients) as [staffName, clients]}
  <!-- <h2 class="font-bold">{staffName}</h2> -->

  <div class="pt-2 mt-2">
    <div in:slide|global={{ duration: 150 }} class="flex justify-between py-0">
      <!-- <a
                href="/#/staffs/{staffId}"
                class="text-base font-semibold text-gray-900 hover:text-indigo-600 cursor-pointer"
                title="Go to {staffId}"
            > -->
      <a> {staffName}</a>
    </div>
  </div>

  {#each clients as client}
    <div
      in:fade|global={{ delay: 100, duration: 300 }}
      out:fade|global={{ delay: 0, duration: 100 }}
      class="grid grid-cols-2 grid-cols-[1fr_2fr] md:grid-cols-[1fr_3fr] lg:grid-cols-[1fr_6fr] gap-4 items-center hover:bg-indigo-50 text-gray-500 text-sm border-b border-gray-200"
    >
      <div class="truncate w-fill">
        <a
          href="/#/clients/{client.id}"
          class="text-blue-600 hover:text-blue-700 hover:underline text-xs"
          >{client.name}</a
        >
      </div>
      <div>
        <div class="flex w-full h-5 bg-gray-200">
          <div
            in:slide|global={{ duration: 1000, axis: "x" }}
            class="{getBackgroundColor(
              Math.abs(client.days_ago)
            )} h-full text-white text-xs items-center flex px-2"
            style="width: {getWidth(client.days_ago)}%"
          ></div>
          <div class="absolute text-white text-xs px-1 py-0.5">
            {#if client.days_ago == null}{:else}
              {Math.abs(client.days_ago)} days ago
            {/if}
          </div>
        </div>
      </div>
    </div>
  {/each}
{/each}

<!-- <div
    in:fade|global={{ delay: 100, duration: 300 }}
    out:fade|global={{ delay: 0, duration: 100 }}
    class="grid grid-cols-2 gap-4 items-center hover:text-indigo-600 hover:bg-indigo-50 text-gray-500 text-sm"
    style="grid-template-columns: 6fr auto;"
>
    <div
        class="grid grid-cols-1 gap-0 xs:gap-4 xs:grid-cols-3 w-full items-center p-0"
        style="grid-template-columns: auto 2fr 2fr;"
    >
        <div class="">
            {client.staff_name}
        </div>
        <div class="flex items-center">
            {item.Quantity}
            <XMarkIcon class="inline h-3 w-3" />
            {parseFloat(item.UnitPrice).toLocaleString("en-AU", {
                style: "currency",
                currency: "AUD",
            })}
        </div>
    </div>
    <div class="text-right items-center">
        {(item.Quantity * item.UnitPrice).toLocaleString("en-AU", {
            style: "currency",
            currency: "AUD",
        })}
        {#if grouped == false}
            <button
                on:click={() => push("/billables/" + item.SessionId)}
                type="button"
                class="inline-flex justify-center rounded-md 600 px-2 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
                >Edit</button
            >
        {/if}
    </div>
</div> -->
