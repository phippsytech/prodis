<script>
    import { XMarkIcon } from "heroicons-svelte/24/outline";
    import LineItem from "./LineItem.svelte";
    
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

    function groupBy(items, keys) {
        return Object.values(
            items.reduce((result, item) => {
                let key = keys.map((k) => item[k]).join("-");
                (result[key] = result[key] || []).push(item);
                return result;
            }, {}),
        );
    }

    $: lineItems = groupBy(line_items, [
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

<!-- {#each Object.values(lineItems) as item, index} -->
{#key lineItems}
    {#each lineItems as lineItem, index (index)}
        <div
            class=".content-container {index == 0 ||
            lineItem[0].NDISNumber != lineItems[index - 1][0].NDISNumber
                ? 'border-t border-gray-200 pt-2 mt-2'
                : ''}"
        >
            {#if index == 0 || lineItem[0].NDISNumber != lineItems[index - 1][0].NDISNumber}
                <div class="flex justify-between py-0">
                    <a
                        href="/#/clients/{lineItem[0].ClientId}"
                        class="text-base font-semibold text-gray-900 hover:text-indigo-600 cursor-pointer"
                        title="Go to {lineItem[0].ClientName}"
                        >{lineItem[0].ClientName}</a
                    >
                    <div class="text-right font-semibold">
                        ${getTotalByNDISNumber(lineItem[0].NDISNumber)}
                    </div>
                </div>
            {/if}

            {#if index == 0 || lineItem[0].PlanManagerId != lineItems[index - 1][0].PlanManagerId || lineItem[0].NDISNumber != lineItems[index - 1][0].NDISNumber}
                <div class="py-0">
                    <span class="text-sm">
                        {lineItem[0].ClientBillingName}
                    </span>
                </div>
            {/if}

            {#each lineItem as item, index}
                <LineItem {item} bind:selectedLineItems grouped={false} />
            {/each}
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
