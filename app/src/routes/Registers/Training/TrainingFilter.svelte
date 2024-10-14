<script>
  import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
  import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";

  export let props = {};

  let staffs;
</script>

<div class="flex justify-between items-center">
  <div class="flex flex-wrap gap-2 items-center">
    <div class="flex gap-2 flex-none">
      <FloatingDate label="Start Date" bind:value={props.start_date} />
      <FloatingDate label="End Date" bind:value={props.end_date} />
    </div>
    <div class="sm:flex-none w-full sm:w-auto min-w-[200px]">
      <FloatingCombo
        label="Staffer"
        items={staffs}
        bind:value={props.staff_id}
        placeholderText="Select or type staff name ..."
      />
    </div>
  </div>
</div>
