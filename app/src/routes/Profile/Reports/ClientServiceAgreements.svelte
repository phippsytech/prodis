<script>
    import { onMount } from "svelte";
    import { flip } from "svelte/animate";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { formatDate, timeAgo } from "@shared/utilities.js";

    import { StafferStore } from "@shared/stores.js";
    import { convertFieldsToBoolean } from "@shared/utilities/convertFieldsToBoolean";

    let staff_id = $StafferStore.id;
    // staff_id = 60; //Flora
    let reports = [];
    let reportList = [];

    $: getReports(staff_id);

    async function getReports(staff_id) {
        let result = await jspa(
            "/Participant/ServiceAgreement",
            "listServiceAgreementsByStaffId",
            {
                staff_id: staff_id,
            },
        );
        reports = convertFieldsToBoolean(result.result, ["on_hold"]);
        // reports = filterReportsByDueDate(reports, 21);
        // return reports;
    }

    function isOverdue(dueDate) {
        const today = new Date();
        const due = new Date(dueDate);
        return due < today;
    }

    function filterReportsByDueDate(reports, days) {
        const today = new Date();
        return reports.filter((report) => {
            const dueDate = new Date(report.service_agreement_end_date);
            const timeDiff = dueDate - today;
            const dayDiff = timeDiff / (1000 * 3600 * 24);
            return dayDiff <= days;
        });
    }
</script>

<div class="flex sm:flex-row flex-col sm:items-center mt-6 mb-1">
    <h3 class="text-slate-800 font-bold mx-2">
        Client Service Agreement End Dates
    </h3>
</div>

<ul class="bg-white rounded-lg border border-indigo-100 w-full text-slate-800">
    {#each reports as report, index (index)}
        <li
            animate:flip={{ duration: 200 }}
            in:slide={{ duration: 200 }}
            out:slide|local={{ duration: 200 }}
            class="group flex flex-col justify-between px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500
            {isOverdue(report.service_agreement_end_date)
                ? ' bg-red-50 text-red-600'
                : ''}
            
            {reportList.length - 1 == index
                ? 'rounded-b-lg'
                : ''}border-b border-indigo-100 w-full {report.archived == 1
                ? 'text-slate-400 cursor-default'
                : ''}
                    
                    {report.report_type == 'progress' && !report.service_codes
                ? 'hidden'
                : ''}
                    "
        >
            <div class="flex justify-between">
                <div>
                    {#if report.on_hold == true}
                        <span
                            class=" bg-red-600 text-white rounded-full px-2 py-1 text-xs"
                            >On Hold</span
                        >
                    {/if}
                    <a
                        href="/#/clients/{report.client_id}"
                        class="text-indigo-600 hover:text-indigo-700 hover:underline"
                        >{report.client_name}</a
                    >
                    <span class="text-slate-600 text-xs">
                        {timeAgo(report.service_agreement_end_date)}
                    </span>
                </div>
                <div>
                    <span class="">
                        {formatDate(report.service_agreement_end_date)}</span
                    >
                </div>
            </div>
        </li>
    {:else}
        <li class="px-6 py-2 w-full rounded-t-lg text-slate-400 cursor-default">
            <div class="text-sm text-center text-slate-500 p-4 pt-2">
                <div
                    class="flex justify-center items-center h-8 w-8 text-slate-300 mx-auto m-2"
                >
                    <svg
                        data-slot="icon"
                        fill="none"
                        stroke-width="1.5"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"
                        ></path>
                    </svg>
                </div>
                <div>No reports found</div>
            </div>
        </li>
    {/each}
</ul>
