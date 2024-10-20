<script>
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import NewFloatingSelect from '@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte';
    import { jspa } from "@shared/jspa.js";
    import StaffMultiSelector from "@shared/StaffMultiSelector.svelte";
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import { onMount  } from "svelte";
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
        {option: "Open", value: "open"},
        {option: "Closed", value: "closed"},
    ];

    let complaintTypeItems = [
        {option: "Service Achievement", value: "service achievement"},
        {option: "Service Quality", value: "service quality"},
        {option: "Service Suitability", value: "service suitability"},
        {option: "Staff Behaviour", value: "staff behaviour"},
        {option: "Financial", value: "financial"},
        {option: "Technical", value: "technical"},
    ];


    let complaintDepartmentItems = [
        {option: "Clinical", value: "Clinical"},
        {option: "Administration", value: "Administration"},
        {option: "Finance", value: "finance"},
        {option: "Management", value: "management"},
        {option: "Other", value: "other"},
    ];

    let complaintNotifiedOfOutcome = [
        {option: "Outcome Letter Sent", value: "outcome letter sent"},
        {option: "Discussed With Complaint", value: "discussed with complaint"},
    ];

    let complaintActionsTaken = [
        {option: "Reviewed service delivery", value: "reviewed service delivery"},
        {option: "Issued an apology", value: "issued an apology"},
        {option: "Provided a follow-up appointment", value: "provided a follow-up appointment"}
    ];

      

    export let complaint={
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
        complaint_feedback: null
    };

    let complainantSatisfaction= [
      {option: "Very Satisfied", value: "very satisfied"},
      {option: "Satisfied", value: "satisfied"},
      {option: "Neautral", value: "neautral"},
      {option: "Unsatisfied", value: "unsatisfied"},
      {option: "Very Unsatisfied", value: "very unsatisfied"}
    ];

    export let readOnly =false


    onMount(() => {
      if (complaint.notified_staffs_id) {
        console.log('staffs', complaint.notified_staffs_id);

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

  // TODO:
  // need to incorporate other fields from the complaint model
  // initial multi-step form idea for this register

  // # The Complaint:
  // Date Received
  // Complainant
  // Participant
  // Complainant Email
  // Complainant Phone
  // Complaint Details
  // Outcome Requested

  // # Investigate
  // Date Acknowledged (letter sent)
  // Investigation Result
  // Does the NDIS Commission need to be notified?
  // NDIS Commission ID

  // # Resolve
  // Staff Notified ... hmm
  // Notification Date... Hmm again
  // Complainant Notified of Outcome (Date)
  
  // # Review
  // Complaint Feedback Survey
  // Continuous Improvement Required?
  // Recommendations, Actions, Notes
</script>
<!-- new form -->
<div class="mt-4 mb-2">
  <h3 class="text-base font-bold leading-6 text-gray-900 px-3">The Complaint</h3>
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
      bind:value={complaint.complainant_client_id_id}
      items={clients}
      label="Participant"
      placeholderText="Select or type participant name"
    />
  </div>
</div>

<div class="flex space-x-4 w-full">
  <div class="flex-1">
    <FloatingInput 
      bind:value={complaint.complainant_name} 
      label="Complainant" 
      placeholder="Name of Complainant" 
      {readOnly}
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
      label="Complainant Phone"
      placeholder="eg: Chris Person"
    />
  </div>
</div>
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


<!-- old form -->
    
<!-- <h3 class="text-slate-800 font-bold mx-2 mb-2">The Complaint</h3> -->

<!-- <div class="flex flex-wrap gap-2 flex-none md:flex-row flex-col w-full">
  <FloatingDate 
    label="Date of Complaint" 
    bind:value={complaint.date_complaint} 
    class="w-full md:w-auto" 
  />

  <FloatingInput 
    bind:value={complaint.complainant_name} 
    label="Complainant" 
    placeholder="Name of Complainant" 
    {readOnly}
  />

  <NewFloatingSelect
    class="flex-1 min-w-0 w-full sm:w-1/2"
    on:change
    bind:value={complaint.status}
    label="Status"
    instruction="Set Status"
    options={complaintStatusItems}
    {readOnly}
    clearable

   
    />
  <NewFloatingSelect  class="flex-1 min-w-0 w-full sm:w-1/2"
    on:change
    bind:value={complaint.complaint_type}
    label="Type"
    instruction="Set Type"
    options={complaintTypeItems}
    {readOnly}
    clearable
    />
</div>

  <FloatingCombo
    label="Clients"
    items={clients}
    bind:value={complaint.complainant_client_id}
    placeholderText="Select or type client name ..."
  /> -->



<!-- <div class="flex flex-wrap gap-2 items-center">
    <FloatingInput 
      bind:value={complaint.email} 
      label="Email" 
      placeholder="Email address of person" 
      {readOnly} 
      class="flex-1 min-w-0 w-full sm:w-auto"
    />
    <FloatingInput 
      bind:value={complaint.phone} 
      label="Phone" 
      placeholder="Phone number of person" 
      {readOnly} 
      class="flex-1 min-w-0 w-full sm:w-auto"
    />
  </div> -->
<!-- <FloatingTextArea bind:details={complaint.details} label="Message" placeholder="Message" style="height:150px" {readOnly}/> -->
<!-- <RTE bind:content={complaint.details} /> -->
<!-- <FloatingTextArea bind:value={complaint.outcome_wanted} label="Expected Resolution" placeholder="What outcome does complainant want?" style="height:150px" {readOnly}/> -->


<!-- <h3 class="text-slate-800 font-bold mx-2 mb-2">Investigate and Resolve</h3> -->
<!-- <div class="flex flex-wrap gap-2 flex-none md:flex-row flex-col w-full">
  <FloatingDate label="Resolution Date" bind:value={complaint.resolution_date} class="w-full md:w-auto" />
  <NewFloatingSelect
  on:change
  bind:value={complaint.notified_of_outcome}
  label="Actions Taken"
  instruction="Set Actions Taken"
  options={complaintActionsTaken}
  {readOnly}
  clearable

/> -->
  <!-- <NewFloatingSelect
    on:change
    bind:value={complaint.notified_of_outcome}
    label="Complainant Notified of Outcome"
    instruction="Select outcome"
    options={complaintNotifiedOfOutcome}
    {readOnly}
    clearable

  />
  <NewFloatingSelect
    on:change
    bind:value={complaint.complainant_feedback}
    label="Complainant Feedback Survey"
    instruction="Select Satisfaction"
    options={complainantSatisfaction}
    {readOnly}
    clearable

  /> -->
<!-- </div> -->
<!-- <StaffMultiSelector bind:staff_ids={complaint.notified_staffs_id}/> -->

<!-- <FloatingTextArea bind:value={complaint.investigation_result  } label="Investigation Result" placeholder="Investigation Result." style="height:150px" {readOnly}/>
<h3 class="text-slate-800 font-bold mx-2 mb-2">Review</h3>
<FloatingTextArea bind:value={complaint.continuous_improvement  } label="Continuous Improvement Listing" placeholder="Has a Resolution Required a Continuous Improvement Listing?" style="height:150px" {readOnly}/>
<FloatingTextArea bind:value={complaint.recommended_actions  } label="Recommendations, Actions or Notes" placeholder="Recommendations, Actions or Notes." style="height:150px" {readOnly}/> -->

<!-- {#if complaint.id}
  <Role roles={["admin"]}>
    
    <div class="flex">
      <button 
          class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
          on:click="{deleteComplaint}"      
          >
          Delete
      </button>
    </div>

  </Role>

{/if} -->

