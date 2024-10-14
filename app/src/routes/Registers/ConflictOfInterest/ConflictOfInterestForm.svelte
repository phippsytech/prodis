<script>
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import { jspa } from "@shared/jspa.js";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import NewFloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import RTE from "@shared/RTE/RTE.svelte";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { push } from "svelte-spa-router";
    import Role from "@shared/Role.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import Container from "@shared/Container.svelte";

    export let conflictofinterest = {
        status: "Unresolved",
    };
    export let readOnly = false;

    let conflictofinterestStatusSelectElement = null;

    let conflictofinterestStatusOptions = [
        { option: "Resolved", value: "resolved" },
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
            //console.log(staffList);
            
        })
        .catch(() => {});

    $: {
        readOnly = conflictofinterest.status == "resolved";
    }

    $: if (!conflictofinterest.status) {
        conflictofinterest.status = "unresolved";
    }   

</script>

<Container>
    <div class="flex space-x-4 w-full mt-2">
        <div class="flex-1">
            <FloatingDate label="Conflict Date" bind:value={conflictofinterest.date_identified} />
        </div>
        <div class="flex-1">
            <NewFloatingSelect
                bind:value={conflictofinterest.status}
                label="Status"
                instruction="Set Status"
                options={conflictofinterestStatusOptions}
                hideValidation={true}
            />
        </div>
    </div>
</Container>
<Container>
    <div class="text-xs opacity-50 mb-2">Conflict Type</div> 
    <div class="flex space-x-4 w-full mt-2">
        <div class="flex-1">
        <RadioButtonGroup options={typeList} bind:value={conflictofinterest.type}></RadioButtonGroup>
    </div> 
</Container>
<h3 class="text-base font-fredoka-one-regular leading-6 text-gray-900 px-3" style="color: rgb(34, 0, 85);">Details</h3>
<Container> 
    <div class="w-full">
        <NewFloatingSelect 
            on:change
            bind:value={conflictofinterest.staff_id}
            label="Who reported this conflict of interest"
            instruction="Choose staffer"
            options={staffList}
           
            clearable
        />
    </div>
    
    <div class="w-full">
        <FloatingInput 
            bind:value={conflictofinterest.parties_involved} 
            label="Parties Involved" 
            placeholder="Name of the parties involved." 
           
        />
    </div>
    
    
    
    <div class="w-full">
        <RTE bind:content={conflictofinterest.description}  />
    </div>
</Container>

<h3 class="text-base font-fredoka-one-regular leading-6 text-gray-900 px-3" style="color: rgb(34, 0, 85);">Resolution</h3>
<div class="flex flex-wrap gap-2 w-full md:w-auto">
    <div class="w-full md:w-auto">
        <FloatingDate label="Resolution Date" bind:value={conflictofinterest.date_resolved} />
    </div>
</div>

<div class="w-full">
    <FloatingTextArea
        bind:value={conflictofinterest.resolution}
        label="Actions Taken"
        placeholder="List actions taken to mitigate the conflict of interest."
        style="height:150px"
    />
</div>
