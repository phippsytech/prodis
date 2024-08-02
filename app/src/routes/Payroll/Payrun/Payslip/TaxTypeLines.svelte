<script>
    import { convertMicrosoftDate, formatDate, formatCurrency } from '@shared/utilities.js';
    
        export let tax_lines = [];
        
    </script>
    
    {#if tax_lines.length > 0}
        <table class="min-w-full divide-y divide-gray-300">
            <thead>
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Tax Type</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                {#each tax_lines as tax_line}
                    <tr>
                        
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900 group-hover:text-indigo-600 sm:pl-0">
                                {#if tax_line.ManualTaxType}Manual - {/if}
                                {tax_line.TaxTypeName}
                                {#if tax_line.Description}<br/><span class="text-xs">{tax_line.Description}</span>{/if}</td>
                        
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 group-hover:text-indigo-600  text-right">{formatCurrency(tax_line.Amount)}</td>
                       
                    </tr>
                {/each}  
            </tbody>
        </table>
    {/if}