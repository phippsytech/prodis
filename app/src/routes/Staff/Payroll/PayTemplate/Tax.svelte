<script>
    import { XMarkIcon } from "heroicons-svelte/24/outline";
    import Container from "@shared/Container.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import TaxTypeSelector from "@app/routes/Payroll/TaxTypeSelector.svelte";

    import { formatCurrency } from "@shared/utilities.js";
    import { jspa } from "@shared/jspa.js";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";

    export let staff_id;

    let line_item = {};

    let line_items = [];

    listTaxLines(staff_id);

    function listTaxLines(staff_id) {
        jspa("/Payroll/Template/TaxLine", "listTaxLinesByStaffId", {
            staff_id: staff_id,
        }).then((result) => {
            line_items = result.result;
        });
    }

    function addTaxLine(line_item) {
        line_item.staff_id = staff_id;
        // line_item.id = line_items.length + 1;
        // line_item.manual_tax_type = manual_tax_type;

        jspa("/Payroll/Template/TaxLine", "addTaxLine", line_item)
            .then((result) => {
                line_items.push({ ...line_item });
                line_items = line_items;
                toastSuccess("Tax Line Added");
            })
            .catch((error) => {
                toastError(error.message);
            });
    }

    function deleteTaxLine(line_item_id) {
        jspa("/Payroll/Template/TaxLine", "deleteTaxLine", { id: line_item_id })
            .then((result) => {
                line_items = line_items.filter(
                    (item) => item.id != line_item_id,
                );
                toastSuccess("Tax Line Removed");
            })
            .catch((error) => {
                toastError(error.message);
            });
    }
</script>

<Container>
    <div class="text-xs opacity-50 mb-2">Tax</div>

    <table class="min-w-full">
        <thead class="border-b">
            <tr>
                <th class="text-left">Tax Type</th>
                <th class="text-left">Amount</th>

                <th class="text-right"></th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            {#each line_items as item, index}
                <tr class="hover:bg-indigo-50 my-2">
                    <!-- <td class="px-2 py-4">{item.name}</td> -->
                    <td class="px-2 py-4">{item.manual_tax_type}</td>
                    <td class="px-2"> {formatCurrency(item.amount)}</td>
                    <td class="px-2 text-right">
                        <button
                            on:click={() => deleteTaxLine(item.id)}
                            type="button"
                            class="inline-block rounded-md bg-indigo-600 px-2 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                            ><XMarkIcon
                                class="w-5 h-5  cursor-pointer"
                            /></button
                        >
                    </td>
                </tr>
            {/each}

            <tr>
                <td class="py-2">
                    <TaxTypeSelector bind:value={line_item.manual_tax_type} />
                </td>

                <td>
                    <FloatingInput
                        label="Amount"
                        type="number"
                        bind:value={line_item.amount}
                    />
                </td>
                <td colspan="2" class="flex-grow">
                    <button
                        on:click={() => addTaxLine(line_item)}
                        type="button"
                        class="w-full inline-flex justify-center rounded-md bg-indigo-600 px-3 py-4 text-sm font-semibold text-white hover:bg-indigo-500 mb-2"
                        >Add Tax Line</button
                    >
                </td>
            </tr>
        </tbody>
    </table>
</Container>
