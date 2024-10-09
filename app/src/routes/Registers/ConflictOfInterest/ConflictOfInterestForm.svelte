<script>
    // import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import NewFloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import RTE from "@shared/RTE/RTE.svelte";
    import StaffMultiSelector from "@shared/StaffMultiSelector.svelte";
    
    export let conflictofinterest = {
        status: "Unresolved",
    };
    export let readOnly = false;

    let conflictofinterestStatusSelectElement = null;

    let conflictofinterestStatusOptions = [
        { option: "Resolved", value: "resolved" },
        { option: "Ongoing", value: "ongoing" },
        { option: "Unresolved", value: "unresolved" },git branch --contains staging
    ];

    let typeList = [
        { option: "Organisational", value: "organisational" },
        { option: "Individual", value: "individual" },
    ];

    let staff = [];
    let staffList = [];
    let staffSelectElement = null;

    jspa("/Staff", "listStaff", {})
        .then((result) => {
            staff = result.result;
            staff.forEach((staffer) => {
                if (staffer.archived != 1)
                    staffList.push({ option: staffer.staff_name, value: staffer.id });
            });
            staffList = staffList;
            console.log(staffList);
            
        })
        .catch(() => {});

    $: {
        readOnly = conflictofinterest.status == "closed";
    }
</script>
<div class="flex flex-wrap gap-2 items-center">
    <div class="flex gap-2 flex-none">
        <FloatingDate label="Conflict Date"  bind:value={conflictofinterest.conflict_date}/>
        <FloatingSelect
            bind:this={conflictofinterestStatusSelectElement}
            bind:value={conflictofinterest.status}
            label="Status"
            instruction="Set Status"
            options={conflictofinterestStatusOptions}
            hideValidation={true}
        />
        <NewFloatingSelect 
            on:change
            bind:value={conflictofinterest.type}
            label="Type"
            instruction="Select type"
            options={typeList}
            {readOnly}
            clearable>
            
            
        </NewFloatingSelect>

    </div>
</div>


<NewFloatingSelect 
    on:change
    bind:value={conflictofinterest.staff_id}
    label="Who reported this conflict of interest"
    instruction="Choose staffer"
    options={staffList}
    {readOnly}
    clearable>
</NewFloatingSelect>



<RTE bind:content={conflictofinterest.description} />
<FloatingTextArea
    bind:value={conflictofinterest.resolution}
    label="Actions Taken"
    placeholder="List actions taken to mitigate the conflict of interest."
    style="height:150px"
    {readOnly}
/>
