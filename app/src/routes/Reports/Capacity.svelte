<script>
    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import {
        formatCurrency,
        getDaysUntilDate,
        formatDate,
        convertMinutesToHoursAndMinutes,
        convertDecimalHoursToHoursAndMinutes,
    } from "@shared/utilities.js";
    BreadcrumbStore.set({
        path: [
            { url: "/reports", name: "Reports" },
            { url: null, name: "Capacity Report" },
        ],
    });

    let clients = [];

    onMount(async () => {
        jspa("/Report", "getCapacity", {}).then((result) => {
            clients = result.result;
            clients.sort(function (a, b) {
                return (
                    a.total_sbis_hours_per_week - b.total_sbis_hours_per_week
                );
            });
        });
    });
</script>

<div class="text-2xl font-fredoka-one-regular mb-2" style="color:#220055;">
    SBIS Capacity Report
</div>

<div class="hidden xs:block">
    <div
        class="grid grid-cols-4 items-center py-1 text-xs font-medium text-gray-500"
        style="grid-template-columns: 2fr 2fr 2fr 2fr;"
    >
        <div>Therapist</div>
        <div>KPI</div>
        <div>Assigned</div>
        <div>Available</div>
    </div>
</div>

{#key clients}
    {#each clients as client, index (index)}
        <div
            in:slide|global={{ duration: 150 }}
            class="grid grid-cols-4 gap-4 items-center hover:text-indigo-600 hover:bg-indigo-50 border-t border-gray-200 py-2 px-2"
            style="grid-template-columns: 2fr 2fr 2fr 2fr;"
        >
            <div class="font-semibold">{client.staff_name}</div>
            <div>
                {@html convertDecimalHoursToHoursAndMinutes(
                    client.billable_hours_kpi,
                )}
            </div>
            <div>
                {@html convertDecimalHoursToHoursAndMinutes(
                    client.total_sbis_hours_per_week,
                )}
            </div>
            <div>
                {#if client.billable_hours_kpi && client.total_sbis_hours_per_week}
                    {#if client.billable_hours_kpi - client.total_sbis_hours_per_week > 0}
                        {@html convertDecimalHoursToHoursAndMinutes(
                            client.billable_hours_kpi -
                                client.total_sbis_hours_per_week,
                        )}
                    {:else}
                        <span class="text-red-600">Over Capacity</span>
                    {/if}
                {:else}
                    --
                {/if}
            </div>
        </div>
    {/each}
{/key}
