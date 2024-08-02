<script>
    import Role from "@shared/Role.svelte";

    import { ModalStore } from "@app/Overlays/stores";
    import ReportForm from "./ReportForm.svelte";
    import { REPORT_TYPES } from "@shared/constants.js";
    import { jspa } from "@shared/jspa.js";

    import { slide } from "svelte/transition";
    import { XMarkIcon, PlusIcon } from "heroicons-svelte/24/outline";
    import { onMount } from "svelte";

    import createStore from "@shared/createStore";
    import { convertFieldsToBoolean } from "@shared/utilities/convertFieldsToBoolean";
    import { formatDate, timeAgo } from "@shared/utilities.js";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";

    export let client_id = null;

    let report = {
        client_id: client_id,
        report_type: null,
        due_date: null,
        is_done: false,
    };

    onMount(async () => {
        ClientReports.load({ client_id });
    });

    // Create ClientReports store
    let ClientReports = createStore(
        "/Client/Report",
        {
            list: "listClientReportsByClientId",
            add: "addClientReport",
            update: "updateClientReport",
            delete: "deleteClientReport",
        },
        {
            load: (results) => {
                return convertFieldsToBoolean(results, ["is_done"]);
            },
            add: (result) => {
                let resultReport = convertFieldsToBoolean(result, ["is_done"]);
                delete resultReport.update;
                delete resultReport.created;
                delete resultReport.archived;
                return resultReport;
            },
        },
    );

    function getReportName(value) {
        const role = REPORT_TYPES.find((role) => role.value === value);
        return role ? role.option : value;
    }

    function addReport(newReport) {
        if (newReport.report_type === null) {
            toastError("Please select a report type");
            return;
        }
        if (newReport.due_date === null) {
            toastError("Please select a due date for the report");
            return;
        }
        ClientReports.add(newReport);
    }

    function removeReport(report) {
        ClientReports.remove(report);
    }

    function markDone(reportId) {
        const updatedReport = { id: reportId, is_done: true };
        jspa("/Client/Report", "markDone", updatedReport)
            .then((result) => {
                ClientReports.update((reports) => {
                    return reports.map((report) =>
                        report.id === reportId
                            ? { ...report, is_done: true }
                            : report,
                    );
                });
            })
            .catch((error) => {
                console.log(error);
                toastError("Failed to mark report as done");
            });
    }

    function markUndone(reportId) {
        const updatedReport = { id: reportId, is_done: false };

        jspa("/Client/Report", "markUndone", updatedReport)
            .then((result) => {
                ClientReports.update((reports) => {
                    return reports.map((report) =>
                        report.id === reportId
                            ? { ...report, is_done: false }
                            : report,
                    );
                });
            })
            .catch((error) => {
                console.log(error);
                toastError("Failed to mark report as not done");
            });
    }

    function showAddReport(report) {
        ModalStore.set({
            label: "Add Service Agreement",
            show: true,
            props: report,
            component: ReportForm,
            action_label: "Add",
            action: () => addReport(report),
        });
    }
</script>

<div class="flex sm:flex-row flex-col sm:items-center mt-6 mb-1">
    <h3 class="text-slate-800 font-bold mx-2">Reports</h3>
    <Role roles={["admin"]}>
        <button
            class="text-xs text-indigo-600 hover:underline text-left mx-2"
            on:click={() => showAddReport(report)}
        >
            <PlusIcon class="w-4 h-4 inline" /> Add Report
        </button>
    </Role>
</div>

{#if $ClientReports.length === 0}
    <div class="text-sm text-center text-gray-500 p-4 pt-2">
        <div
            class="flex justify-center items-center h-8 w-8 text-gray-300 mx-auto m-2"
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
                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
                ></path>
            </svg>
        </div>
        <div>No reports have been added for this client</div>
    </div>
{:else}
    <ul
        class="bg-white rounded-lg border border-indigo-100 w-full text-slate-800"
    >
        {#each $ClientReports as report, index (index)}
            <li
                in:slide={{ duration: 200 }}
                out:slide={{ duration: 200 }}
                class="group flex justify-between items-center hover:bg-indigo-50 px-4 py-2 focus:outline-none focus:ring-0 focus:bg-indigo-600 focus:text-slate-600 transition duration-500 {$ClientReports.length -
                    1 ==
                index
                    ? 'rounded-b-lg '
                    : ''}border-b border-indigo-100 w-full
                        
                        
                        
                        "
            >
                <div class="flex flex-row items-top">
                    <Role roles={["admin"]}>
                        <div class="w-auto my-auto">
                            <input
                                class=" appearance-none h-4 w-4 border border-slate-300 rounded-sm bg-white checked:bg-indigo-600 checked:border-indigo-600 focus:outline-none transition duration-200 bg-no-repeat bg-center bg-contain float-left mr-2 mb-2 cursor-pointer"
                                type="checkbox"
                                bind:checked={report.is_done}
                                on:change={(event) =>
                                    event.target.checked
                                        ? markDone(report.id)
                                        : markUndone(report.id)}
                            />
                        </div>
                    </Role>
                    <div>
                        <div class="text-sm font-bold text-slate-800">
                            {getReportName(report.report_type)}
                            {#if report.is_done}
                                <span class="text-xs">- Done</span>
                            {/if}
                        </div>
                        <div class="text-xs">
                            {formatDate(report.due_date)}
                            {#if !report.is_done}
                                <span class="italic"
                                    >- {timeAgo(report.due_date)}</span
                                >{/if}
                        </div>
                    </div>
                </div>
                <Role roles={["admin"]}>
                    <button
                        on:click={() => removeReport(report)}
                        type="button"
                        class="flex p-1 rounded-full text-center text-sm font-semibold text-indigo-600 hover:bg-indigo-600 hover:text-white transition duration-300"
                        ><XMarkIcon class="h-4 w-4 inline m-0 p-0" /></button
                    >
                </Role>
            </li>
        {/each}
    </ul>
{/if}
