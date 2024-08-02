<script>

    import Container from '@shared/Container.svelte';
    import IconMenu from '@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte';
    import {UserGroupIcon} from 'heroicons-svelte/24/outline'
    import { BreadcrumbStore, RolesStore } from '@shared/stores.js';

    let icons = [
        {url:`/accounts/billables`, name:"Billables", icon: UserGroupIcon, role:["admin"]},
        {url:`/accounts/invoiced`, name:"Invoiced", icon: UserGroupIcon, role:["admin"]},
        {url:`/accounts/planmanagers`, name:"Plan Managers", icon: UserGroupIcon, role:["admin"]},
    ]
   
    BreadcrumbStore.set({
        path: [
            {url: null, name: "Accounts"},
        ]
    });

</script>
<Container>
    <IconMenu icons={icons} userRoles={$RolesStore} />
</Container>
