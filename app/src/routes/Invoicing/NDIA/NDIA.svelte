<script>

    import {push} from 'svelte-spa-router'

    import ReportPanel from '@app/routes/Reports/ReportPanel.svelte'
    import { BreadcrumbStore } from '@shared/stores.js';

    BreadcrumbStore.set({
        path: [
            {url: '/invoicing', name: 'Invoicing'},
            {url: null, name: 'NDIA'},
        ]
    });

</script>

        <ReportPanel on:click={()=>push('/invoicing/ndia/unbilled')} label="NDIA Invoicing">
            Prepares data to be submitted to NDIA as a bulk csv upload.
        </ReportPanel>

        <ReportPanel on:click={()=>push('/invoicing/ndia/remittance/upload')} label="Upload NDIA Payee Payment Remittance Advice">
            Upload and process NDIA Payee Payment Remittance Advice and generate invoices and payment batches for easy 
            bank reconcilliation in Xero.
        </ReportPanel>
