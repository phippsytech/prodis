<script>
    import ScaledNumberDisplay from "@shared/PhippsyTech/svelte-ui/ScaledNumberDisplay.svelte";
    import { jspa } from "@shared/jspa.js";

    export let staff_id;

    let leave_balances = [];

    function getLeaveBalances(staff_id) {
        leave_balances = [];
        jspa("/Payroll/Leave", "getLeaveBalances", { staff_id: staff_id }).then(
            (result) => {
                leave_balances = result.result;
            },
        );
    }

    $: {
        getLeaveBalances(staff_id);
    }
</script>

<div class="mb-2">
    <dl
        class="mx-auto grid max-w-7xl grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 lg:px-1 xl:px-0"
    >
        {#each leave_balances as leave_balance}
            <div
                class="flex items-baseline flex-wrap justify-between gap-y-1 gap-x-2 border-t border-gray-900/5 px-4 py-4 sm:px-6 lg:border-t-0 xl:px-8"
            >
                <dt class="text-sm font-light leading-6 text-gray-500">
                    {leave_balance.leave_name}
                </dt>
                <dd class="w-full flex-none">
                    <ScaledNumberDisplay
                        value={leave_balance.number_of_units}
                    />
                    <span class="text-sm font-light text-gray-400"
                        >{leave_balance.type_of_units}</span
                    >
                </dd>
            </div>
        {/each}
    </dl>
</div>
