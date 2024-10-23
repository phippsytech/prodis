<script>

    import Container from '@shared/Container.svelte';
    import IconMenu from '@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte';
    import {AcademicCapIcon, ChatBubbleLeftEllipsisIcon, FireIcon, ExclamationCircleIcon, PlusIcon, BookOpenIcon} from 'heroicons-svelte/24/outline';
    import { BreadcrumbStore, RolesStore } from '@shared/stores.js';
    import { push } from "svelte-spa-router";
    import RegisterPanel from './RegisterPanel.svelte';


    let icons = [
        // {url:"/registers/feedbacks", name:"Feedback Register", icon:  ChatBubbleLeftEllipsisIcon, role:["admin"]},
        {url:"/registers/complaints", name:"Complaint Register", icon: FireIcon, role:["admin"]},
        {url:"/registers/compliments", name:"Compliment Register", icon: BookOpenIcon, role:["admin"]},
        {url:"/registers/conflictofinterests", name:"Conflict Of Interest Register", icon: ExclamationCircleIcon, role:["admin"]},
        {url:"/registers/continuousimprovements", name:"Continuous Improvement", icon: PlusIcon, role:["admin"]},
        {url:"/registers/trainings", name:"Training Register", icon: AcademicCapIcon, role:["admin"]},
        {url:"/registers/risks", name:"Risk Register", icon:  FireIcon, role:["admin"]},
    ]
    
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

