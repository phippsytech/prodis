<script>
    import {
        convertMicrosoftDate,
        formatDate,
        formatCurrency,
    } from "@shared/utilities.js";

    import { get } from "svelte/store";
    import { payItemsStore } from "@shared/payItemsStore";

    function getPayItemNameByRef(xeroRef) {
        const payItems = get(payItemsStore);
        const item = payItems.find((item) => item.payItemXeroRef === xeroRef);
        return item ? item.payItemName : null;
    }

    export let payitems = [];
    export let type = "";
</script>

{#if payitems.length > 0}
    <table class="min-w-full divide-y divide-gray-300">
        <thead>
            <tr>
                {#if type == "earnings"}
                    <th
                        scope="col"
                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                        >Earnings</th
                    >
                    <th
                        scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                        >Quantity</th
                    >
                    <th
                        scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                        >Rate</th
                    >
                    <th
                        scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                        >Total</th
                    >
                {/if}

                {#if type == "leave"}
                    <th
                        scope="col"
                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                        >Leave</th
                    >
                    <th
                        scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                        >Quantity</th
                    >
                    <th
                        scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                        >Rate</th
                    >
                    <th
                        scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                    ></th>
                {/if}

                {#if type == "leave_accrual"}
                    <th
                        scope="col"
                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                        >Leave Accrual</th
                    >
                    <th
                        scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                        >Quantity</th
                    >
                {/if}

                {#if type == "deductions"}
                    <th
                        scope="col"
                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                        >Deductions</th
                    >
                    <th
                        scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                        >Amount</th
                    >
                {/if}

                {#if type == "reimbursements"}
                    <th
                        scope="col"
                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                        >Reimbursements</th
                    >
                    <th
                        scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                        >Quantity</th
                    >
                    <th
                        scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right"
                        >Rate</th
                    >
                {/if}
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            {#each payitems as payitem}
                <tr>
                    {#if type == "earnings"}
                        <td
                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900 group-hover:text-indigo-600 sm:pl-0"
                            >{getPayItemNameByRef(payitem.EarningsRateID)}</td
                        >

                        {#if payitem.FixedAmount}
                            <td
                                class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                                >&nbsp;</td
                            >
                            <td
                                class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                                >&nbsp;</td
                            >
                            <td
                                class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                                >{formatCurrency(payitem.FixedAmount)}</td
                            >
                        {:else}
                            <td
                                class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                                >{payitem.NumberOfUnits}</td
                            >
                            <td
                                class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                                >{formatCurrency(payitem.RatePerUnit)}</td
                            >
                            <td
                                class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                                >{formatCurrency(
                                    payitem.RatePerUnit * payitem.NumberOfUnits,
                                )}</td
                            >
                        {/if}
                    {/if}

                    {#if type == "leave"}
                        <td
                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900 group-hover:text-indigo-600 sm:pl-0"
                            >{getPayItemNameByRef(payitem.EarningsRateID)}</td
                        >
                        <td
                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                            >{payitem.NumberOfUnits}</td
                        >
                        <td
                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                            >{formatCurrency(payitem.RatePerUnit)}</td
                        >
                        <td
                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                            >{formatCurrency(
                                payitem.RatePerUnit * payitem.NumberOfUnits,
                            )}</td
                        >
                    {/if}

                    {#if type == "leave_accrual"}
                        <td
                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900 group-hover:text-indigo-600 sm:pl-0"
                            >{getPayItemNameByRef(payitem.LeaveTypeID)}</td
                        >
                        <td
                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                            >{payitem.NumberOfUnits}</td
                        >
                    {/if}

                    {#if type == "deductions"}
                        <td
                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900 group-hover:text-indigo-600 sm:pl-0"
                            >{getPayItemNameByRef(payitem.DeductionTypeID)}</td
                        >
                        <td
                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                            >{formatCurrency(payitem.Amount)}</td
                        >
                    {/if}

                    {#if type == "reimbursements"}
                        <td
                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900 group-hover:text-indigo-600 sm:pl-0"
                            >{getPayItemNameByRef(payitem.EarningsRateID)}</td
                        >
                        <td
                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                            >{payitem.NumberOfUnits}</td
                        >
                        <td
                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                            >{formatCurrency(payitem.RatePerUnit)}</td
                        >
                        <td
                            class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 text-right"
                            >{formatCurrency(
                                payitem.RatePerUnit * payitem.NumberOfUnits,
                            )}</td
                        >
                    {/if}
                </tr>
            {/each}
        </tbody>
    </table>
{/if}
