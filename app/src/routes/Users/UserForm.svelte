<script>
    import Container from "@shared/Container.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import CheckboxButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte";
    import ParticipantAccessControl from "./ParticipantAccessControl.svelte";

    export let user = {};
</script>

<div class="mb-2"></div>
<Container>
    <div
        class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular"
    >
        {user.display_name}
    </div>
    <div>
        {user.first_name}
        {user.last_name} - {user.email} - {user.phone}
    </div>
</Container>
<div class="bg-white px-3 pt-2 pb-4 mb-2 border border-indigo-100 rounded-md">
    <div class="text-xs opacity-50 mb-2">User Roles</div>
    <CheckboxButtonGroup
        options={[
            { value: "stakeholder", option: "stakeholder" },
            { value: "therapist", option: "therapist" },
            { value: "therapist.lead", option: "therapist.lead" },
            { value: "house", option: "house" },
            { value: "house.lead", option: "house.lead" },
            { value: "trip", option: "trip" },
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

<ParticipantAccessControl bind:user />
