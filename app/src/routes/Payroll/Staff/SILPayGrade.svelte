<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";

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

<select class="p-1" bind:value>
    {#each paygrade_list as paygrade}
        <option value={paygrade.value}>{paygrade.option}</option>
    {/each}
</select>
