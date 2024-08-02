<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import Container from "@shared/Container.svelte";
    import {
        formatCurrency,
        convertMinutesToHoursAndMinutes,
    } from "@shared/utilities.js";
    import { jspa } from "@shared/jspa.js";
    import { onMount } from "svelte";

    export let service = {};

    service.selected = false;
    service.reindex_budget = false;
    service.new_rate = service.rate;

    jspa("/SupportItem", "getSupportItemByNumber", {
        support_item_number: service.billing_code,
    }).then((result) => {
        // console.log(result.result);
        service.max_rate = result.result.sa;
        return result.result;
    });

    // Function to get the remaining budget
    function getRemainingBudget() {
        return service.budget - service.spent;
    }

    // Function to get the remaining hours based on the current rate
    function getRemainingHours() {
        return getRemainingBudget() / service.rate;
    }

    // Function to calculate the new budget based on the reindex budget flag
    function getNewBudget() {
        if (!service.reindex_budget) {
            return getRemainingBudget();
        }
        return getRemainingHours() * service.new_rate;
    }

    // Reactive statements to update the remaining hours and new budget
    $: remainingBudget = getRemainingBudget(service);
    $: remainingHours = getRemainingHours(service);
    $: new_budget = getNewBudget(service);
</script>

<Container>
    <div class="flex items-center mb-2">
        <input
            type="checkbox"
            id="selectedService"
            bind:checked={service.selected}
            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
        />
        <label for="selectedService" class="ml-2 text-sm text-gray-900"
            ><span class="font-bold">{service.code}</span>
            ${service.rate}
            ${service.max_rate}</label
        >
    </div>

    {#if service.selected}
        <FloatingInput
            label={`New Rate (max $${service.max_rate})`}
            placeholder="193.99"
            bind:value={service.new_rate}
        />

        <div class="flex justify-start items-end">
            <div class="px-2">
                <div class="text-xs opacity-50">New Budget</div>
                <div class="font-bold text-gray-900">
                    {formatCurrency(new_budget)}
                </div>
            </div>

            <div class="px-2">
                <div class="text-xs opacity-50">New Hours</div>
                <div class="font-bold text-gray-900">
                    {@html convertMinutesToHoursAndMinutes(
                        (new_budget / service.new_rate) * 60,
                    )}
                </div>
            </div>

            <div class="flex items-center mb-1">
                <input
                    type="checkbox"
                    id="reindexBudget"
                    bind:checked={service.reindex_budget}
                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
                <label
                    for="reindexBudget"
                    class="ml-2 text-sm font-medium text-gray-900"
                    >Re-Index Budget <span class="text-xs italic opacity-50"
                        >(preserves remaining hours)</span
                    ></label
                >
            </div>
        </div>
    {/if}
</Container>
