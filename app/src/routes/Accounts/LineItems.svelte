<script>
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { push } from "svelte-spa-router";
    import { formatDate } from "@shared/utilities";
    import { XMarkIcon, PencilIcon } from "heroicons-svelte/24/outline";
    // import {MinusIcon} from 'heroicons-svelte/24/outline';
    // import ExpandIndicator from "@app/Layout/ExpandIndicator.svelte";

    export let line_items = [];
    export let mode = "edit";

    export let selected_total = 0;

    export let selectedLineItems = [];

    // let client_expanded= false;

    let claim_types = [
        { option: "Direct Service", value: "" },
        { option: "Cancellation", value: "CANC" },
        { option: "NDIA Required Report", value: "REPW" },
        { option: "Provider Travel", value: "TRAN" },
        {
            option: "Non-Face-to-Face Services",
            value: "NF2F",
        },
    ];

    function getOption(value) {
        if (value === null) value = "";
        let option = "";
        claim_types.forEach((item) => {
            if (item.value == value) {
                option = item.option;
            }
        });
        return option;
    }

    function getItem(id) {
        return line_items.find((item) => item.SessionId === id);
    }

    function reverse(id) {
        let line_item = getItem(id);
        let reverse = confirm(`Are you sure you want to reverse this line item?

${line_item.ClientName}
${line_item.SupportsDeliveredFrom} ${line_item.ServiceCode} (${getOption(line_item.ClaimType)}) 
${line_item.Quantity} x ${parseFloat(line_item.UnitPrice).toLocaleString("en-AU", { style: "currency", currency: "AUD" })} = ${(line_item.Quantity * line_item.UnitPrice).toLocaleString("en-AU", { style: "currency", currency: "AUD" })}`);

        if (reverse) {
            jspa("/TimeTracking", "updateTimeTracking", {
                id: line_item.SessionId,
                invoice_batch: null,
            }).then((result) => {
                line_items = line_items.filter((item) => item.SessionId !== id);
            });
        }
    }

    function getTotalValueByNDISNumber(ndisNumber) {
        const totalValue = line_items.reduce((total, item) => {
            if (item.NDISNumber === ndisNumber) {
                return (
                    total +
                    parseFloat(item.Quantity) * parseFloat(item.UnitPrice)
                );
            }
            return total;
        }, 0);
        return totalValue.toFixed(2);
    }

    function getTotalValueByNDISNumberAndPlanManager(
        ndisNumber,
        planManagerId,
    ) {
        const totalValue = line_items.reduce((total, item) => {
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
        }, 0);
        return totalValue.toFixed(2);
    }

    function handleCheckboxChange(event) {
        const checkboxValue = event.target.value; // was using parseInt

        if (event.target.checked) {
            // If checkbox is checked, add the invoice ID to selectedLineItems
            selectedLineItems = [...selectedLineItems, checkboxValue];
        } else {
            // If checkbox is unchecked, remove the invoice ID from selectedLineItems
            selectedLineItems = selectedLineItems.filter(
                (id) => id !== checkboxValue,
            );
        }
    }

    export function handleSelectByDate(start_date, end_date) {
        // map the line items to get the invoice IDs filtered between start date and end date
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
</script>

<!-- <p>Selected Line Items: {selectedLineItems.join(', ')}</p> -->

<!-- Header -->
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
                    class="h-4 w-4 mr-2 rounded border-indigo-100 text-indigo-600 focus:ring-indigo-600"
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

{#key line_items}
    {#each line_items as item, index}
        <div
            class={index == 0 ||
            item.ClientName != line_items[index - 1].ClientName
                ? "border-t border-indigo-100 pt-2 mt-2"
                : ""}
        >
            {#if index == 0 || item.ClientName != line_items[index - 1].ClientName}
                <div
                    in:slide|global={{ duration: 150 }}
                    class="flex justify-between py-0"
                >
                    <a
                        href="/#/clients/{item.ClientId}"
                        class="text-base font-semibold text-gray-900 hover:text-indigo-600 cursor-pointer"
                        title="Go to {item.ClientName}"
                    >
                        {item.ClientName}</a
                    >

                    <div class="text-right font-semibold">
                        ${getTotalValueByNDISNumberAndPlanManager(
                            item.NDISNumber,
                            item.PlanManagerId,
                        )}
                    </div>
                </div>
            {/if}

            {#if 1 == 1}
                {#if index == 0 || item.PlanManagerId != line_items[index - 1].PlanManagerId || item.ClientName != line_items[index - 1].ClientName}
                    <div in:slide|global={{ duration: 150 }} class="py-0">
                        <span class=" text-sm">
                            <a
                                href="/#/planmanagers/{item.PlanManagerId}"
                                class="hover:text-indigo-600"
                                title="Go to {item.ClientBillingName}"
                                >{item.ClientBillingName}</a
                            ></span
                        >
                    </div>
                {/if}

                <div
                    in:slide|global={{ duration: 150 }}
                    class="grid grid-cols-2 gap-4 items-center hover:text-indigo-600 hover:bg-indigo-50 text-gray-500 text-sm"
                    style="grid-template-columns: 6fr auto;"
                >
                    <div
                        class="grid grid-cols-1 gap-0 xs:gap-4 xs:grid-cols-3 w-full items-center p-0"
                        style="grid-template-columns: auto 2fr 2fr;"
                    >
                        <div class="flex items-center">
                            <input
                                id="invoice-{item.SessionId}"
                                value={item.SessionId}
                                checked={selectedLineItems.includes(
                                    item.SessionId,
                                )}
                                on:change={handleCheckboxChange}
                                type="checkbox"
                                class="h-4 w-4 mr-2 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                style="background-color:rgb(79, 70, 229);"
                            />

                            <span class="hidden sm:inline"
                                >{formatDate(item.SupportsDeliveredFrom, {
                                    day: "numeric",
                                    month: "short", // short, numeric
                                    year: "numeric",
                                })}</span
                            >
                            <span class="inline sm:hidden"
                                >{formatDate(item.SupportsDeliveredFrom, {
                                    day: "numeric",
                                    month: "short", // short, numeric
                                    year: null,
                                })}</span
                            >
                        </div>

                        <div class="">
                            {item.SessionId}
                            {item.ServiceCode}
                            <span class="text-xs hidden sm:inline"
                                >({getOption(item.ClaimType)})</span
                            >
                        </div>
                        <div class="flex items-center">
                            {item.Quantity}
                            <XMarkIcon class="inline h-3 w-3" />
                            {parseFloat(item.UnitPrice).toLocaleString(
                                "en-AU",
                                { style: "currency", currency: "AUD" },
                            )}
                        </div>
                    </div>

                    <div class="text-right items-center">
                        {(item.Quantity * item.UnitPrice).toLocaleString(
                            "en-AU",
                            { style: "currency", currency: "AUD" },
                        )}
                        {#if mode == "edit"}
                            <button
                                on:click={() =>
                                    push("/billables/" + item.SessionId)}
                                type="button"
                                class="inline-flex justify-center rounded-md 600 px-2 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
                            >
                                Edit</button
                            >
                            <!-- </span> -->
                        {/if}
                    </div>
                </div>
            {/if}
        </div>
    {/each}
{/key}
