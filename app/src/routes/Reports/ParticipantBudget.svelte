<script>
    import { onMount } from "svelte";
    import { slide } from "svelte/transition";
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

    onMount(async () => {
        jspa("/Report", "getParticipantBudget", {}).then((result) => {
            clients = result.result;
            sum_of_remaining = calculateTotalRemaining();
            sum_of_total_billed = calculateTotalBilled();
            sum_of_budget = calculateTotalBudget();
        });
    });

    // $: sum_of_remaining = calculateTotalRemaining();

    function calculateTotalBilled() {
        let total = 0;
        clients.forEach((client) => {
            total += parseFloat(client.total_billed);
        });
        return total;
    }

    function calculateTotalRemaining() {
        let total = 0;
        clients.forEach((client) => {
            total += parseFloat(client.remaining);
        });
        return total;
    }

    function calculateTotalBudget() {
        let total = 0;
        clients.forEach((client) => {
            total += parseFloat(client.budget);
        });
        return total;
    }
</script>

<div class="text-2xl font-fredoka-one-regular mb-2" style="color:#220055;">
    Participant Budget Report
</div>

<div>
    <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3 mb-4">
        <div
            class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6"
        >
            <dt class="truncate text-sm font-medium text-gray-500">
                Combined Budget Total
            </dt>
            <dd
                class="mt-1 text-3xl font-semibold tracking-tight text-gray-900"
            >
                {formatCurrency(sum_of_budget)}
            </dd>
        </div>
        <div
            class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6"
        >
            <dt class="truncate text-sm font-medium text-gray-500">Spent</dt>
            <dd
                class="mt-1 text-3xl font-semibold tracking-tight text-gray-900"
            >
                {formatCurrency(sum_of_total_billed)}
            </dd>
        </div>
        <div
            class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6"
        >
            <dt class="truncate text-sm font-medium text-gray-500">
                Available
            </dt>
            <dd
                class="mt-1 text-3xl font-semibold tracking-tight text-gray-900"
            >
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

<div class="hidden xs:block">
    <div
        class="grid grid-cols-7 items-center py-1 text-xs font-medium text-gray-500"
        style="grid-template-columns: 2fr 2fr 1fr 1fr 1fr 1fr 1fr;"
    >
        <div>Participant</div>
        <div>Plan Expires</div>
        <div>Service</div>
        <div>Rate</div>
        <div>Budget</div>
        <div>Remaining Budget</div>

        <div>Available Time</div>
    </div>
</div>

{#key clients}
    {#each clients as client, index (index)}
        <div
            in:slide|global={{ duration: 150 }}
            class="grid grid-cols-4 gap-4 items-center hover:text-indigo-600 hover:bg-indigo-50 border-t border-gray-200 py-2 px-2"
            style="grid-template-columns: 2fr 2fr 1fr 1fr 1fr 1fr 1fr;"
        >
            <div class="font-semibold">
                <a class="text-indigo-600" href="/#/clients/{client.id}"
                    >{client.name}</a
                >
            </div>
            <div>
                {formatDate(client.ndis_plan_end_date)}
                <span class="text-xs"
                    >({getDaysUntilDate(client.ndis_plan_end_date)})</span
                >
            </div>
            <div>
                {client.code}
            </div>
            <div>{formatCurrency(client.rate)}</div>
            <div>{formatCurrency(client.budget)}</div>
            <div>{formatCurrency(client.remaining)}</div>
            <div>
                {@html convertMinutesToHoursAndMinutes(
                    Math.round((client.remaining / client.rate) * 60),
                )}
            </div>
        </div>
    {/each}
{/key}
