<script>
    import {
        convertMicrosoftDate,
        formatDate,
        formatCurrency,
    } from "@shared/utilities.js";
    import { jspa } from "@shared/jspa.js";
    import { push } from "svelte-spa-router";
    import Container from "@shared/Container.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import PayslipGenerator from "./PayslipGenerator.svelte";
    import { SpinnerStore } from "@app/Overlays/stores.js";

    export let params;

    let payrun_id = params.payrun_id;

    let payrun = {
        Payslips: [],
    };

    SpinnerStore.set({ show: true, message: "Fetching Pay Run from Xero" });

    jspa("/Payroll/Payrun", "getPayrun", { payrun_id: payrun_id })
        .then((result) => {
            payrun = result.result;

            BreadcrumbStore.set({
                path: [
                    { url: "/payroll", name: "Payroll" },
                    { url: "/payroll/payruns", name: "Pay Runs" },
                    { url: null, name: "Pay Run" },
                ],
            });
        })
        .finally(() => {
            SpinnerStore.set({ show: false });
        });
</script>

{#if payrun}
    <div class="px-4 sm:px-6 lg:px-8">
        <h1
            class=" text-3xl leading-6 text-gray-900 font-fredoka-one-regular"
            style="color:#220055;"
        >
            Pay Run
        </h1>
        {#if payrun.PayRunPeriodEndDate}
            <p class="mt-2 text-sm text-gray-700">
                {formatDate(convertMicrosoftDate(payrun.PayRunPeriodStartDate))}
                to {formatDate(
                    convertMicrosoftDate(payrun.PayRunPeriodEndDate),
                )}
            </p>
        {/if}

        {#if payrun.PayRunStatus === "DRAFT"}
            <!-- Payslip generator has been disabled to prevent overwriting data that has been changed in Xero. -->
            <PayslipGenerator bind:payrun />
        {/if}

        {#if payrun.PayRunStatus === "POSTED"}
            POSTED
        {/if}

        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 sm:-mx-6 lg:-mx-8">
                <div
                    class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8"
                >
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="sticky top-28 bg-white">
                            <tr>
                                <th
                                    scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                                    >Employee</th
                                >
                                <th
                                    scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                                    >Wages</th
                                >
                                <th
                                    scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                                    >Deductions</th
                                >
                                <th
                                    scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                                    >Tax</th
                                >
                                <th
                                    scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                                    >Super</th
                                >
                                <th
                                    scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                                    >Reimbursements</th
                                >
                                <th
                                    scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                                    >Net Pay</th
                                >
                                <th
                                    scope="col"
                                    class="relative py-3.5 pl-3 pr-4 sm:pr-0"
                                >
                                    <span class="sr-only">Edit</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            {#if payrun}
                                {#each payrun.Payslips as payslip}
                                    <tr
                                        class="group hover:bg-indigo-50 cursor-pointer"
                                        on:click={() =>
                                            push(
                                                "/payroll/payruns/" +
                                                    payrun.PayRunID +
                                                    "/payslips/" +
                                                    payslip.PayslipID,
                                            )}
                                        title="{payslip.FirstName} {payslip.LastName}"
                                    >
                                        <td
                                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 group-hover:text-indigo-600 sm:pl-0"
                                            >{payslip.FirstName}
                                            {payslip.LastName}</td
                                        >
                                        <td
                                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                                            >{formatCurrency(payslip.Wages)}</td
                                        >
                                        <td
                                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                                            >{formatCurrency(
                                                payslip.Deductions,
                                            )}</td
                                        >
                                        <td
                                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                                            >{formatCurrency(payslip.Tax)}</td
                                        >
                                        <td
                                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                                            >{formatCurrency(payslip.Super)}</td
                                        >
                                        <td
                                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                                            >{formatCurrency(
                                                payslip.Reimbursements,
                                            )}</td
                                        >
                                        <td
                                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                                            >{formatCurrency(
                                                payslip.NetPay,
                                            )}</td
                                        >
                                        <td
                                            class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0"
                                        >
                                            <!-- <a href="/#/" class="text-indigo-600 hover:text-indigo-900">Edit<span class="sr-only">, Lindsay Walton</span></a> -->
                                        </td>
                                    </tr>
                                {/each}
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
{/if}
