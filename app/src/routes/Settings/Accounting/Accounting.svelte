<script>
    import Container from '@shared/Container.svelte';
    
    import Xero from './Xero.svelte';

    import { BreadcrumbStore } from '@shared/stores.js';
    
    BreadcrumbStore.set({
        path: [
            {url: "/settings", name: 'Settings'},
            {url: null, name: 'Accounting'}
        ]
    });
    
    
</script>


<!-- Choose your accounting provider

Xero
QuickBooks
MYOB -->



    <Xero />

    

