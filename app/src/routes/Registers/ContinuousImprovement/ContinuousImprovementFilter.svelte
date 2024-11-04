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
        <FloatingDate label="Suggestion Date" bind:value={props.date_of_suggestion} />
        <FloatingDate label="Implementation Date" bind:value={props.implementation_date} />
      </div>
      <div class="sm:flex-none w-full sm:w-auto min-w-[200px]">
        <FloatingCombo
          label="Staffer"
          items={staffer}
          bind:value={props.staff}
          placeholderText="Select or type staff name ..."
        />
      </div>
      <div class="sm:flex-none w-full sm:w-auto min-w-[200px]">
        <FloatingCombo
          label="Reviewer"
          items={staffer}
          bind:value={props.reviewer}
          placeholderText="Select or type staff name ..."
        />
      </div>
    </div>
  </div>