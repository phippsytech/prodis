<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";
    import { onMount } from "svelte";

    import { BreadcrumbStore } from "@shared/stores.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

    let stored_km_allowance = {};
    let km_allowance = {};
    let mounted = false;
    let show = false;

    // km_allowance.day = "5c950435-f229-406d-8b06-5e9b4e7ce2b2";

    let earnings_rates;
    let earnings_rates_list = [];

    jspa("/SIL/Payrun", "getPayItems", {}).then((result) => {
        earnings_rates = result.result.EarningsRates;

        earnings_rates.forEach((earnings_rate) => {
            let options = {
                option: earnings_rate.Name,
                value: earnings_rate.EarningsRateID,
            };

            earnings_rates_list.push(options);

            earnings_rates_list.sort(function (a, b) {
                const nameA = a.option.toUpperCase(); // ignore upper and lowercase
                const nameB = b.option.toUpperCase(); // ignore upper and lowercase
                if (nameA < nameB) return -1;
                if (nameA > nameB) return 1;
                return 0; // names must be equal
            });

            earnings_rates_list = earnings_rates_list;
        });
    });

    onMount(async () => {
        jspa("/SIL/km_allowance", "getkm_allowance", {})
            .then((result) => {
                km_allowance = result.result;
                // Make a copy of the object
                stored_km_allowance = Object.assign({}, km_allowance);
            })
            .catch((error) => {
                console.log(error);
            });

        mounted = true;
    });

    function undo() {
        km_allowance = Object.assign({}, stored_km_allowance);
    }

    function save() {
        jspa("/SIL/km_allowance", "updatekm_allowance", km_allowance).then(
            (result) => {
                stored_km_allowance = Object.assign({}, km_allowance); // Make a copy of the object
            },
        );
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: false,
                show: !(
                    JSON.stringify(km_allowance) ===
                    JSON.stringify(stored_km_allowance)
                ),
                undo: () => undo(),
                save: () => save(),
            });
        }
    }
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular px-2"
    style="color:#220055;"
>
    KM Reimbursement
</div>

<p class="px-2 mb-4">
    Reimburse staff for kilometers travelled in their vehicles during a shift
</p>

<FloatingSelect
    bind:value={km_allowance.below_ato}
    label="KM Allowance (Below ATO Rate)"
    options={earnings_rates_list}
/>

<FloatingSelect
    bind:value={km_allowance.above_ato}
    label="KM Allowance (Above ATO Rate)"
    options={earnings_rates_list}
/>
