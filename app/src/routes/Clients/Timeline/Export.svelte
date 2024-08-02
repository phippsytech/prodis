<script>
    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import {
        formatDate,
        getMonday,
        getDatePlusDays,
    } from "@shared/utilities.js";
    import { slide, fade } from "svelte/transition";
    import { quintOut } from "svelte/easing";
    import { getClient } from "@shared/api.js";
    import { RolesStore } from "@shared/stores.js";

    export let params;

    let start_date = getMonday();
    let end_date = getDatePlusDays(start_date, 7);

    let client_id = params.client_id;
    let client = {};
    // let shifts = [];

    let ready = false;

    onMount(async () => {
        client = await getClient(client_id);
        BreadcrumbStore.set({
            path: [
                { name: "Clients", url: "/clients" },
                { name: client.name, url: "/clients/" + client.id },
            ],
        });
        ready = true;
    });

    let internal_reports = [
        "ParentalContact",
        "Parental Contact",
        "AdhocRiskAssessment",
        "RiskAssessment",
        "Risk Assessment",
        "RiskAssessment",
        "Expense",
    ];

    let report_types = [];

    function getReportTypes(start_date, end_date, participant_id) {
        jspa("/SIL/House/Form", "getAvailableReportTypes", {
            start_date: start_date,
            end_date: end_date,
            participant_id: participant_id,
        }).then((result) => {
            report_types = result.result;
            if ($RolesStore.includes("stakeholder")) {
                report_types = report_types.filter(
                    (type) => !internal_reports.includes(type.report_type),
                );
                report_types = report_types;
            }
        });
    }

    function stripSpaces(string) {
        return string.replace(/\s/g, "");
    }

    function downloadCSV(start_date, end_date, client_id, report_type) {
        jspa("/SIL/House/Form", "getCSVExport", {
            start_date: start_date,
            end_date: end_date,
            participant_id: client_id,
            report_type: report_type,
        })
            .then((result) => {
                const blob = new Blob([result.result], { type: "text/csv" });
                // @ts-ignore
                let filename =
                    stripSpaces(
                        report_type +
                            "-" +
                            formatDate(start_date) +
                            "-" +
                            formatDate(end_date),
                    ) + ".csv";
                if (window.navigator.msSaveOrOpenBlob) {
                    // @ts-ignore

                    window.navigator.msSaveBlob(blob, filename);
                } else {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = filename;
                    a.click();
                    URL.revokeObjectURL(url);
                }
            })
            .catch((result) => {});
    }

    $: {
        if (start_date && end_date) {
            const dateRegex = /^\d{4}-\d{2}-\d{2}$/;

            if (!dateRegex.test(start_date) || !dateRegex.test(end_date)) {
                // Display an error or handle the invalid date format here
            } else {
                getReportTypes(start_date, end_date, client_id);
            }
        }
    }
</script>

<!-- I think overview should be dashboard throughout -->

<Container>
    <div
        class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4"
        style="color:#220055;"
    >
        Timeline CSV Export
    </div>

    <div class="text-sm mb-1 font-bold opacity-50">Period</div>
    <div
        class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 items-center"
    >
        <div class="flex-grow">
            <div
                class="bg-white w-full rounded-lg py-1 p-2 border border-gray-200 mb-2"
            >
                <label class="text-xs opacity-50 p-0 m-0 block"
                    >Start Date</label
                >
                <input
                    type="date"
                    class="w-full p-0 m-0 outline-none bg-white"
                    bind:value={start_date}
                />
            </div>
        </div>

        <div class="flex-grow">
            <div
                class="bg-white w-full rounded-lg py-1 p-2 border border-gray-200 mb-2"
            >
                <label class="text-xs opacity-50 p-0 m-0 block">End Date</label>
                <input
                    type="date"
                    class="w-full p-0 m-0 outline-none bg-white"
                    bind:value={end_date}
                />
            </div>
        </div>
    </div>
</Container>

<div
    in:fade={{ delay: 250, duration: 250, easing: quintOut }}
    out:fade={{ duration: 50, easing: quintOut }}
    class="hidden sm:block"
>
    <div
        class="grid grid-cols-2 items-center py-1 text-xs font-medium text-gray-500"
        style="grid-template-columns: 6fr 1fr;"
    >
        <div class="grid grid-cols-3 gap-4 items-center">
            <div>Report Type</div>
            <div>No. of Records</div>
        </div>
    </div>
</div>

<ul class="border-y border-gray-200 w-full mb-4">
    {#each report_types as item, index}
        <li
            in:slide|global={{ duration: 250 }}
            out:slide|global={{ duration: 250 }}
            class="grid grid-cols-2 text-sm border-b border-gray-200 items-center hover:bg-indigo-600 hover:text-white py-1 cursor-pointer"
            style="grid-template-columns: 6fr 1fr; "
            on:click={() =>
                downloadCSV(start_date, end_date, client_id, item.report_type)}
        >
            <div
                class="grid grid-cols-1 gap-0 sm:gap-4 sm:grid-cols-3 w-full items-center"
            >
                <div class="font-medium">{item.report_type}</div>

                <div class="">{item.count}</div>
            </div>
        </li>
    {/each}
</ul>
