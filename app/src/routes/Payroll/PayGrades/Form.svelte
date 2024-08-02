<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";
    import { onMount } from "svelte";

    import { BreadcrumbStore } from "@shared/stores.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

    export let paygrade_id = null;

    let stored_paygrade = {};
    let paygrade = {};
    let mounted = false;
    let show = false;

    // paygrade.day = "5c950435-f229-406d-8b06-5e9b4e7ce2b2";

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
        if (paygrade_id) {
            jspa("/Payroll/PayGrade", "getPayGrade", { id: paygrade_id })
                .then((result) => {
                    paygrade = result.result;
                    // Make a copy of the object
                    stored_paygrade = Object.assign({}, paygrade);
                })
                .catch((error) => {});
        }

        mounted = true;
    });

    function undo() {
        paygrade = Object.assign({}, stored_paygrade);
    }

    function save() {
        if (paygrade_id) {
            paygrade.id = paygrade_id;
            jspa("/Payroll/PayGrade", "updatePayGrade", paygrade).then(
                (result) => {
                    stored_paygrade = Object.assign({}, paygrade); // Make a copy of the object
                },
            );
        } else {
            jspa("/Payroll/PayGrade", "addPayGrade", paygrade).then(
                (result) => {
                    stored_paygrade = Object.assign({}, paygrade); // Make a copy of the object
                },
            );
        }
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: false,
                show: !(
                    JSON.stringify(paygrade) === JSON.stringify(stored_paygrade)
                ),
                undo: () => undo(),
                save: () => save(),
            });
        }
    }
</script>

<FloatingInput
    bind:value={paygrade.name}
    label="Pay Grade Name"
    placeholder="eg: Level 1 SIL"
/>

<!-- Monday - Friday 06:00 - 20:00 -->
<FloatingSelect
    bind:value={paygrade.day}
    label="Day Earning Item"
    options={earnings_rates_list}
/>

<!-- Monday - Friday 20:00 - 22:00 -->
<FloatingSelect
    bind:value={paygrade.evening}
    label="Evening Earning Item"
    options={earnings_rates_list}
/>

<!-- Monday - Friday 22:00 - 06:00 -->
<FloatingSelect
    bind:value={paygrade.night}
    label="Night Earning Item"
    options={earnings_rates_list}
/>

<!-- Saturday 06:00 - 22:00 -->
<FloatingSelect
    bind:value={paygrade.saturday}
    label="Saturday Earning Item"
    options={earnings_rates_list}
/>

<!-- Saturday 06:00 - 22:00 -->
<FloatingSelect
    bind:value={paygrade.sunday}
    label="Sunday Earning Item"
    options={earnings_rates_list}
/>

<!-- Public Holiday 06:00 - 22:00 -->
<FloatingSelect
    bind:value={paygrade.public_holiday}
    label="Public Holiday Earning Item"
    options={earnings_rates_list}
/>

<!-- Passive 22:00 - 06:00 -->
<FloatingSelect
    bind:value={paygrade.sleep_over}
    label="Sleep-over Earning Item"
    options={earnings_rates_list}
/>
