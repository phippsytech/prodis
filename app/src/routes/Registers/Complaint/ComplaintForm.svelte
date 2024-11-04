<script>
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";
  import NewFloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
  import { jspa } from "@shared/jspa.js";
  import StaffMultiSelector from "@shared/StaffMultiSelector.svelte";
  import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
  import { onMount } from "svelte";
  import Role from "@shared/Role.svelte";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";
  import { push } from "svelte-spa-router";
  import RTE from "@shared/RTE/RTE.svelte";
  import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";

  let staffs = [];
  let clients = [];

  let staffIds = [];
  let client_id = null;

  let complaintStatusItems = [
    { option: "Open", value: "open" },
    { option: "Closed", value: "closed" },
  ];

  let complaintTypeItems = [
    { option: "Service Achievement", value: "service achievement" },
    { option: "Service Quality", value: "service quality" },
    { option: "Service Suitability", value: "service suitability" },
    { option: "Staff Behaviour", value: "staff behaviour" },
    { option: "Financial", value: "financial" },
    { option: "Technical", value: "technical" },
  ];

  let complaintDepartmentItems = [
    { option: "Clinical", value: "Clinical" },
    { option: "Administration", value: "Administration" },
    { option: "Finance", value: "finance" },
    { option: "Management", value: "management" },
    { option: "Other", value: "other" },
  ];

  let complaintNotifiedOfOutcome = [
    { option: "Outcome Letter Sent", value: "outcome letter sent" },
    { option: "Discussed With Complaint", value: "discussed with complaint" },
  ];

  let complaintActionsTaken = [
    { option: "Reviewed service delivery", value: "reviewed service delivery" },
    { option: "Issued an apology", value: "issued an apology" },
    {
      option: "Provided a follow-up appointment",
      value: "provided a follow-up appointment",
    },
  ];

  export let complaint = {
    status: "under investigation",
    complainant_client_id: null,
    complainant_contact_details: null,
    department: null,
    complaint_type: null,
    received_by: null,
    details: null,
    outcome_wanted: null,
    is_staff_notified: false,
    notified_staffs_id: [],
    date_actioned: null,
    complainant_notified: false,
    complainant_feedback: null,
    continuous_improvement: null,
    ndis_commission_notified: false,
    ndis_commission_id: null,
    recommended_actions: null,
    archived: false,
    complaint_feedback: null,
    type: null,
  };

  let complainantSatisfaction = [
    { option: "Very Satisfied", value: "very satisfied" },
    { option: "Satisfied", value: "satisfied" },
    { option: "Neautral", value: "neautral" },
    { option: "Unsatisfied", value: "unsatisfied" },
    { option: "Very Unsatisfied", value: "very unsatisfied" },
  ];

  export let readOnly = false;

  onMount(() => {
    if (complaint.notified_staffs_id) {
      console.log("staffs", complaint.notified_staffs_id);
    }
  });

  jspa("/Staff", "listStaff", {}).then((result) => {
    staffs = result.result
      .filter((item) => item.archived != 1)
      .map((item) => ({
        label: `${item.staff_name}`,
        value: item.id,
      }))
      .sort((a, b) => a.label.localeCompare(b.label));
  });

  jspa("/Participant", "listClients", {}).then((result) => {
    clients = result.result
      .filter((item) => item.archived != 1)
      .map((item) => ({
        label: `${item.client_name}`,
        value: item.client_id,
      }))
      .sort((a, b) => a.label.localeCompare(b.label));
  });

  function deleteComplaint() {
    jspa("/Register/Complaint", "deleteComplaint", { id: complaint.id })
      .then((result) => {
        toastSuccess("Complaint deleted successfully.");
        push("/registers/complaints");
      })
      .catch((error) => {
        toastError("Error deleting complaint, please try again.");
      });
  }
</script>

<!-- new form -->
<div class="mt-4 mb-2">
  <h3 class="text-base font-bold leading-6 text-gray-900 px-3">
    The Complaint
  </h3>
</div>

<div class="flex space-x-4 w-full">
  <div class="flex-1">
    <FloatingDate
      bind:value={complaint.date_complaint}
      label="Date of complaint"
    />
  </div>
  <div class="flex-1">
    <FloatingCombo
      bind:value={complaint.complainant_client_id}
      items={clients}
      label="Participant"
      placeholderText="Select or type participant name"
    />
  </div>
  <!-- <div class="flex-1">
      <NewFloatingSelect  class="flex-1 min-w-0 w-full sm:w-1/2"
          on:change
          bind:value={complaint.complaint_type}
          label="Type"
          instruction="Set Type"
          options={complaintTypeItems}
          clearable
      />
  </div> -->
</div>

<div class="flex space-x-4 w-full">
  <div class="flex-1">
    <FloatingInput
      bind:value={complaint.complainant_name}
      label="Complainant"
      placeholder="Name of Complainant"
    />
  </div>
  <div class="flex-1">
    <FloatingInput
      bind:value={complaint.email}
      label="Complainant Email"
      placeholder="eg: Chris Person"
    />
  </div>
  <div class="flex-1">
    <FloatingInput
      bind:value={complaint.phone}
      label="Phone Number"
      placeholder="eg: XXXX XXX XXX"
    />
  </div>
</div>

<span class="ml-2 text-xs text-gray-900/50">Complaint details</span>
<RTE bind:content={complaint.details} />

<span class="ml-2 text-xs text-gray-900/50">Outcome requested</span>
<RTE bind:content={complaint.outcome_wanted} />

<div class="mt-4 mb-2">
  <h3 class="text-base font-bold leading-6 text-gray-900 px-3">Investigate</h3>
</div>
<FloatingTextArea
  bind:value={complaint.investigation_result}
  label="Investigation result"
  placeholder=""
/>

<div class="mt-4 mb-2">
  <h3 class="text-base font-bold leading-6 text-gray-900 px-3">Review</h3>
</div>

<FloatingTextArea
  bind:value={complaint.recommended_actions}
  label="Recommendations"
  placeholder=""
/>
