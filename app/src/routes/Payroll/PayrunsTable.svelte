<script>
    import { convertMicrosoftDate, formatDate, formatCurrency } from '@shared/utilities.js';
    import {push} from 'svelte-spa-router'
    export let payruns =[]

</script>

{#if payruns.length === 0}
            <div class="py-4 text-sm text-gray-500">No payruns found</div>
{:else}

       
<table class="min-w-full divide-y divide-gray-300">
    <thead>
        <tr>
            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Period</th>
            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Payment Date</th>
            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right">Wages</th>
            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right">Deductions</th>
            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right">Tax</th>
            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right">Super</th>
            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right">Reimbursement</th>
            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right">Net Pay</th>
            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
            <span class="sr-only">View</span>
            </th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        {#each payruns as payrun}
            <tr class="group hover:bg-indigo-50 cursor-pointer" on:click={()=>push("/payroll/payruns/"+payrun.PayRunID)} title="Fornight ending {formatDate(convertMicrosoftDate(payrun.PayRunPeriodEndDate))}">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 group-hover:text-indigo-600 sm:pl-0">Fornight ending {formatDate(convertMicrosoftDate(payrun.PayRunPeriodEndDate))}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600 ">{formatDate(convertMicrosoftDate(payrun.PaymentDate))}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600  text-right">{formatCurrency(payrun.Wages)}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600  text-right">{formatCurrency(payrun.Deductions)}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600  text-right">{formatCurrency(payrun.Tax)}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600  text-right">{formatCurrency(payrun.Super)}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600  text-right">{formatCurrency(payrun.Reimbursement)}</td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600  text-right">{formatCurrency(payrun.NetPay)}</td>
                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                
                </td>
            </tr>
        {/each}  
        
    </tbody>
</table>
{/if}
