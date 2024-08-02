<script>
    import Container from "@shared/Container.svelte";
    import FileUploader from "@shared/FileUploader.svelte";
    import StaffSelector from "@shared/StaffSelector.svelte";

    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";

    export let params;

    BreadcrumbStore.set({
        path: [
            { url: "/payroll", name: "Payroll" },
            { url: "/payroll/payruns", name: "Pay Runs" },
            { url: null, name: "Pay Run" },
        ],
    });

    let payrun_id = params.payrun_id;
    let csv = {
        payrun_id: payrun_id,
    };

    let salary_packaging_deductions = [];

    function upload(csv) {
        SpinnerStore.set({
            show: true,
            message: "Uploading Salary Packaging CSV",
        });
        jspa("/Payroll/Payrun/SalaryPackagingDeduction", "uploadCSV", csv)
            .then((result) => {
                getSalaryPackagingDeductions();
                toastSuccess("Salary Packaging CSV uploaded successfully");
            })
            .catch((error) => {
                csv = {};
                toastError(error.error_message);
            })
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    }

    function download(csv) {
        jspa("/Payroll/Payrun/SalaryPackagingDeduction", "downloadCSV", csv)
            .then((result) => {
                const filename = result.filename;
                const blob = new Blob([result.result], { type: "text/csv" });
                // @ts-ignore
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

    async function getSalaryPackagingDeductions() {
        jspa(
            "/Payroll/Payrun/SalaryPackagingDeduction",
            "listSalaryPackagingDeductions",
            { payrun_id: payrun_id },
        ).then((result) => {
            salary_packaging_deductions = result.result;
            // toastSuccess("Salary Packaging CSV uploaded successfully");
        });
    }

    function updatePid(full_name, newPid) {
        salary_packaging_deductions.forEach((deduction, index) => {
            if (deduction.full_name == full_name) {
                salary_packaging_deductions[index].pid = newPid;
                saveSalaryPackagingDeductions(
                    salary_packaging_deductions[index],
                );
            }
        });
    }

    function saveSalaryPackagingDeductions(deduction) {
        jspa(
            "/Payroll/Payrun/SalaryPackagingDeduction",
            "updateSalaryPackagingDeduction",
            deduction,
        );
    }
</script>

<div class="px-4">
    <h2
        class=" text-2xl leading-7 text-indigo-900 sm:truncate tracking-tight font-fredoka-one-regular"
    >
        Salary Packaging
    </h2>
</div>

<div class="bg-slate-100 rounded-md px-4 py-3 my-2">
    {#if salary_packaging_deductions.length == 0}
        <div class="text-sm text-gray-500">Salary Packaging File</div>
        <FileUploader bind:value={csv.data} bind:name={csv.name} />

        <button
            class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 px-4 rounded"
            on:click={() => {
                upload(csv);
            }}>Upload</button
        >
    {/if}
    {#if salary_packaging_deductions.length > 0}
        <button
            class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 px-4 rounded"
            on:click={() => {
                download(csv);
            }}>Download</button
        >
    {/if}
</div>

<section class="mt-12">
    <ol
        class="mt-2 divide-y divide-indigo-100 text-sm leading-6 text-slate-700"
    >
        {#await getSalaryPackagingDeductions()}
            <li class="py-4 flex">
                <p class="mt-2 flex-auto">Nothing on today’s schedule</p>
            </li>
        {:then}
            {#each salary_packaging_deductions as deduction}
                <li class="py-4 grid grid-cols-7 gap-4">
                    <div class="">{deduction.ncb}</div>
                    <div class="">{deduction.full_name}</div>
                    <div class="">{deduction.total_amount}</div>
                    <div class="">{deduction.adjusted_amount}</div>
                    <div class="">{deduction.account_type}</div>
                    <div class="">{deduction.status}</div>
                    <div class="">
                        <StaffSelector
                            bind:staff_id={deduction.pid}
                            on:change={(e) =>
                                updatePid(deduction.full_name, e.target.value)}
                        />
                    </div>
                </li>
            {:else}
                <li class="py-4">
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
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"
                                ></path>
                            </svg>
                        </div>
                        <div>
                            Salary Packaging has not been uploaded for this pay
                            run.
                        </div>
                    </div>
                </li>
            {/each}
        {/await}
    </ol>
</section>
