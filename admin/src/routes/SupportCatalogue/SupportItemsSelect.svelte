<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";

    export let value;
    export let location;
    export let unit;
    export let max_rate;
    export let name;

    let mounted = false;
    let support_items = [];
    let option_list = [];

    jspa("/SupportItem", "listSupportItems", {})
        .then((result) => {
            support_items = result.result;

            let selected = false;

            support_items.forEach((item) => {
                let options = {
                    option:
                        item.support_item_number +
                        " : " +
                        item.support_item_name +
                        " (" +
                        item.registration_group_name +
                        ")",
                    value: item.support_item_number,
                    selected: false,
                };

                if (value && item.support_item_number == value) {
                    options.selected = true;
                    selected = true;
                }
                option_list.push(options);
            });

            if (!selected) {
                value = "Select NDIS Support Item"; // unset the selected client_id
            } else {
                setSupportItem(value);
            }

            option_list.sort(function (a, b) {
                const nameA = a.option.toUpperCase(); // ignore upper and lowercase
                const nameB = b.option.toUpperCase(); // ignore upper and lowercase
                if (nameA < nameB) return -1;
                if (nameA > nameB) return 1;
                return 0; // names must be equal
            });

            option_list = option_list;
            mounted = true;
        })
        .catch((error) => {
            // error_message = error.error_message;
        })
        .finally(() => {
            // uploading = false;
        });

    function setSupportItem(support_item_number) {
        let item = support_items.find(
            (item) => item.support_item_number == support_item_number,
        );

        name = item.support_item_name;
        /*
                units:
                H = hourly
                E = each
                D = day
                YR = yearly <- but doesn't make sense really.
                MON = monthly
            */

        switch (item.unit) {
            case "H":
                unit = "hour";
                break;
            case "E":
                unit = "each";
                break;
            case "D":
                unit = "day";
                break;
            case "YR":
                unit = "year";
                break;
            case "MON":
                unit = "month";
                break;
        }

        max_rate = item[location];
    }

    $: {
        if (
            value != null &&
            value != "Select NDIS Support Item" &&
            location &&
            mounted
        ) {
            setSupportItem(value);
        }
    }
</script>

<FloatingSelect
    on:change={(e) => setSupportItem(e.target.value)}
    bind:value
    label="NDIS Support Item"
    instruction="Select NDIS Support Item"
    options={option_list}
/>
