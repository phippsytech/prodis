<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import { createEventDispatcher } from "svelte";
    const dispatch = createEventDispatcher();
    import { jspa } from "@shared/jspa.js";

    export let value;

    let earnings_items = [];
    let options = [];

    jspa("/Payroll", "getEarningsItems", {}).then((result) => {
        options = [];
        earnings_items = result.result;
        earnings_items = earnings_items.sort((a, b) =>
            a.Name.localeCompare(b.Name),
        );
        earnings_items.forEach((item) => {
            let option = {
                option: item.Name,
                value: item.EarningsRateID,
            };
            options.push(option);
        });
        options = options;
    });

    function handleChange(e) {
        value = earnings_items.find(
            (item) => item.EarningsRateID == e.target.value,
        );
        dispatch("change", value);
    }
</script>

<FloatingSelect on:change={handleChange} label="Earnings Item" {options} />
