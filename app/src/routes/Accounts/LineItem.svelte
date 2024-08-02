<script>
    // import { createEventDispatcher } from "svelte";
    import { fade } from "svelte/transition";
    import { push } from "svelte-spa-router";
    import { formatDate } from "@shared/utilities";
    import { XMarkIcon } from "heroicons-svelte/24/outline";

    export let item = {};
    export let selectedLineItems = [];
    export let grouped = false;

    // let dispatch = createEventDispatcher();

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
            if (item.value === value) {
                option = item.option;
            }
        });
        return option;
    }

    function handleCheckboxChange(event) {
        if (grouped) {
            // If items are grouped, add or remove the contents of the item.SessionIds array
            const groupIds = item.SessionIds;
            if (event.target.checked) {
                selectedLineItems = [...selectedLineItems, ...groupIds];
            } else {
                selectedLineItems = selectedLineItems.filter(
                    (id) => !groupIds.includes(id),
                );
            }
        } else {
            // If items are not grouped, add or remove the checkboxValue
            const checkboxValue = event.target.value;
            if (event.target.checked) {
                selectedLineItems = [...selectedLineItems, checkboxValue];
            } else {
                selectedLineItems = selectedLineItems.filter(
                    (id) => id !== checkboxValue,
                );
            }
        }
    }
</script>

<div
    in:fade|global={{ delay: 100, duration: 300 }}
    out:fade|global={{ delay: 0, duration: 100 }}
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
                checked={selectedLineItems.includes(item.SessionId)}
                on:change={handleCheckboxChange}
                type="checkbox"
                class="h-4 w-4 mr-2 rounded border-indigo-100 text-indigo-600 focus:ring-indigo-600"
                style="background-color:rgb(79, 70, 229);"
            />
            <span class="hidden sm:inline"
                >{formatDate(item.SupportsDeliveredFrom, {
                    day: "numeric",
                    month: "short",
                    year: "numeric",
                })}</span
            >
            <span class="inline sm:hidden"
                >{formatDate(item.SupportsDeliveredFrom, {
                    day: "numeric",
                    month: "short",
                    year: null,
                })}</span
            >
        </div>
        <div class="">
            <!-- {item.SessionId} -->
            {item.ServiceCode}
            <span class="text-xs hidden sm:inline"
                >({getOption(item.ClaimType)})</span
            >
            {#if !grouped}
                <span class="text-xs hidden md:inline">
                    : {item.TherapistName}</span
                >
            {/if}
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
            {#if item.ClaimType == "TRAN"}
                <button
                    on:click={() => push("/trips/" + item.TripId)}
                    type="button"
                    class="inline-flex justify-center rounded-md 600 px-2 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
                    >Edit</button
                >
            {:else}
                <button
                    on:click={() => push("/billables/" + item.SessionId)}
                    type="button"
                    class="inline-flex justify-center rounded-md 600 px-2 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
                    >Edit</button
                >
            {/if}
        {/if}
    </div>
</div>
