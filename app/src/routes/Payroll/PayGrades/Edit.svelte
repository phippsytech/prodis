<script>
    import Form from "./Form.svelte";
    import { BreadcrumbStore } from '@shared/stores.js';

    BreadcrumbStore.set({
        path: [
            {url: "/payroll", name: 'Pay Roll'},
            {url: "/payroll/paygrades", name: 'Pay Grades'},
            {url: null, name: 'Edit'},
        ]
    });

    export let params;

    let paygrade_id = params.paygrade_id;
      
</script>

<div class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2" style="color:#220055;">Edit Pay Grade</div>
<Form bind:paygrade_id={paygrade_id} />

