<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import Complaint from "./Complaint.svelte";
    import ComplaintForm from "./ComplaintForm.svelte";
    import NewFloatingSelect from '@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte';
    import FloatingInput from '@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte';
    import FloatingTextArea from '@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte';
    import FloatingDate from "@shared/PhippsyTech/svelte-ui/forms/FloatingDate.svelte";
    import StaffMultiSelector from "@shared/StaffMultiSelector.svelte";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { push } from "svelte-spa-router";
    
    export let params;

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

    let complaintNotifiedOfOutcome = [
        {option: "Outcome Letter Sent", value: "outcome letter sent"},
        {option: "Discussed With Complaint", value: "discussed with complaint"},
    ];


    let clients = [];
    

    export let complaint={
        status: "under investigation",
        complainant_client_id: null,
        complainant_contact_details: null,
        department: null,
        complaint_type: null,
        received_by: null,
        description: null,
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


    const validations = [
        { check: () => !complaint.date_complaint, message: "Complaint date must be provided." },
        { check: () => !complaint.status, message: "Complaint status must be provided." },
        { check: () => !complaint.complainant_client_id || complaint.complainant_client_id.length === 0, message: "Please select a client." },
        { check: () => !complaint.complainant_name, message: "Complainant's name must be provided." },
        { check: () => !complaint.details, message: "Details must be provided." },
        { check: () =>  (!complaint.resolution_date && complaint.date_complaint > complaint.resolution_date) , message: "Resolution date must not be before the complaint date provided." },
        { check: () => (complaint.resolution_date && !complaint.notified_of_outcome) , message: "Actions taken must be provided." },
        
    ];

    let storedComplaint = Object.assign({}, complaint);
    let mounted = false;
    let readOnly = false;

    BreadcrumbStore.set(
        { 
            path: [
                { url: "/registers", name: "Registers" }, 
                { url: "/registers/complaints", name: "Complaints" }, 
            ],
        }
    );

    onMount(() => {
        if (params.id != "add") {
           getComplaint(params.id);
        }
        mounted = true;
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

    
    function validate() {
        for (const { check, message } of validations) {
            if (check()) {
                toastError(message);
                return false;
            }
        }
        return true;
    }

    function getComplaint(id) {
        jspa("/Register/Complaint", "getComplaint", { id: id })
                .then((result) => {
                    complaint = result.result;
                    
                })
                .catch(() => {})
                .finally(() => {
                    // Make a copy of the object
                    storedComplaint = Object.assign({}, complaint);
                });
    }

    function undo() {
        complaint = Object.assign({}, storedComplaint);
    }

    function save() {
        console.log(complaint);
        
        if (!validate()) {
            return; 
        }
        
        jspa("/Register/Complaint", "updateComplaint", complaint)
            .then((result) => {
                if (result.error) {  
                    toastError(result.error);
                    return;
                }
                let updatedId = result.result;

                getComplaint(updatedId);
              
                // storedComplaint = Object.assign({}, complaint);
            })
            .then((result) => {
                toastSuccess("Complaint updated successfully.");
                push("/registers/complaints");
                
            })
            .catch((error) => {
                console.log('err', error);
                
                toastError("Error updating complaint, please try again.");
            });
    }

    function deleteComplaint() {
        if (confirm("Are you sure you want to delete this complaint?")) {
            jspa("/Register/Complaint", "deleteComplaint", { id: complaint.id })
            .then((result) => {
                toastSuccess("Complaint successfully deleted");
                push("/registers/complaints");
            })
            .catch((error) => {
                toastError("Error deleting complaint");
            });
        }
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: true,
                show: true, //!(JSON.stringify(complaint) === JSON.stringify(storedComplaint)),
                undo: () => undo(),
                save: () => save(),
                delete: () => deleteComplaint(),
            });
        }
    }

    $: {
        complaint.status = complaint.investigation_result && complaint.recommended_actions ? 'closed' : 'open';
        if (complaint.status === 'closed' && !complaint.date_actioned) {
            let today = new Date();
            complaint.date_actioned = today.toISOString().split('T')[0]; 
        }
        // console.log(complaint.status);
    }

    // $: {
    //     readOnly = complaint.status == "closed";
    // }

</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
    style="color:#220055;"
>
    Complaint Details
</div>


<ComplaintForm bind:complaint />