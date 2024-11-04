<script>
    import { jspa } from "@shared/jspa.js";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import NewFloatingSelect from '@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte';

    export let props = {};

    let staffer;

    let statusOptions = [
        {option: "Acknowledged", value: "acknowledged"},
        {option: "Not Acknowledged", value: "not_acknowledged"},
    ];

    jspa("/Staff", "listStaff", {}).then((result) => {
        staffer = result.result
            .filter((item) => item.archived !== "1")
            .map((item) => ({
                label: `${item.staff_name}`, 
                value: item.id,
            }))
            .sort((a, b) => a.label.localeCompare(b.label));
    });
</script>
<div class="flex justify-between items-center">
    <div class="flex flex-wrap gap-2 items-center">
      <div class="flex gap-2 flex-none">
        <FloatingDate label="Compliment Date" bind:value={props.date} />
        <FloatingDate label="Acknowledgement Date" bind:value={props.acknowledgement_date} />
      </div>
      <div class="flex flex-wrap">
        <NewFloatingSelect
            bind:value={props.status}
            label="Type"
            instruction="Select status"
            options={statusOptions}
            clearable
        />
      </div>
      <div class="sm:flex-none w-full sm:w-auto min-w-[200px]">
        <FloatingCombo
          label="Staffer"
          items={staffer}
          bind:value={props.staff}
          placeholderText="Select or type staff name ..."
        />
      </div>
    </div>
  </div>