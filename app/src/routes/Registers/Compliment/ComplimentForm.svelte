<script>
    import RTE from "@shared/RTE/RTE.svelte";
    import RTEView from "@shared/RTE/RTE.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import Role from "@shared/Role.svelte";

    export let compliment;
    export let staffer = [];
    export let readOnly = false;


    console.log(readOnly)
</script>
<div class="mt-4 mb-2">
    <h3 class="text-base font-fredoka-one-regular leading-6 text-gray-900 px-3" style="color: rgb(34, 0, 85);">Compliment</h3>
</div>

<div class="flex space-x-4">
    <div class="flex-1">
        <FloatingInput 
            bind:value={compliment.complimenter}
            label="Name of complimenter"
            placeholder="eg: Eva Snow"
            {readOnly}
        />
    </div>

    <div class="flex-1">
        <FloatingDate
            bind:value={compliment.date}
            label="Date of compliment"
            {readOnly}
        />
    </div>
</div>

<span class="ml-2 text-xs text-gray-900/50">Compliment</span>

<RTE 
    bind:content={compliment.description}
/>


<Role roles={["admin"]}>
    <div class="mt-4 mb-2">
        <h3 class="text-base font-fredoka-one-regular leading-6 text-gray-900 px-3" style="color: rgb(34, 0, 85);">Action</h3>
    </div>

    <div class="mt-2">
        <FloatingCombo 
            bind:value={compliment.staffs_id}
            items={staffer}
            label="Acknowledging Staff"
            placeholderText="Select or type staff name"
        />
        <FloatingTextArea 
            bind:value={compliment.action_taken}
            label="Action Taken"
            placeholder="Indicate action taken by staff"
            {readOnly}
        /> 
    </div>
</Role>
