<script>
    import { formatDate } from "@shared/utilities.js";

    export let line_items = [];

    let claim_types = [
        { option: "Direct Service", value: "" },
        { option: "Cancellation", value: "CANC" },
        { option: "NDIA Required Report", value: "REPW" },
        { option: "Provider Travel", value: "TRAN" },
        {
            option: "Non-Face-to-Face Services",
            value: "NF2F",
        },
    ];

    function getOption(value) {
        if (value === null) value = "";
        let option = "";
        claim_types.forEach((item) => {
            if (item.value == value) {
                option = item.option;
            }
        });
        return option;
    }
</script>

<table class="min-w-full">
    <thead class="border-b">
        <tr>
            <th class="text-left">Date</th>
            <th class="text-left"
                >Service <span class="text-xs">(Claim Type)</span></th
            >

            <th class="text-left">Quantity x Unit Price</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    {#each line_items as item, index}
        <tr class="hover:bg-indigo-50">
            <td class="px-2">{formatDate(item.SupportsDeliveredFrom)}</td>
            <td class="px-2">
                {item.ServiceCode}
                <span class="text-xs"
                    >({getOption(item.ClaimType)}) {item.ServiceBillingUnit}</span
                >
            </td>

            <td class="px-2">
                {item.Quantity} x {parseFloat(item.UnitPrice).toLocaleString(
                    "en-AU",
                    { style: "currency", currency: "AUD" },
                )}</td
            >
            <td class="px-2 text-right">
                {(item.Quantity * item.UnitPrice).toLocaleString("en-AU", {
                    style: "currency",
                    currency: "AUD",
                })}</td
            >
        </tr>
    {/each}
</table>
