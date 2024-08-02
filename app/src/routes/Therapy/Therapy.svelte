<script>

    import {push} from 'svelte-spa-router'
    import ReportPanel from '@app/routes/Reports/ReportPanel.svelte'
    import { BreadcrumbStore } from '@shared/stores.js';
    
    BreadcrumbStore.set({
        path: [
            {url: null, name: 'Therapy'},
        ]
    });
    
</script>

    <ReportPanel on:click={()=>push('/therapy/kpi')} label="KPI Report">
        A summary of staff KPI for a date period
    </ReportPanel>
