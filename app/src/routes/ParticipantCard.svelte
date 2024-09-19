<script>
  import { jspa } from "@shared/jspa.js";
  import { formatDate } from "@shared/utilities.js";

  export let client = {};
  let latest_activity = null;
  function getFirstLetters(str) {
    return str
      .split(" ")
      .map((word) => word.charAt(0))
      .join("")
      .toUpperCase();
  }

  function retrieveLatestActivity(actionType) {
      let data = {
          entity_id: client.client_id,
          entity_type: "participant",
          action_type: actionType
      }

      jspa("/ActivityLog", "getLatestActivityLog", data).then((result) => {
          latest_activity = result.result;
      });
  }

  $: {
        if (client.on_hold) {
            retrieveLatestActivity("on-hold");
        }
    }
</script>

<a
  href="/#/clients/{client.client_id}"
  class="relative flex items-center space-x-3 rounded-lg border {client.on_hold ==
  '1'
    ? 'border-red-600 bg-red-50'
    : 'border-indigo-100 bg-white'} pl-4 pr-2 py-3 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2
    hover:text-white
    hover:bg-indigo-600
    transition ease-in-out duration-150 hover:scale-105
    group
    text-gray-800
    cursor-pointer
    "
>
  <div class="flex-shrink-0">
    <!-- <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1590341328520-63256eb32bc3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8OXx8c3VwZXJoZXJvfGVufDB8fDB8fHww&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt=""> -->

    <div
      class="flex h-10 w-10 rounded-full bg-indigo-600 text-white items-center justify-center text-xl font-bold"
    >
      {getFirstLetters(client.client_name)}
    </div>
  </div>
  <div class="min-w-0 flex-1">
    <div
      class="text-sm font-medium
      
      "
    >
      {client.client_name}
    </div>
    {#if client.on_hold}
      <div class="flex gap-1">
        <span class="text-xs font-bold text-red-600">
          ON HOLD
        </span>
        {#if latest_activity}
        <span class="text-xs">
          {formatDate(latest_activity?.timestamp)} 
        </span>
        {/if}
      </div>
      {#if latest_activity}
      {#if latest_activity?.reason != null || latest_activity?.reason != ""}
        <div class="text-xs">
            {latest_activity?.reason}
        </div>
      {/if}
      {/if}
    {/if}
  </div>
</a>
