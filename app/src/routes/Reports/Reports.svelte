<script>
    import { push } from "svelte-spa-router";
    import ReportPanel from "./ReportPanel.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";

    BreadcrumbStore.set({
        path: [{ url: null, name: "Reports" }],
    });
</script>

<ReportPanel
    on:click={() => {
        push("/reports/budget");
    }}
    label="Client Budget Report"
>
    Shows the services provided time vs the allocation of time.
</ReportPanel>

<ReportPanel
    on:click={() => push("/reports/capacity")}
    label="SBIS Capacity Report"
>
    Shows the hours per week assigned to therapists for SBIS services
</ReportPanel>

<ReportPanel
    on:click={() => push("/reports/participantbudget")}
    label="Participant Budget Report"
>
    A list of participants sorted by NDIS Plan End Date with remaining available
    budget.
</ReportPanel>

<ReportPanel
    on:click={() => push("/reports/due")}
    label="Client Plan Report Due Dates"
>
    Shows due dates of Interim, Comprehensive and Yearly reports for all
    clients.
</ReportPanel>

<ReportPanel
    on:click={() => push("/reports/expired")}
    label="Expired NDIS Plans"
>
    A list of clients who have expired NDIS Plans.
</ReportPanel>

<!-- <ReportPanel
    on:click={() => push("/reports/expiring")}
    label="Expiring this month"
>
    A list of clients with NDIS Plans expiring this month with remaining budget.
</ReportPanel> -->

<ReportPanel
    on:click={() => push("/reports/serviceagreementexpiry")}
    label="Client Service Agreements"
>
    A list of the status of client service agreements.
</ReportPanel>

<ReportPanel on:click={() => push("/reports/kpi")} label="KPI Report">
    A summary of staff KPI for a date period
</ReportPanel>

<!-- <ReportPanel
    on:click={() => push("/reports/overduevisits")}
    label="Overdue Visits Report"
>
    A summary of clients who have not been visited by any staff in the last 14
    days.
</ReportPanel> -->

<ReportPanel
    on:click={() => push("/reports/lastseen")}
    label="Last Seen Report"
>
    A summary showing the last time therapists provided direct service (face to
    face) services to participants.
</ReportPanel>

<ReportPanel
    on:click={() => push("/reports/providertravel")}
    label="Provider Travel Report"
>
    A summary of Provider Travel for staff for a date period
</ReportPanel>

<ReportPanel on:click={() => push("/reports/timelog")} label="TimeLog Report">
    A report of time logged for a date period
</ReportPanel>
