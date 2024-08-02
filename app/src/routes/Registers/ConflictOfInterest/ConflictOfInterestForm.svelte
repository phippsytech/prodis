<script>
    // import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";

    export let conflictofinterest = {
        status: "open",
    };
    export let readOnly = false;

    let conflictofinterestStatusSelectElement = null;

    let conflictofinterestStatusOptions = [
        { option: "Open", value: "open" },
        { option: "Closed", value: "closed" },
    ];

    let staff = [];
    let staffList = [];
    let staffSelectElement = null;

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

    $: {
        readOnly = conflictofinterest.status == "closed";
    }
</script>

<FloatingSelect
    bind:this={conflictofinterestStatusSelectElement}
    bind:value={conflictofinterest.status}
    label="Status"
    instruction="Set Status"
    options={conflictofinterestStatusOptions}
    hideValidation={true}
/>

<FloatingSelect
    bind:this={staffSelectElement}
    bind:value={conflictofinterest.staff_id}
    label="Who reported this conflict of interest"
    instruction="Choose staffer"
    options={staffList}
    hideValidation={true}
    {readOnly}
/>

<FloatingTextArea
    bind:value={conflictofinterest.description}
    label="Description"
    placeholder="Describe the conflict of interest"
    style="height:150px"
    {readOnly}
/>
<FloatingTextArea
    bind:value={conflictofinterest.resolution}
    label="Resolution"
    placeholder="List actions taken to mitigate the conflict of interest."
    style="height:150px"
    {readOnly}
/>
