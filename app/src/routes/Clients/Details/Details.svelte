<script>
  import { onMount } from "svelte";
  import { jspa } from "@shared/jspa.js";
  import { getClient } from "@shared/api.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
  import { toastError, toastSuccess } from "@shared/toastHelper.js";
  import { RolesStore } from "@shared/stores.js";
  import { formatDate, haveCommonElements } from "@shared/utilities.js";
  import ClientReports from "@app/routes/Clients/Reports/ClientReports.svelte";
  import Container from "@shared/Container.svelte";
  import ClientServiceAgreements from "@app/routes/Clients/ServiceAgreements/ServiceAgreements.svelte";
  import ClientStakeholderList from "@app/routes/Clients/Stakeholders/Stakeholders.svelte";
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
  import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
  import Role from "@shared/Role.svelte";
  import Options from "./Options.svelte";
  import Notes from "../Notes/Notes.svelte";

  $: rolesStore = $RolesStore;

  export let params;

  let client_id = params.client_id;

  let stored_client = {};
  let client = {};
  let mounted = false;
  let show = false;
  let dob_start_year = new Date().getFullYear() - 120;
  let dob_end_year = new Date().getFullYear();
  let latest_activity = null;

  let readOnly = false;

  onMount(async () => {
    client = await getClient(client_id);

    if (client.restrictive_practices == "1")
      client.restrictive_practices = true;
    if (client.restrictive_practices == "0")
      client.restrictive_practices = false;

    stored_client = Object.assign({}, client);

    mounted = true;

    readOnly = !haveCommonElements(rolesStore, ["participant.modify"]);

    document.title = client.name;
    BreadcrumbStore.set({
      path: [{ url: "/clients", name: "Clients" }, { name: client.name }],
    });
  });

  function undo() {
    client = Object.assign({}, stored_client);
  }

  function save() {
    jspa("/Client", "updateClient", client).then((result) => {
      // Make a copy of the object
      stored_client = Object.assign({}, client);
      toastSuccess("Client updated successfully");
    });
  }

  function retrieveLatestActivity(actionType) {
    let data = {
      entity_id: client_id,
      entity_type: "participant",
      action_type: actionType,
    };

    jspa("/ActivityLog", "getLatestActivityLog", data).then((result) => {
      latest_activity = result.result;
    });
  }

  $: {
    if (mounted) {
      ActionBarStore.set({
        can_delete: false,
        show: !(JSON.stringify(client) === JSON.stringify(stored_client)),
        undo: () => undo(),
        save: () => save(),
      });
    }

    if (client.on_hold == 1) {
      retrieveLatestActivity("on-hold");
    }
  }
</script>

<div class="flex justify-between items-center mb-4">
  <div class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular">
    {client.name}
  </div>
  <Role roles={["participant.modify"]}>
    <Options bind:client bind:stored_client />
  </Role>
</div>

{#if client.restrictive_practices == true}
  <div
    class="bg-white rounded-md px-4 py-2 mb-2 text-base text-indigo-600 mb-3 flex justify-between items-center font-medium"
    role="alert"
  >
    Restrictive practices are in place
  </div>
{/if}

{#if client.archived == 1}
  <div
    class="bg-red-100 rounded-md px-4 py-2 mb-2 text-base text-red-700 mb-3 flex justify-between"
    role="alert"
  >
    {client.name} has been archived.
  </div>
{/if}

{#if client.on_hold == 1}
  <div
    class="bg-indigo-100 rounded-md px-4 py-2 mb-2 text-base text-indigo-600 mb-3"
    role="alert"
  >
    <div class="flex items-center gap-1">
      <span>{client.name} is on hold</span>
      {#if latest_activity}
        {#if latest_activity.timestamp && latest_activity.timestamp !== null}
          <span>
            {formatDate(latest_activity.timestamp)}
          </span>
        {/if}
      {/if}
    </div>
    {#if latest_activity}
      {#if latest_activity.reason && latest_activity.reason !== ""}
        <div>
          {latest_activity.reason}
        </div>
      {/if}
    {/if}
  </div>
{/if}

<FloatingInput
  bind:value={client.name}
  label="Client Name"
  placeholder="eg: Chris Person"
  {readOnly}
/>
<div class="flex gap-x-2 flex-wrap">
  <div class="w-full sm:w-auto sm:flex-1">
    <FloatingInput
      bind:value={client.ndis_number}
      label="NDIS Number"
      placeholder="eg: XXXXXXXXXX"
      {readOnly}
    />
  </div>
  <div
    class="flex flex-wrap xs:flex-nowrap gap-2 justify-between w-full sm:w-auto"
  >
    <FloatingDate
      class="w-full sm:w-auto"
      bind:value={client.ndis_plan_start_date}
      label="NDIS Plan Start Date"
      {readOnly}
    />
    <FloatingDate
      class="w-full sm:w-auto"
      bind:value={client.ndis_plan_end_date}
      label="NDIS Plan End Date"
      {readOnly}
    />
  </div>
</div>

<FloatingInput
  bind:value={client.phone_number}
  label="Phone Number"
  placeholder="eg: XXXX XXX XXX"
  {readOnly}
/>
<FloatingInput
  bind:value={client.mailing_address}
  label="Address"
  placeholder="eg: 1 Someplace St, Adelaide SA 1234"
  {readOnly}
/>
<FloatingInput
  bind:value={client.service_provision_location}
  label="Service Provision Location"
  placeholder="eg: 1 Someplace St, Adelaide SA 1234"
  {readOnly}
/>
<FloatingDate
  bind:value={client.date_of_birth}
  label="Date of Birth"
  start_year={dob_start_year}
  end_year={dob_end_year}
  {readOnly}
/>
<FloatingInput
  bind:value={client.referrer}
  label="Referrer"
  placeholder="eg: Alex Citizen"
  {readOnly}
/>

<ClientReports {client_id} />

<ClientServiceAgreements bind:client_id />

<Role roles={["therapist", "participant.modify"]}>
  <Container>
    <ClientStakeholderList bind:client {client_id} />
  </Container>
</Role>

<Role roles={["participant.modify"]}>
  <Notes bind:client_id />
  <!-- <Container>
        <h1 class="text-black text-1xl font-bold mt-0 drop-shadow">
            Old Notes
        </h1>
        <div class="text-xs mb-2">This section is being retired.</div>
        <FloatingTextArea
            bind:value={client.notes}
            label="Notes"
            placeholder="eg: Extra Notes."
            style="height:350px"
            readOnly={true}
        />
    </Container> -->
</Role>
