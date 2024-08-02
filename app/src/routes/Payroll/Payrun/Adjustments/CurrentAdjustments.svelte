<script>
    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";
    import { jspa } from "@shared/jspa.js";
    import { formatCurrency } from "@shared/utilities.js";

    // export let staff_id;

    let adjustments = [];

    let hasFocus;

    // let display="pending";

    onMount(() => {
        listAdjustments();
    });

    export function listAdjustments() {
        adjustments = [];
        jspa("/Payroll/Payrun", "listAdjustments", {}).then((result) => {
            adjustments = result.result;
        });
    }

    function deleteAdjustment(adjustment_id) {
        jspa("/Payroll/Payrun/Adjustment", "deleteAdjustment", {
            id: adjustment_id,
        }).then((result) => {
            adjustments = adjustments.filter(
                (adjustment) => adjustment.id !== adjustment_id,
            );
            // adjustments = result.result
        });
    }

    // $: if(staff_id){
    //     // staff_id;
    //     listTrips();
    // }
</script>

{#if adjustments.length > 0}
    <Container>
        <table class="table w-full">
            <tr class="border-b-2 border-gray-300">
                <th class="p-2 text-left">Staff Name</th>
                <th class="p-2 text-left">Pay Item Type</th>
                <th class="p-2 text-left">Pay Item</th>
                <th class="p-2 text-right">Quantity</th>
                <th class="p-2 text-right">Rate</th>
                <th class="p-2 text-left"></th>
            </tr>
            {#each adjustments as item, index}
                <tr
                    class="border-b border-gray-200"
                    class:bg-indigo-600={hasFocus == item.id}
                    on:focusin={() => (hasFocus = item.id)}
                    on:focusout={() => (hasFocus = false)}
                >
                    <td class="p-2" class:text-white={hasFocus == item.id}>
                        {item.staff_name}
                    </td>
                    <td class="p-2">{item.pay_item_type}</td>
                    <td class="p-2">{item.pay_item_name}</td>
                    <td class="p-2 text-right">{item.quantity}</td>
                    <td class="p-2 text-right">{formatCurrency(item.rate)}</td>
                    <td class="p-2 text-right">
                        <button
                            on:click={() => deleteAdjustment(item.id)}
                            type="button"
                            class="inline-flex justify-center rounded-md 600 px-1 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
                        >
                            X</button
                        >
                    </td>
                </tr>
            {/each}
        </table>
    </Container>
{/if}
