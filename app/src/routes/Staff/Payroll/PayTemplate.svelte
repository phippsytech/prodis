<script>
  import { onMount } from "svelte";
  import Role from "@shared/Role.svelte";
  import Container from "@shared/Container.svelte";
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";

  import { BreadcrumbStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";

  import Earnings from "./PayTemplate/Earnings.svelte";
  // import Deductions from "./PayTemplate/Deductions.svelte";
  import Tax from "./PayTemplate/Tax.svelte";
  // import Superannuation from "./PayTemplate/Superannuation.svelte";
  // import Reimbursements from "./PayTemplate/Reimbursements.svelte";

  // $: staffer = $StafferStore;
  // You need to define the component prop "params"
  export let params = {};

  let staff_id = params.staff_id;
  let staffer = {};

  onMount(async () => {
    staffer = await jspa("/Staff", "getStaffer", { id: staff_id }); //getStaffer(staff_id)
    staffer = staffer.result;

    BreadcrumbStore.set({
      path: [
        { url: "/staff", name: "Staff" },
        { url: null, name: staffer.name },
      ],
    });
  });
</script>

<Role roles={["payroll"]} conditional={true}>
  <div slot="authorised">
    <div
      class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
    >
      Pay Template for {staffer.name}
    </div>
    <p>
      Add extra pay items that should be included on every payslip for {staffer.name}.
    </p>

    <Earnings {staff_id} />

    <!-- <Deductions /> -->

    <Tax bind:staff_id />

    <!-- <Superannuation /> -->

    <!-- <Reimbursements /> -->
  </div>

  <div slot="declined">
    <div class="text-sm text-gray-500">
      You are not authorised to view this page.
    </div>
  </div>
</Role>
