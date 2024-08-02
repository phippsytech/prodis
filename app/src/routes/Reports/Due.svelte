<script>
    import { flip } from "svelte/animate";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { formatDate, timeAgo } from "@shared/utilities.js";
    import Filter from "@shared/PhippsyTech/svelte-ui/Filter.svelte";
    import { REPORT_TYPES } from "@shared/constants.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import StaffSelector from "@shared/StaffSelector.svelte";
    import { convertFieldsToBoolean } from "@shared/utilities/convertFieldsToBoolean";

    BreadcrumbStore.set({
        path: [
            { url: "/reports", name: "Reports" },
            { url: null, name: "Participant Reports" },
        ],
    });

    let staff_id = null;
    let filter_by_staff = false;
    let reports = [];
    let reportList = [];
    let filters = [
        { label: "interim", enabled: true },
        { label: "comprehensive", enabled: true },
        { label: "review", enabled: true },
        { label: "progress", enabled: true },
        { label: "fca", enabled: true },
        { label: "on hold", enabled: true },
    ];

    jspa("/Client/Report", "listClientReports", {}).then((result) => {
        reports = convertFieldsToBoolean(result.result, ["on_hold"]);
        reports = filterReportsByDueDate(reports, 21);
    });

    function filterReportsByDueDate(reports, days) {
        const today = new Date();
        return reports.filter((report) => {
            const dueDate = new Date(report.due_date);
            const timeDiff = dueDate - today;
            const dayDiff = timeDiff / (1000 * 3600 * 24);
            return dayDiff <= days;
        });
    }

    function getReportName(value) {
        const role = REPORT_TYPES.find((role) => role.value === value);
        return role ? role.option : value;
    }

    $: {
        if (!filter_by_staff) staff_id = null;
    }

    $: {
        reportList = reports
            .filter((report) => {
                const typeFilter = filters.some(
                    (filter) =>
                        filter.label === report.report_type && filter.enabled,
                );
                const holdFilter = !(
                    report.on_hold &&
                    !filters.some(
                        (filter) =>
                            filter.label === "on hold" && filter.enabled,
                    )
                );
                const staffFilter =
                    staff_id == null || report.staff_id == staff_id;
                return (
                    report.report_type &&
                    typeFilter &&
                    holdFilter &&
                    staffFilter
                );
            })
            .sort((a, b) => new Date(a.due_date) - new Date(b.due_date));
    }
</script>

<div class="px-2 mb-2">
    <h2
        class="text-2xl font-fredoka-one-regular tracking-tight text-indigo-900"
    >
        Participant Reports
    </h2>
    <p>Participant Reports that are overdue or due in the next 21 days.</p>
</div>

<div class="bg-white px-3 mb-2 rounded-md pb-1">
    <Filter bind:filters />
    <div class="flex items-center">
        <div>
            <input
                class=" appearance-none h-4 w-4 border border-slate-300 rounded-sm bg-white checked:bg-indigo-600 checked:border-indigo-600 focus:outline-none transition duration-200 bg-no-repeat bg-center bg-contain float-left mr-2 mb-1 cursor-pointer"
                type="checkbox"
                bind:checked={filter_by_staff}
            />
        </div>
        <div>
            {#if filter_by_staff}
                <StaffSelector bind:staff_id />
            {:else}<span class="text-sm text-slate-500"
                    >Filter by Staff Member</span
                >{/if}
        </div>
    </div>
</div>

<ul class="bg-white rounded-lg border border-indigo-100 w-full text-slate-900">
    {#each reportList as report, index (index)}
        <li
            animate:flip={{ duration: 200 }}
            in:slide={{ duration: 200 }}
            out:slide|local={{ duration: 200 }}
            class="px-6 py-2 hover:bg-indigo-50/50 focus:outline-none focus:ring-0 focus:bg-indigo-50/50 focus:text-slate-600 transition duration-500 cursor-pointer {reportList.length -
                1 ==
            index
                ? 'rounded-b-lg'
                : ''}border-b border-indigo-100 w-full {report.archived == 1
                ? 'text-slate-400 cursor-default'
                : ''}
                    
                    {report.report_type == 'progress' && !report.service_codes
                ? 'hidden'
                : ''}
                    "
        >
            <div>
                <span class="font-bold"> {formatDate(report.due_date)}</span>
                - due {timeAgo(report.due_date)}
                {#if report.on_hold == true}
                    <span
                        class=" bg-red-600 text-white rounded-full px-2 py-1 text-xs"
                        >On Hold</span
                    >
                {/if}
            </div>
            <div class="text-slate-600">
                {getReportName(report.report_type)} for
                <a
                    href="/#/clients/{report.client_id}"
                    class="text-indigo-600 hover:text-indigo-700 hover:underline"
                    >{report.client_name}</a
                >

                to be prepared by
                <a
                    href="/#/staff/{report.staff_id}"
                    class="text-indigo-600 hover:text-indigo-700 hover:underline"
                    >{report.staff_name}</a
                >
            </div>
            {#if report.report_type == "progress"}
                <div class="text-xs text-slate-600">
                    Progress Reports are required for: {report.service_codes}
                </div>
            {/if}
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
