<script>
    import Container from "@shared/Container.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";

    let xeroConnected = false;

    // let breadcrumbs_path = [];

    let tenants = [];
    let tenantList = [];
    let accounting_tenant_id = null;
    let payroll_tenant_id = null;

    jspa("/Xero", "getAccountingTenantId", {})
        .then((result) => {
            accounting_tenant_id = result.result;
        })
        .catch((error) => {});

    jspa("/Xero", "getPayrollTenantId", {})
        .then((result) => {
            payroll_tenant_id = result.result;
        })
        .catch((error) => {});

    jspa("/Xero", "checkConnected", {}).then((result) => {
        xeroConnected = result.result;
    });

    jspa("/Xero", "listTenants", {})
        .then((result) => {
            tenantList = []; // clear the tenantList
            tenants = result.result;
            tenants.forEach((tenant) => {
                let options = {
                    option: tenant.name,
                    value: tenant.id,
                };
                // if (tenant.id == tenant_id) options.selected = true;
                tenantList.push(options);
            });
            tenantList = tenantList;
        })
        .catch((error) => {});

    function setXeroAccountingTenantId(xero_accounting_tenant_id) {
        jspa("/Xero", "setAccountingTenantId", {
            accounting_tenant_id: xero_accounting_tenant_id,
        })
            .then((result) => {})
            .catch((error) => {});
    }

    function setXeroPayrollTenantId(xero_payroll_tenant_id) {
        jspa("/Xero", "setPayrollTenantId", {
            payroll_tenant_id: xero_payroll_tenant_id,
        })
            .then((result) => {})
            .catch((error) => {});
    }

    function connectXero() {
        jspa("/Xero", "connect", {}).then((result) => {
            document.location = result.result.authorizationUrl;
        });
    }

    function disconnectXero() {
        jspa("/Xero", "disconnect", {}).then((result) => {
            xeroConnected = false;
        });
    }
</script>

<h2 class=" text-1xl font-semibold mt-0 mb-2">Xero</h2>

<div class="my-4">
    <Button on:click={() => connectXero()} label="Connect to Xero" />
</div>

{#if !xeroConnected}
    <div class="my-4">
        <Button on:click={() => connectXero()} label="Connect to Xero" />
    </div>
{:else}
    <p>
        You may use different accounting systems for your invoicing and payroll
    </p>

    <Container>
        <h2 class="font-medium mt-0 mb-2">Accounting</h2>

        <FloatingSelect
            bind:value={accounting_tenant_id}
            label="Xero Accounting File"
            instruction="Choose Accounting File"
            options={tenantList}
            hideValidation={true}
            on:change={(e) => {
                setXeroAccountingTenantId(e.target.value);
            }}
        />
    </Container>

    <Container>
        <h2 class="font-medium mt-0 mb-2">Payroll</h2>
        <FloatingSelect
            bind:value={payroll_tenant_id}
            label="Xero Payroll File"
            instruction="Choose Payroll File"
            options={tenantList}
            hideValidation={true}
            on:change={(e) => {
                setXeroPayrollTenantId(e.target.value);
            }}
        />
    </Container>

    <div class="my-4">
        <button
            on:click={() => disconnectXero()}
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
        >
            Disconnect Xero
        </button>
    </div>
{/if}
