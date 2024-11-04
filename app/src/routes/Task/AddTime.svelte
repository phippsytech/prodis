<script>
  import Container from "@shared/Container.svelte";
  import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import ClientSelector from "@app/routes/Billables/ClientSelector.svelte";
  import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
  import ComboBox from "@shared/PhippsyTech/svelte-ui/forms/ComboBox.svelte";
  import Toggle from "@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte";

  import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";

  let history;

  let tasktime = {
    client_id: null,
    staff_id: $StafferStore.id,
    date: new Date().toISOString().split("T")[0],
    duration: null,
  };

  let tasktimes = [];

  console.log(tasktime);

  BreadcrumbStore.set({
    path: [{ url: null, name: "Time Logger" }],
  });

  function add(tasktime) {
    // console.log(tasktime)
    if (tasktime.client_id == "Choose client" || tasktime.client_id == null) {
      toastError("Please select a client");
      return;
    }

    if (!tasktime.kms || isNaN(tasktime.kms)) {
      toastError("Please enter a valid number of KMs");
      return;
    }
    jspa("/Trip", "addTrip", tasktime)
      .then((result) => {
        tasktime = result.result;
        console.log(result);

        toastSuccess("Trip added");
        history.listTrips();
      })
      .catch((error) => {
        let error_message = error.error_message;

        toastError(error_message);
      });
  }

  tasktimes = [{}];

  let comboOptions = [
    { id: 1, value: "#1 Special Task" },
    { id: 2, value: "#129389 Another great Task" },
  ];

  let comboValue = "";
  let comboId = null;

  let assign_to_participant = false;
</script>

<Container>
  <div class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    Time Logger
  </div>
  <!-- 
    <ComboBox label="Activity / Task / Ticket" options={comboOptions} bind:value={comboValue} bind:ticket_id={comboId}/>

    {#if comboId}
        {comboValue}
    {:else}
        {comboId}
    {/if} -->

  <FloatingInput label="Activity" type="text" bind:value={tasktime.activity} />

  <div class="flex flex-row justify-between gap-2">
    <div class="flex-grow">
      <FloatingInput
        label="Duration (in minutes)"
        type="number"
        bind:value={tasktime.duration}
      />
    </div>
    <div class="flex-grow">
      <FloatingDate bind:value={tasktime.date} label="Date" />
    </div>
  </div>

  <Toggle
    bind:value={assign_to_participant}
    label_on="Allocate to a participant?"
    label_off="Allocate to a participant?"
  />
  {#if assign_to_participant}
    Note: This does not bill the participant. It just records that admin time
    was spent on the participant. This information helps us to better understand
    the time spent on each participant.
    <ClientSelector bind:value={tasktime.client_id} label="Participant" />
  {/if}

  <div class="flex justify-end">
    <button
      on:click={() => add(tasktime)}
      type="button"
      class="rounded-md bg-indigo-600 px-5 py-4 text-sm font-semibold text-white hover:bg-indigo-500 mb-2"
      >Add Time</button
    >
  </div>
</Container>
