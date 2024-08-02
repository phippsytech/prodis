<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";

    export let value;

    let paygrades = [];
    let paygrade_list = [];

    onMount(async () => {
        await listPayGrades();
    });

    async function listPayGrades() {
        jspa("/Payroll/PayGrade", "listPayGrades", {}).then((result) => {
            paygrades = result.result;

            paygrades.forEach((paygrade) => {
                let options = {
                    option: paygrade.name,
                    value: paygrade.id,
                };

                paygrade_list.push(options);

                paygrade_list.sort(function (a, b) {
                    const nameA = a.option.toUpperCase(); // ignore upper and lowercase
                    const nameB = b.option.toUpperCase(); // ignore upper and lowercase
                    if (nameA < nameB) return -1;
                    if (nameA > nameB) return 1;
                    return 0; // names must be equal
                });

                paygrade_list = paygrade_list;
            });
        });
    }
</script>

<FloatingSelect bind:value label="SIL Pay Grade" options={paygrade_list} />
