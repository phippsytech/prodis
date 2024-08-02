<script>
    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";
    import { jspa } from "@shared/jspa.js";
    import { formatDate } from "@shared/utilities.js";

    export let task_id;

    let staff_id;

    let times = [];

    let display = "pending";

    onMount(() => {
        // listTrips();
    });

    export function listTrips() {
        if (staff_id == null) return;
        jspa("/Trip", "listTrips", { staff_id: staff_id }).then((result) => {
            times = result.result;
        });
    }

    function deleteTrip(time_id) {
        jspa("/Trip", "deleteTrip", { id: time_id }).then((result) => {
            times = times.filter((time) => time.id !== time_id);
            // times = result.result
        });
    }

    times = [
        {
            date: "2021-09-30",
            duration: 120,
            staff: "Phippsy",
            status: "pending",
        },
    ];
</script>

{#if times.length > 0}
    <Container>
        <div class="font-bold text-xl mb-2" style="color:#220055;">
            Time on Task
        </div>

        <!-- <div>
    
      <div class="border-b border-gray-200 mb-4">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">

          <span class="border-indigo-500 text-indigo-600 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium" aria-current="page">Pending</span>
          <span class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium cursor-pointer">Processed</span>
        </nav>
      </div>
    
  </div> -->

        {#each times as time}
            <div
                class="flex justify-between items-center hover:bg-indigo-50 p-2"
            >
                <span class="font-semibold">{formatDate(time.date)}</span>
                <span>{time.staff}</span>
                <div class="flex items-center gap-x-4">
                    <div class="text-xl">{time.duration} mins</div>
                    <!-- {#if time.status === "pending"} -->
                    <button
                        class="inline-block hover:text-indigo-600"
                        on:click={() => deleteTrip(time.id)}
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            class="h-6 w-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            ></path>
                        </svg>
                    </button>
                    <!-- {/if} -->
                </div>
            </div>
        {/each}
    </Container>
{/if}
