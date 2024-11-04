<script>
  import { onMount } from "svelte";
  import { push } from "svelte-spa-router";
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
  import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
  import FloatingDateSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte";
  import { jspa } from "@shared/jspa.js";

  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
  import { BreadcrumbStore } from "@shared/stores.js";

  export let params;

  let training = {
    status: "open",
  };
  let stored_training = Object.assign({}, training);

  let trainingStatusSelectElement = null;

  let trainingStatusOptions = [
    { option: "Open", value: "open" },
    { option: "Closed", value: "closed" },
  ];

  let staff = [];
  let staffList = [];
  let staffSelectElement = null;

  let readOnly = false;

  BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });

  jspa("/Staff", "listStaff", {})
    .then((result) => {
      staff = result.result;
      staff.forEach((staffer) => {
        if (staffer.archived != 1)
          staffList.push({ option: staffer.name, value: staffer.id });
      });
      staffList = staffList;
    })
    .catch(() => {});

  let mounted = false;
  let show = false;

  onMount(() => {
    if (params.id != "add") {
      jspa("/Register/Training", "getTraining", { id: params.id })
        .then((result) => {
          training = result.result;
        })
        .catch(() => {})
        .finally(() => {
          // Make a copy of the object
          stored_training = Object.assign({}, training);
        });
    }
    mounted = true;
  });

  function undo() {
    training = Object.assign({}, stored_training);
  }

  function save() {
    jspa("/Register/Training", "updateTraining", training)
      .then((result) => {
        training = result.result;
        stored_training = Object.assign({}, training);
        // let result = result.result.id;
      })
      .catch(() => {});
  }

  $: {
    if (mounted) {
      ActionBarStore.set({
        can_delete: false,
        show: !(JSON.stringify(training) === JSON.stringify(stored_training)),
        undo: () => undo(),
        save: () => save(),
      });
    }
  }

  $: {
    readOnly = training.status == "closed";
  }
</script>

<div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
  Add training
</div>
<FloatingSelect
  bind:this={trainingStatusSelectElement}
  bind:value={training.status}
  bind:option={training.status}
  label="Status"
  instruction="Set Status"
  options={trainingStatusOptions}
  hideValidation={true}
/>

<!-- {#if training.status == "open"} -->
<FloatingSelect
  bind:this={staffSelectElement}
  bind:value={training.staff_id}
  bind:option={training.staff_id}
  label="Who reported this training"
  instruction="Choose staffer"
  options={staffList}
  hideValidation={true}
  {readOnly}
/>

<!-- <FloatingDateSelect bind:value={training.session_date} label="Date"  /> -->

<FloatingInput
  bind:value={training.type}
  label="Type of training"
  placeholder="Type of training"
  {readOnly}
/>
<FloatingTextArea
  bind:value={training.description}
  label="Description"
  placeholder="Describe the training"
  style="height:150px"
  {readOnly}
/>
<FloatingTextArea
  bind:value={training.resolution}
  label="Resolution"
  placeholder="List actions taken to mitigate the training."
  style="height:150px"
  {readOnly}
/>
