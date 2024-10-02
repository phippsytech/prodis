<script>
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import FloatingSelect from '@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte';
    import NewFloatingSelect from '@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte';
    import { jspa } from "@shared/jspa.js";
    import FloatingCombo from "@shared/PhippsyTech/svelte-ui/forms/FloatingCombo.svelte";
    import StaffMultiSelector from "@shared/StaffMultiSelector.svelte";
  import { consoleLogs } from '@app/Overlays/stores';
    let feedbackStatusSelectElement = null;

    let staffs = [];
    let clients = [];

    let staffIds = [];
    let client_id = null;

    let complaintStatusItems = [
        {option: "Under Investigation", value: "under investigation"},
        {option: "Escelated Internally", value: "escelated internally"},
        {option: "Resoultion Proposed", value: "resoultion Proposed"},
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

    

    export let complaint={
        status: "open",
        type: "complaint",
        name: null,
        email: null,
        phone: null,
        message: null,
        resolution: null,
    };

    export let readOnly =false

    $: {
        readOnly = complaint.status=="closed"
    }


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
          value: item.id,
        }))
        .sort((a, b) => a.label.localeCompare(b.label));
    });

    $: {
        console.log(staffIds);
    }

</script>
    
<h3 class="text-slate-800 font-bold mx-2">Complaint Management Register</h3>
<NewFloatingSelect
    on:change
    bind:value={complaint.status}
    label="Status"
    instruction="Set Status"
    options={complaintStatusItems}
    {readOnly}
    />


<FloatingCombo bind:value={client_id} items={clients} label="Client" placeholderText="Name of Client" {readOnly}/>
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
<FloatingTextArea bind:value={complaint.message} label="Message" placeholder="Message" style="height:150px" {readOnly}/>
<FloatingTextArea bind:value={complaint.resolution} label="Resolution" placeholder="List actions taken to resolve the feedback." style="height:150px" {readOnly}/>


<h3 class="text-slate-800 font-bold mx-2">Investigate and Resolve</h3>
<StaffMultiSelector on:change staff_ids={staffIds}/>
<FloatingTextArea bind:value={complaint.resolution} label="Investigation Result" placeholder="Investigation Result." style="height:150px" {readOnly}/>
