<script>
    import { XMarkIcon } from "heroicons-svelte/24/outline";
    import Container from "@shared/Container.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import EarningsItemSelector from "@app/routes/Payroll/EarningsItemSelector.svelte";

    import { formatCurrency } from "@shared/utilities.js";
    import { jspa } from "@shared/jspa.js";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";

    export let staff_id;

    let line_item = {};

    let earnings_item = {
        Name: null,
        EarningsRateID: null,
        RatePerUnit: null,
    };

    let line_items = [];

    listEarningsLines(staff_id);

    function listEarningsLines(staff_id) {
        jspa("/Payroll/Template/EarningsLine", "listEarningsLinesByStaffId", {
            staff_id: staff_id,
        }).then((result) => {
            line_items = result.result;
        });
    }

    function addEarningsLine(line_item) {
        line_item.staff_id = staff_id;
        line_item.id = line_items.length + 1;
        line_item.name = earnings_item.Name;
        line_item.earnings_rate_id = earnings_item.EarningsRateID;
        if (line_item.rate == null) {
            line_item.rate = earnings_item.RatePerUnit;
        }

        jspa("/Payroll/Template/EarningsLine", "addEarningsLine", line_item)
            .then((result) => {
                line_items.push({ ...line_item });
                line_items = line_items;
                toastSuccess("Earnings Line Added");
            })
            .catch((error) => {
                toastError(error.message);
            });
    }

    function deleteEarningsLine(line_item_id) {
        jspa("/Payroll/Template/EarningsLine", "deleteEarningsLine", {
            id: line_item_id,
        })
            .then((result) => {
                line_items = line_items.filter(
                    (item) => item.id != line_item_id,
                );
                toastSuccess("Earnings Line Removed");
            })
            .catch((error) => {
                toastError(error.message);
            });
    }

    function onEarningsItemSelected(e) {
        line_item.rate = e.detail.RatePerUnit;
    }
</script>

<Container>
    <div class="text-xs opacity-50 mb-2">Earnings</div>

    <table class="min-w-full">
        <thead class="border-b">
            <tr>
                <th class="text-left">Earnings Item</th>
                <th class="text-left">Quantity</th>
                <th class="text-left">Rate</th>
                <th class="text-right">Total</th>
                <th class="text-right"></th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            {#each line_items as item, index}
                <tr class="hover:bg-indigo-50 my-2">
                    <td class="px-2 py-4">{item.name}</td>
                    <td class="px-2"> {item.quantity}</td>
                    <td class="px-2"> {formatCurrency(item.rate)}</td>
                    <td class="px-2 text-right">
                        {formatCurrency(item.quantity * item.rate)}</td
                    >
                    <td class="px-2 text-right">
                        <button
                            on:click={() => deleteEarningsLine(item.id)}
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
                    <EarningsItemSelector
                        bind:value={earnings_item}
                        on:change={onEarningsItemSelected}
                    />
                </td>
                <td>
                    <FloatingInput
                        label="Quantity"
                        type="number"
                        bind:value={line_item.quantity}
                    />
                </td>
                <td>
                    <FloatingInput
                        label="Rate"
                        type="number"
                        bind:value={line_item.rate}
                        placeholder={earnings_item.RatePerUnit}
                    />
                </td>
                <td colspan="2" class="flex-grow">
                    <button
                        on:click={() => addEarningsLine(line_item)}
                        type="button"
                        class="w-full inline-flex justify-center rounded-md bg-indigo-600 px-3 py-4 text-sm font-semibold text-white hover:bg-indigo-500 mb-2"
                        >Add Earnings Line
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</Container>
