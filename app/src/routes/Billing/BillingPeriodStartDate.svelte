<script>
    import { jspa } from "@shared/jspa.js";

    let billing_period_start_date;

    jspa("/SIL/Billing", "getBillingPeriodStartDate", {}).then((result) => {
        billing_period_start_date = result.result;
    });

    function setBillingPeriodStartDate() {
        jspa("/SIL/Billing", "setBillingPeriodStartDate", {
            date: billing_period_start_date,
        }).then((result) => {
            // shifts = result.result;
            alert("Billing Period Start Date Set");
        });
    }
</script>

<div
    class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 items-center"
>
    <div class="flex-grow">
        <div
            class="bg-white w-full rounded-lg py-1 p-2 border border-gray-200 mb-2"
        >
            <label class="text-xs opacity-50 p-0 m-0 block"
                >Billing Period Start Date</label
            >
            <input
                type="date"
                class="w-full p-0 m-0 outline-none bg-white"
                bind:value={billing_period_start_date}
            />
        </div>
    </div>

    <button
        on:click={() => setBillingPeriodStartDate()}
        type="button"
        class="w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 mb-2"
        >Set
    </button>
</div>
