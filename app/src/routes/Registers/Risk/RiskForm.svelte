<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import FloatingDateSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte";
    import { jspa } from "@shared/jspa.js";

    export let risk = {
        status: "open",
    };

    let riskStatusSelectElement = null;

    let riskStatusOptions = [
        { option: "Open", value: "open" },
        { option: "Closed", value: "closed" },
    ];

    let staff = [];
    let staffList = [];
    let staffSelectElement = null;

    export let readOnly = false;

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
        readOnly = risk.status == "closed";
    }
</script>

<FloatingSelect
    bind:this={riskStatusSelectElement}
    bind:value={risk.status}
    label="Status"
    instruction="Set Status"
    options={riskStatusOptions}
    hideValidation={true}
/>

<FloatingSelect
    bind:this={staffSelectElement}
    bind:value={risk.staff_id}
    label="Who reported this risk"
    instruction="Choose staffer"
    options={staffList}
    hideValidation={true}
    {readOnly}
/>

<FloatingInput
    bind:value={risk.type}
    label="Type of Risk"
    placeholder="Type of risk"
    {readOnly}
/>
<FloatingTextArea
    bind:value={risk.description}
    label="Description"
    placeholder="Describe the risk"
    style="height:150px"
    {readOnly}
/>
<FloatingTextArea
    bind:value={risk.resolution}
    label="Resolution"
    placeholder="List actions taken to mitigate the risk."
    style="height:150px"
    {readOnly}
/>
