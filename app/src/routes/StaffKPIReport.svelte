<script>
    import Container from "@shared/Container.svelte";
    import KPIChart from "@app/routes/Reports/KPI/KPIChart.svelte";
    import KPIBillingItems from "@app/routes/Reports/KPI/KPIBillingItems.svelte";
    import { jspa } from "@shared/jspa.js";
    import {
        formatDate,
        getMonday,
        getDatePlus7Days,
    } from "@shared/utilities.js";

    export let staff_id;
    let staff = [];

    let start_date = getMonday();
    let end_date = getDatePlus7Days(start_date);

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
                jspa("/Staff", "getStafferKPI", {
                    staff_id: staff_id,
                    start_date: start_date,
                    end_date: end_date,
                }).then((result) => {
                    staff = result.result;
                });
            }
        }
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

<Container>
    <div class="font-medium mb-2 text-sm">
        Your KPI Report for {formatDate(start_date)} - {formatDate(end_date)}
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
            <KPIBillingItems
                staff_id={staffer.id}
                bind:start_date
                bind:end_date
            />
            <br />
        {/if}
        <!-- {/if} -->
        <!-- </Container> -->
    {:else}
        <span class="font-light"
            >No billing data is available for this period</span
        >
    {/each}
</Container>
