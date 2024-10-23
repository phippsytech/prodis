<script>
    import { BreadcrumbStore, RolesStore } from '@shared/stores.js';
    import { push } from "svelte-spa-router";
    import RegisterPanel from './RegisterPanel.svelte';

    BreadcrumbStore.set({
        path: [
            {url: null, name: 'Registers'},
        ]
    });

</script>
<div class="mt-2 mb-2">
    <h1 class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
        Registers
    </h1>
</div>

<RegisterPanel
    on:click={() => {
        push("/registers/complaints");
    }}
    label="Complaint Register"
>
</RegisterPanel>

<RegisterPanel
    on:click={() => {
        push("/registers/compliments");
    }}
    label="Compliment Register"
>
</RegisterPanel>

<RegisterPanel
    on:click={() => {
        push("/registers/conflictofinterests");
    }}
    label="Conflict of Interest Register"
>
</RegisterPanel>

<RegisterPanel
    on:click={() => {
        push("/registers/continuousimprovements");
    }}
    label="Continuous Improvement Register"
>
</RegisterPanel>

<RegisterPanel
    on:click={() => {
        push("/registers/trainings");
    }}
    label="Training Register"
>
</RegisterPanel>

<RegisterPanel
    on:click={() => {
        push("/registers/risks");
    }}
    label="Risk Register"
>
</RegisterPanel>

