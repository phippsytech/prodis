<script>
    import { XMarkIcon } from "heroicons-svelte/24/outline";
    import LineItem from "./LineItem.svelte";
    import GroupIcon from "@shared/PhippsyTech/svelte-ui/icons/Group.svelte";
    import UngroupIcon from "@shared/PhippsyTech/svelte-ui/icons/Ungroup.svelte";
    import FilterListIcon from "@shared/PhippsyTech/svelte-ui/icons/FilterList.svelte";

    export let line_items = [];
    export let selected_total = 0;
    export let selectedLineItems = [];

    function getTotalValueByNDISNumber(ndisNumber) {
        return line_items
            .reduce((total, item) => {
                if (item.NDISNumber === ndisNumber) {
                    return (
                        total +
                        parseFloat(item.Quantity) * parseFloat(item.UnitPrice)
                    );
                }
                return total;
            }, 0)
            .toFixed(2);
    }

    function getTotalValueByNDISNumberAndPlanManager(
        ndisNumber,
        planManagerId,
    ) {
        return line_items
            .reduce((total, item) => {
                if (
                    item.NDISNumber === ndisNumber &&
                    item.PlanManagerId === planManagerId
                ) {
                    return (
                        total +
                        parseFloat(item.Quantity) * parseFloat(item.UnitPrice)
                    );
                }
                return total;
            }, 0)
            .toFixed(2);
    }

    export function handleSelectByDate(start_date, end_date) {
        selectedLineItems = line_items
            .filter(
                (line_item) =>
                    line_item.SupportsDeliveredFrom >= start_date &&
                    line_item.SupportsDeliveredFrom <= end_date,
            )
            .map((line_item) => line_item.SessionId);
    }

    function handleSelectAll(event) {
        if (event.target.checked) {
            selectedLineItems = line_items.map(
                (line_item) => line_item.SessionId,
            );
        } else {
            selectedLineItems = [];
        }
    }

    function calculateSelectedTotal(selectedLineItems) {
        selected_total = 0;
        line_items.forEach((item) => {
            if (selectedLineItems.includes(item.SessionId)) {
                selected_total +=
                    parseFloat(item.Quantity) * parseFloat(item.UnitPrice);
            }
        });
    }

    $: calculateSelectedTotal(selectedLineItems);

    let groupItems = false;

    function groupBy(items, keys) {
        return Object.values(
            items.reduce((result, item) => {
                let key = keys.map((k) => item[k]).join("-");
                (result[key] = result[key] || []).push(item);
                return result;
            }, {}),
        );
    }

    $: groupedItems = groupBy(line_items, [
        "NDISNumber",
        "PlanManagerId",
        "SupportNumber",
        "ClaimType",
        "SupportsDeliveredFrom",
    ]);

    function aggregateGroup(group) {
        return group.reduce(
            (result, item) => {
                // Aggregate the data you need. This is just an example.
                result.CancellationReason = item.CancellationReason;
                result.ClaimReference = item.ClaimReference;
                result.ClaimType = item.ClaimType;
                result.ClientBillingEmail = item.ClientBillingEmail;
                result.ClientBillingId = item.ClientBillingId;
                result.ClientBillingName = item.ClientBillingName;
                result.ClientId = item.ClientId;
                result.ClientName = item.ClientName;
                result.InvoiceBatch = item.InvoiceBatch;
                result.NDISNumber = item.NDISNumber;
                result.PlanManagerId = item.PlanManagerId;

                result.RegistrationNumber = item.RegistrationNumber;
                result.ServiceBillingUnit = item.ServiceBillingUnit;
                result.ServiceCode = item.ServiceCode;
                result.ServiceName = item.ServiceName;
                result.SessionId = item.SessionId;
                result.SupportNumber = item.SupportNumber;
                result.SupportsDeliveredFrom = item.SupportsDeliveredFrom;
                result.SupportsDeliveredTo = item.SupportsDeliveredTo;
                result.TherapistName = item.TherapistName;
                result.UnitPrice = item.UnitPrice;

                result.Quantity += parseFloat(item.Quantity);
                result.SessionDuration += parseFloat(item.SessionDuration);
                // result.UnitPrice = Math.max(
                //     result.UnitPrice,
                //     parseFloat(item.UnitPrice),
                // );
                result.SessionIds.push(item.SessionId);

                return result;
            },
            {
                // Initial values for the aggregated data
                Quantity: 0,
                SessionDuration: 0,
                UnitPrice: 0,
                SessionIds: [],
            },
        );
    }

    function getTotalByNDISNumber(ndisNumber) {
        return line_items
            .filter(item => item.NDISNumber === ndisNumber)
            .reduce((total, item) => {
                return total + (parseFloat(item.Quantity) * parseFloat(item.UnitPrice));
            }, 0)
            .toFixed(2);
    }
    
</script>

<nav
    class="bg-white text-gray-600 border border-indigo-100 rounded-lg drop-shadow-sm flex space-x-6 items-center px-3 py-3 mb-2"
    aria-label="Toolbar"
>
    <button
        on:click={() => (groupItems = !groupItems)}
        class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-50 hover:text-indigo-600 text-xs"
    >
        {#if groupItems}
            <span><UngroupIcon class="inline h-6 w-6 " />Ungroup</span>
        {:else}
            <span><GroupIcon class="inline h-6 w-6 " />Group</span>
        {/if}
    </button>

    <!-- <button
        on:click={() => (groupItems = !groupItems)}
        class="w-8 h-8 relative inline-flex justify-center items-center rounded-lg hover:bg-indigo-50 hover:text-indigo-600 text-xs"
    >
        <span><FilterListIcon class="inline h-6 w-6 " />Filter</span>
    </button> -->
</nav>

<div class="hidden xs:block">
    <div
        class="grid grid-cols-2 items-center py-1 text-xs font-medium text-gray-500"
        style="grid-template-columns: 6fr auto;"
    >
        <div
            class="grid grid-cols-3 gap-4 items-center"
            style="grid-template-columns: auto 2fr 2fr;"
        >
            <div class="flex items-center">
                <input
                    checked={selectedLineItems.length > 0}
                    on:change={handleSelectAll}
                    type="checkbox"
                    class="h-4 w-4 mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                    style="background-color:rgb(79, 70, 229);"
                />
                Date
            </div>
            <div>Service <span class="text-xs">(Claim Type)</span></div>
            <div>Quantity <XMarkIcon class="inline h-3 w-3" /> Unit Price</div>
        </div>
        <div class="text-right">Total</div>
    </div>
</div>

<!-- {#each Object.values(groupedItems) as group, index} -->
{#key groupedItems}
    {#each groupedItems as group, index (index)}
        <div
            class=".content-container {index == 0 ||
            group[0].NDISNumber != groupedItems[index - 1][0].NDISNumber
                ? 'border-t border-gray-200 pt-2 mt-2'
                : ''}"
        >
            {#if index == 0 || group[0].NDISNumber != groupedItems[index - 1][0].NDISNumber}
                <div class="flex justify-between py-0">
                    <a
                        href="/#/clients/{group[0].ClientId}"
                        class="text-base font-semibold text-gray-900 hover:text-indigo-600 cursor-pointer"
                        title="Go to {group[0].ClientName}"
                        >{group[0].ClientName}</a
                    >
                    <div class="text-right font-semibold">
                        ${getTotalByNDISNumber(group[0].NDISNumber)}
                    </div>
                </div>
            {/if}

            {#if index == 0 || group[0].PlanManagerId != groupedItems[index - 1][0].PlanManagerId || group[0].NDISNumber != groupedItems[index - 1][0].NDISNumber}
                <div class="py-0">
                    <span class="text-sm">
                        <a                     
                            title="Go to {group[0].ClientBillingName}"
                            >{group[0].ClientBillingName}</a
                        >
                    </span>
                </div>
            {/if}

            {#if groupItems}
                <LineItem
                    item={aggregateGroup(group)}
                    bind:selectedLineItems
                    grouped={true}
                />
            {:else}
                {#each group as item, index}
                    <LineItem {item} bind:selectedLineItems grouped={false} />
                {/each}
            {/if}
        </div>
    {/each} 
{/key}

<style>
    .content-container {
        /* Ensure the container expands to fit its content */
        overflow: hidden;
        /* Apply a transition effect to smooth size changes */
        transition: height 1s cubic-bezier(0.645, 0.045, 0.355, 1);
    }
</style>
