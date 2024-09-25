<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import StaffSelector from "@shared/StaffSelector.svelte";

    let training = {};

    training.status = "open";

    let staff = [];
    let staffList = [];
    let staffSelectElement = null;

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });

    jspa("/Staff", "listStaff", {})
        .then((result) => {
            staff = result.result;
            staff.forEach((staffer) => {
                if (staffer.archived != 1)
                    staffList.push({ option: staffer.name, value: staffer.id });
            });
            staffList = staffList;
        })
        .catch(() => {});

    training.staff_ids = null;

    let trainingStatusSelectElement = null;

    let trainingStatusOptions = [
        { option: "Open", value: "open" },
        { option: "Closed", value: "closed" },
    ];

    function addTraining() {
        jspa("/Register/Training", "addTraining", training)
            .then((result) => {
                let training_id = result.result.id;
                push("/registers/trainings/" + training_id);
            })
            .catch(() => {});
    }
    // expected payload for staff
    // training.staff_ids = [1, 2, 3];
</script>



<!-- <StaffSelector 
    bind:staff_id={training.staff_id} 
    label="Assignees"
    clearable /> -->

<FloatingInput
    bind:value={training.type}
    label="Type of training"
    placeholder="Type of training"
/>
<FloatingTextArea
    bind:value={training.description}
    label="Description"
    placeholder="Describe the training"
    style="height:250px"
/>

<div class="flex justify-between">
    <span></span>
    <Button on:click={() => addTraining()} label="Add training" />
</div>
