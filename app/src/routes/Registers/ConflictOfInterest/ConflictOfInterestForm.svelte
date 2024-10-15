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

<h3 class="text-base font-bold leading-6 text-gray-900 px-3 mt-4 mb-2">Details</h3>
<Container>
    <div class="flex space-x-4 w-full mt-2">
        <div class="flex-1">
            <FloatingDate label="Conflict Date" bind:value={conflictofinterest.date_identified} />
        </div>
        <div class="flex-1">
            <NewFloatingSelect
                bind:value={conflictofinterest.type}
                label="Type"
                instruction="Set conflict type"
                options={typeList}
                hideValidation={true}
            />
        </div>
    </div>
</Container>

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

<h3 class="text-base font-bold leading-6 text-gray-900 px-3 mt-4 mb-2">Resolution</h3>

<div class="w-full">
    <RTE bind:content={conflictofinterest.resolution }  />
</div>
