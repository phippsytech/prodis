<script>
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import KPIChart from "./KPI/KPIChart.svelte";
    import KPIBillingItems from "./KPI/KPIBillingItems.svelte";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { getMonday, getDatePlus7Days } from "@shared/utilities.js";

    let staff = [];
    let start_date = getMonday();
    let end_date = getDatePlus7Days(start_date);

    BreadcrumbStore.set({
        path: [
            { url: "/reports", name: "Reports" },
            { url: null, name: "KPI Report" },
        ],
    });

    $: {
        if (start_date && end_date) {
            const startDateRegex = /^\d{4}-\d{2}-\d{2}$/;
            const endDateRegex = /^\d{4}-\d{2}-\d{2}$/;

            if (
                !startDateRegex.test(start_date) ||
                !endDateRegex.test(end_date)
            ) {
                // Display an error or handle the invalid date format here
            } else {
                staff = [];
                jspa("/Staff", "getStaffKPI", {
                    start_date: start_date,
                    end_date: end_date,
                }).then((result) => {
                    staff = result.result;
                });
            }
        }
    }

    $: {
        staff.sort(function (a, b) {
            const nameA = a.name.toUpperCase(); // ignore upper and lowercase
            const nameB = b.name.toUpperCase(); // ignore upper and lowercase
            if (nameA < nameB) return -1;
            if (nameA > nameB) return 1;
            return 0; // names must be equal
        });
    }

    function select(id) {
        staff.forEach((staffMember, index) => {
            if (staffMember.id !== id) {
                staff[index].isExpanded = false;
            } else {
                staff[index].isExpanded = !staff[index].isExpanded;
            }
        });
    }
</script>

<div class="text-2xl font-fredoka-one-regular mb-2" style="color:#220055;">
    KPI Report for period
</div>

<div class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 mb-4">
    <div class="flex-grow">
        <FloatingDate label="Start Date" bind:value={start_date} />
        <FloatingDate label="End Date" bind:value={end_date} />
    </div>
</div>

{#each staff as staffer}
    <!-- <Container> -->
    <!-- {#if staffer.billable_hours_kpi} -->
    <div
        on:click={() => select(staffer.id)}
        class="flex justify-between items-center mt-4 cursor-pointer"
    >
        <div class="w-full">
            <KPIChart {staffer} />
        </div>
        <div></div>
    </div>
    {#if staffer.isExpanded}
        <KPIBillingItems staff_id={staffer.id} bind:start_date bind:end_date />
        <br />
    {/if}
    <!-- {/if} -->
    <!-- </Container> -->
{:else}
    No timesheet data is available for this period
{/each}
