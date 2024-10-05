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

    let staffs = [];
    let clients = [];

    let staffIds = [];
    let client_id = null;

    let complaintStatusItems = [
        {option: "Under Investigation", value: "under investigation"},
        {option: "Escelated Internally", value: "escelated internally"},
        {option: "Resoultion Proposed", value: "resoultion proposed"},
        {option: "Resolved", value: "resolved"},
        {option: "Unresolved", value: "unresolved"},
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
          option: `${item.client_name}`,
          value: item.client_id,
        }))
        .sort((a, b) => a.option.localeCompare(b.option));

        console.log('clients', clients);
        
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
    
<h3 class="text-slate-800 font-bold mx-2 mb-2">Complaint Management Register</h3>
<div class="flex flex-wrap gap-2 flex-none md:flex-row flex-col w-full">
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


<NewFloatingSelect
  on:change
  bind:value={complaint.complainant_client_id}
  label="Client"
  instruction="Select Client"
  options={clients}
  {readOnly}
  clearable
  />


<FloatingInput bind:value={complaint.name} label="Complainant" placeholder="Name of Complainant" {readOnly}/>
<div class="flex flex-wrap gap-2 items-center">
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
  </div>
<FloatingTextArea bind:value={complaint.details} label="Message" placeholder="Message" style="height:150px" {readOnly}/>
<FloatingTextArea bind:value={complaint.outcome_wanted} label="Expected Resolution" placeholder="What outcome does complainant want?" style="height:150px" {readOnly}/>


<h3 class="text-slate-800 font-bold mx-2 mb-2">Investigate and Resolve</h3>
<div class="flex flex-wrap gap-2 flex-none md:flex-row flex-col w-full">
  <FloatingDate label="Date Actioned" bind:value={complaint.date_actioned} class="w-full md:w-auto" />
  <NewFloatingSelect
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

  />
</div>
<StaffMultiSelector bind:staff_ids={complaint.notified_staffs_id}/>

<FloatingTextArea bind:value={complaint.investigation_result  } label="Investigation Result" placeholder="Investigation Result." style="height:150px" {readOnly}/>
<h3 class="text-slate-800 font-bold mx-2 mb-2">Review</h3>
<FloatingTextArea bind:value={complaint.continuous_improvement  } label="Continuous Improvement Listing" placeholder="Has a Resolution Required a Continuous Improvement Listing?" style="height:150px" {readOnly}/>
<FloatingTextArea bind:value={complaint.recommended_actions  } label="Recommendations, Actions or Notes" placeholder="Recommendations, Actions or Notes." style="height:150px" {readOnly}/>

{#if complaint.id}
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

{/if}

