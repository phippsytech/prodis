<script>
  import { onMount } from "svelte";
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";

  import { UserStore, StafferStore, BreadcrumbStore } from "@shared/stores.js";

  import { jspa } from "@shared/jspa.js";

  import Container from "@shared/Container.svelte";

  $: userStore = $UserStore;
  $: stafferStore = $StafferStore;

  let staffer = {};
  let employee = {};

  let loaded = false;

  onMount(async () => {
    stafferStore.id = 57;

    if (stafferStore.id) {
      jspa("/Staff", "getStaffer", { id: stafferStore.id })
        .then((result) => {
          staffer = result.result;
          BreadcrumbStore.set({ path: [{ name: staffer.name }] });
          employee = jspa("/Payroll/Payrun", "getEmployeeByStaffId", {
            staff_id: stafferStore.id,
          });
        })
        .finally(() => {
          loaded = true;
        });
    }
  });
</script>

<div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  {$UserStore.name}
</div>

<Container>
  {#await employee}
    <p>Loading</p>
  {:then result}
    <div class="text-xs opacity-50 mb-2">Bank Account</div>
    {#if result.result && result.result.BankAccounts && result.result.BankAccounts[0]}
      <FloatingInput
        label="Account Name"
        value={result.result.BankAccounts[0].AccountName}
      />
      <div class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2">
        <div class="flex-grow">
          <FloatingInput
            label="BSB"
            type="number"
            value={result.result.BankAccounts[0].BSB}
          />
        </div>
        <div class="flex-grow">
          <FloatingInput
            label="Account Number"
            type="number"
            value={result.result.BankAccounts[0].AccountNumber}
          />
        </div>
      </div>
    {/if}
  {:catch error}
    <p style="color:red;">{error.error}</p>
  {/await}
</Container>
