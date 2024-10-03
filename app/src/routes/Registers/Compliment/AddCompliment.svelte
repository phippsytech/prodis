<script>
    import RTE from "@shared/RTE/RTE.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import NewFloatingSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte';
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { push } from "svelte-spa-router";
    import { jspa } from "@shared/jspa.js";

    let compliment - {};
    let staffer = [];

    let acknowledgementStatusOptions = [
            { option: "Not Acknowledged", value: "not_acknowledged" },
            { option: "Acknowledged", value: "acknowledged" },
    ];

    // add here the request from staff api bind it in the compliment.staff_id floatingcombo
    jspa("/Staff", "listStaff", {}).then((result) => {
        staffer = result.result
            .filter((item) => item.archived != 1)
            .map((item) => ({
            label: `${item.staff_name}`,
            value: item.id,
            }))
            .sort((a, b) => a.label.localeCompare(b.label));
    });

    // create a function to save compliment using an endpoint ('/Register/Compliment') use addCompliment
    function addCompliment() {
        jspa("/Register/Compliment", "addCompliment", compliment)
            .then((result) => {
                let compliment_id = result.result.id;

                push("/registers/compliments/" + compliment_id);
            })
            .catch(() => {});
    }
</script>

<!-- date of compliment -->
<FloatingDate
	bind:value={compliment.date} 
/>

<!-- name of person giving the compliment -->
<FloatingInput 
	bind:value={compliment.complimenter}
/>

<!-- description -->
<RTE 
	bind:value={compliment.description}
/>

<!-- staff being complimented  -->
<FloatingCombo 
	bind:value={compliment.staff_id}
    options={staffer}
    label="Select Staff"
    placeholder="Select Staff"
/>

<!-- action taken  -->
<FloatingTextArea 
	bind:value={compliment.action_taken}
    label="Action Taken"
    placeholder=" Indicate action taken by staff"

/>

<!-- status  -->
<NewFloatingSelect 
	bind:value={compliment.status}
	options={acknowledgementStatusOptions}
    label="Status"
   	placeholder="Select Status"
/>

<!-- date of acknowledgement , 
 check if this is necessary 
 once we set the status to acknowledged it should put the current date
-->
<!-- <FloatingDate /> -->




