<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";

    import { onMount } from "svelte";

    import { BreadcrumbStore } from "@shared/stores.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

    let start_date = "2023-08-07";
    let end_date = "2023-08-13";

    let items = [];
    let mounted = false;
    let show = false;

    // items.day = "5c950435-f229-406d-8b06-5e9b4e7ce2b2";

    onMount(async () => {
        getBreakdown();

        mounted = true;
    });

    function getBreakdown() {
        jspa("/SIL/Payrun", "getBreakdown", {
            start_date: start_date,
            end_date: end_date,
        })
            .then((result) => {
                items = result.result;
            })
            .catch((error) => {});
    }

    function calculateTotal(entries) {
        let total = 0;
        entries.forEach((entry) => {
            total += entry.total;
        });
        return total.toLocaleString("en-US", {
            style: "currency",
            currency: "USD",
        });
    }
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular px-2"
    style="color:#220055;"
>
    Payroll Breakdown
</div>

<p class="px-2 mb-4">
    A summary of pays for a period broken down by house and earning rate.
</p>

<div class="text-sm mb-1 font-bold opacity-50">Billing Period</div>

<div
    class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 items-center"
>
    <div class="flex-grow">
        <FloatingDate label="Start Date" bind:value={start_date} />
    </div>
    <div class="flex-grow">
        <FloatingDate label="End Date" bind:value={end_date} />
    </div>
    <button
        on:click={() => getBreakdown()}
        type="button"
        class="w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-4 text-sm font-semibold text-white hover:bg-indigo-500 mb-2"
        >Get Breakdown
    </button>
</div>

<!-- {#each items as item, index }
<div>{JSON.stringify(item)}</div>
{/each} -->

{#each items as item}
    <h2 class="text-xl font-medium mt-2">
        {item.house_id}
        {calculateTotal(item.entries)}
    </h2>
    {#each item.entries as entry}
        <div class="flex border-y border-gray-200">
            <div class="flex-col flex-shrink-0 w-1/2">
                {entry.earnings_name}
                <span class="text-xs"
                    >({entry.earnings_rate.toLocaleString("en-US", {
                        style: "currency",
                        currency: "USD",
                    })})</span
                >:
            </div>
            <div class="flex-col w-1/4 flex-grow text-center">
                {entry.duration}
            </div>
            <div class="flex-col flex-shrink-0 text-right w-1/4">
                {entry.total.toLocaleString("en-US", {
                    style: "currency",
                    currency: "USD",
                })}
            </div>
        </div>
    {/each}
{/each}
