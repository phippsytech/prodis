<script>
    
    import Container from '@shared/Container.svelte';
    import NotificationsList from './NotificationsList.svelte'
    import { BreadcrumbStore } from '@shared/stores.js';

    BreadcrumbStore.set({path:[
        {url:null, name:'Notifications'},    
    ]})

</script>

<Container>
    <NotificationsList />
</Container>