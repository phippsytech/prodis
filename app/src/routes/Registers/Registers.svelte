<script>

    import Container from '@shared/Container.svelte';
    import IconMenu from '@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte';
    
    import {AcademicCapIcon, ChatBubbleLeftEllipsisIcon, FireIcon, ExclamationCircleIcon, BookOpenIcon} from 'heroicons-svelte/24/outline'
    
    import { BreadcrumbStore, RolesStore } from '@shared/stores.js';


    let icons = [
        {url:"/registers/feedbacks", name:"Feedback Register", icon:  ChatBubbleLeftEllipsisIcon, role:["admin"]},
        {url:"/registers/risks", name:"Risk Register", icon:  FireIcon, role:["admin"]},
        {url:"/registers/conflictofinterests", name:"Conflict Of Interest Register", icon:  ExclamationCircleIcon, role:["admin"]},
        {url:"/registers/trainings", name:"Training Register", icon:  AcademicCapIcon, role:["admin"]},
        {url:"/registers/compliments", name:"Compliment Register", icon:  BookOpenIcon, role:["admin"]},
    ]
    
    BreadcrumbStore.set({
        path: [
            {url: null, name: 'Registers'},
        ]
    });

</script>


<Container>
    <IconMenu icons={icons} userRoles={$RolesStore} />
</Container>

