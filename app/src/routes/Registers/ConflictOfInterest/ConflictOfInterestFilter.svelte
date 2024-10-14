<script>
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import RadioButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/RadioButtonGroup.svelte";
    import NewFloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";
    import Container from "@shared/Container.svelte";

    export let props = {};
  
    let staffs;
    let staffList = [];
    let statusList = [
        { option: "Resolved", value: "resolved" },
        { option: "Unresolved", value: "unresolved" }
    ];
    let typeList = [
        { option: "Organisational", value: "organisational" },
        { option: "Individual", value: "individual" },
    ];

    jspa("/Staff", "listStaff", {})
        .then((result) => {
          staffs = result.result;
          staffs.forEach((staffer) => {
                if (staffer.archived != 1)
                staffList.push({ option: staffer.staff_name, value: staffer.id });
            });
            staffList = staffList;
           
            
        })
      .catch(() => {});
  </script>

  
  
<div class="flex flex-col gap-4">
  <!-- Date Fields -->
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <FloatingDate label="Conflict Date" bind:value={props.date_identified} />
      <FloatingDate label="Resolution Date" bind:value={props.date_resolved} />
  </div>

  <!-- Staffer -->
  <div class="w-full">
      <NewFloatingSelect
          on:change
          label="Staffer"
          options={staffList}
          bind:value={props.staff_id}
          instruction="Select or type staff name ..."
          clearable
      />
  </div>

  <!-- Status and Type side by side -->
  <div class="flex space-x-4 w-full">
      <!-- Status -->
      <div class="w-1/2">
          <div class="text-xs opacity-50 mb-2">Status</div>
          <div class="flex space-x-4 w-full mt-2">
              <RadioButtonGroup options={statusList} bind:value={props.status} columns={"5"}></RadioButtonGroup>
          </div>
      </div>
      
      <!-- Type -->
      <div class="w-1/2">
          <div class="text-xs opacity-50 mb-2">Type</div>
          <div class="flex space-x-4 w-full mt-2">
              <RadioButtonGroup options={typeList} bind:value={props.type} columns={"5"}></RadioButtonGroup>
          </div>
      </div>
  </div>
</div>

  
  
