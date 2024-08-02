<script>
  import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
  import { LockClosedIcon } from "heroicons-svelte/24/outline";

  import Toggle from "@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte";
  import { jspa } from "@shared/jspa.js";

  import { formatDate } from "@shared/utilities.js";

  export let props;
  // ={
  //     shift_type:"individual"
  // };

  let client_id = props.client_id;
  let staff = [];
  let staffList = [];

  let billable = !props.do_not_bill; // this needs to change to bill_time
  // let bill_kms=props.bill_kms;

  $: {
    props.do_not_bill = !billable;
    // props.bill_kms = bill_kms;

    if (props.staff_id == "Choose staffer") props.staff_id = null;
  }

  // $: {
  //     if(props.vehicle_type==null) props.vehicle_type="private"
  // }

  function getHouseStaff() {
    jspa("/Client/Staff", "listClientStaffByClientId", {
      client_id: client_id,
    }).then((result) => {
      staffList = [];
      staff = result.result;
      staff.push({ staff_id: null, staff_name: "[Unassigned]", id: null });
      staff = staff.sort((a, b) => a.staff_name.localeCompare(b.staff_name));
      staff.forEach((staffer) => {
        let options = {
          option: staffer.staff_name,
          value: staffer.staff_id,
          selected: false,
        };
        if (staffer.staff_id && props && staffer.staff_id == props.staff_id) {
          options.selected = true;
        }
        if (staffer.archived != 1) {
          staffList.push(options);
        }
      });
      staffList = staffList;
    });
  }

  getHouseStaff();
</script>

{#if props.editable}
  Shift is locked

  <div class="rounded-md bg-blue-50 p-4">
    <div class="flex">
      <div class="flex-shrink-0">
        <LockClosedIcon class="h-5 w-5 text-blue-400" />
        <!-- <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
        </svg> -->
      </div>
      <div class="ml-3 flex-1 md:flex md:justify-between">
        <p class="text-sm text-blue-700">Shift is locked</p>
        <p class="mt-3 text-sm md:ml-6 md:mt-0">
          <a
            href="/#/"
            class="whitespace-nowrap font-medium text-blue-700 hover:text-blue-600"
          >
            Details
            <span aria-hidden="true"> &rarr;</span>
          </a>
        </p>
      </div>
    </div>
  </div>
{/if}

<h3 class="text-base leading-6 text-gray-900" id="modal-title">
  {formatDate(props.start_date, { day: "numeric", month: "short", year: null })}
  {props.start_time} - {#if props.start_date != props.end_date}{formatDate(
      props.end_date,
      { day: "numeric", month: "short", year: null },
    )}{/if}
  {props.end_time}
</h3>

<FloatingSelect
  bind:value={props.staff_id}
  label="Staffer"
  instruction="Choose staffer"
  options={staffList}
  hideValidation={true}
/>
<!-- <FloatingInput label="kms (optional)" bind:value={props.kms} placeholder="2"/> -->

<!-- {#if props.kms>0}
<div class="bg-white px-3 pt-2 pb-4 mb-2 border border-gray-300 rounded-md">
    <div class="text-xs opacity-50 mb-2">Vehicle Type</div>
<RadioButtonGroup options={[
    {value: 'company', option: 'Company'},
    {value: 'private', option: 'Private'},
    

]} bind:value={props.vehicle_type} />
</div>
{/if} -->

<div class="flex gap-2">
  <Toggle
    bind:value={billable}
    label_on="Bill time"
    label_off="Do not bill time"
  />
  <!-- {#if props.kms>0}
        <Toggle bind:value={bill_kms} label_on="Bill kms" label_off="Do not bill kms" />
        {/if} -->
  <Toggle bind:value={props.passive} label_on="Passive" label_off="Passive" />
</div>
