<script>
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import CheckboxButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte";

    export let user = {};
</script>

<div class="mb-2"></div>
<FloatingInput
    bind:value={user.name}
    label="Display Name"
    placeholder="eg: Bella"
    autocomplete="off"
/>
<FloatingInput
    bind:value={user.first_name}
    label="First Name"
    placeholder="eg: Annie"
    autocomplete="off"
/>
<FloatingInput
    bind:value={user.last_name}
    label="Last Name"
    placeholder="eg: Walker"
    autocomplete="off"
/>
<FloatingInput
    bind:value={user.email}
    label="Email"
    placeholder="eg: annie.walker@cia.gov"
    autocomplete="off"
/>
<FloatingInput
    bind:value={user.phone}
    label="Phone"
    placeholder="eg: 04XX XXX XXX"
    autocomplete="off"
/>

<div class="bg-white px-3 pt-2 pb-4 mb-2 border border-indigo-100 rounded-md">
    <div class="text-xs opacity-50 mb-2">User Roles</div>
    <CheckboxButtonGroup
        options={[
            { value: "stakeholder", option: "stakeholder" },
            { value: "therapist", option: "therapist" },
            { value: "therapist.lead", option: "therapist.lead" },
            { value: "house", option: "house" },
            { value: "house.lead", option: "house.lead" },
            { value: "roster", option: "roster" },
            { value: "forms", option: "forms" },
            { value: "timeline", option: "timeline" },
            { value: "sil.admin", option: "sil.admin" },
            { value: "payroll", option: "payroll" },
            { value: "accounts", option: "accounts" },
            { value: "participant.modify", option: "participant.modify" },
            {
                value: "serviceagreement.modify",
                option: "serviceagreement.modify",
            },
            { value: "admin", option: "admin" },
            { value: "super", option: "super" },
            { value: "sysadmin", option: "sysadmin" },
        ]}
        bind:values={user.roles}
    />
</div>


