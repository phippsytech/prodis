<script>
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import { jspa } from "@shared/jspa.js";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import NewFloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import RTE from "@shared/RTE/RTE.svelte";
    
    
    export let conflictofinterest = {
        status: "unresolved",
    };
    export let readOnly = false;

    let conflictofinterestStatusSelectElement = null;

    let conflictofinterestStatusOptions = [
        { option: "Resolved", value: "resolved" },
        { option: "Ongoing", value: "ongoing" },
        { option: "Unresolved", value: "unresolved" }
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
        readOnly = conflictofinterest.status == "resolved";
    }
</script>
<div class="flex flex-wrap gap-2 items-center">
    <div class="flex flex-wrap gap-2 w-full md:w-auto">
        <div class="w-full md:w-auto">
            <FloatingDate label="Conflict Date" bind:value={conflictofinterest.conflict_date} />
        </div>
        <div class="w-full md:w-auto">
            <NewFloatingSelect
                bind:value={conflictofinterest.status}
                label="Status"
                instruction="Set Status"
                options={conflictofinterestStatusOptions}
                hideValidation={true}
            />
        </div>
        <div class="w-full md:w-auto">
            <NewFloatingSelect 
                on:change
                bind:value={conflictofinterest.type}
                label="Type"
                instruction="Select type"
                options={typeList}
                {readOnly}
                clearable
            />
        </div>
    </div>
</div>

<div class="w-full">
    <NewFloatingSelect 
        on:change
        bind:value={conflictofinterest.staff_id}
        label="Who reported this conflict of interest"
        instruction="Choose staffer"
        options={staffList}
        {readOnly}
        clearable
    />
</div>

<div class="w-full">
    <FloatingInput 
        bind:value={conflictofinterest.parties_involved} 
        label="Parties Involved" 
        placeholder="Name of the parties involved." 
        {readOnly}
    />
</div>

<h3 class="text-slate-800 font-bold mx-2">Details</h3>

<div class="w-full">
    <RTE bind:content={conflictofinterest.description} />
</div>

<div class="w-full mt-2">
    <FloatingTextArea
        bind:value={conflictofinterest.resolution}
        label="Actions Taken"
        placeholder="List actions taken to mitigate the conflict of interest."
        style="height:150px"
        {readOnly}
    />
</div>
