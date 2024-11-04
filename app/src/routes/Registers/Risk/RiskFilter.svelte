<script>
    import { jspa } from "@shared/jspa.js";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";

    export let props = {};

    let staffer;

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
        <FloatingDate label="Date Identified" bind:value={props.date_identified} />
        <FloatingDate label="Date Resolved" bind:value={props.date_resolved} />
      </div>
      <div class="sm:flex-none w-full sm:w-auto min-w-[200px]">
        <FloatingCombo
          label="Reporter"
          items={staffer}
          bind:value={props.reporter}
          placeholderText="Select or type staff name ..."
        />
      </div>
    </div>
  </div>