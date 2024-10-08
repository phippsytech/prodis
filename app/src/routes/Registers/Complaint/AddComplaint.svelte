<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import ComplaintForm from "./ComplaintForm.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";

    let complaint = {};

    complaint.status = "open";

    const validations = [
        { check: () => !complaint.date_complaint, message: "Complaint date must be provided." },
        { check: () => !complaint.complaint_type, message: "Complaint type must be provided." },
        { check: () => !complaint.status, message: "Complaint status must be provided." },
        { check: () => !complaint.complainant_client_id || complaint.complainant_client_id.length === 0, message: "Please select a client." },
        { check: () => !complaint.complainant_name, message: "Complainant's name must be provided." },
        { check: () => !complaint.details, message: "Details must be provided." },
        { check: () =>  (!complaint.resolution_date && complaint.date_complaint > complaint.resolution_date) , message: "Resolution date must not be before the complaint date provided." },
        { check: () => (complaint.resolution_date && !complaint.notified_of_outcome) , message: "Actions taken must be provided." },
        
    ];

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });

    function validate() {
        for (const { check, message } of validations) {
            if (check()) {
                toastError(message);
                return false;
            }
        }
        return true;
    }

    function addComplaint() {
        if (!validate()) {
            return; 
        }
        jspa("/Register/Complaint", "addComplaint", complaint)
            .then((result) => {
                let complaintId = result.result.id;
                
                toastSuccess("Complaint added successfully.")
                push("/registers/complaints/");
            })
            .catch(() => {});
    }
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
    style="color:#220055;"
>
    Add Complaint
</div>

<ComplaintForm bind:complaint />

<div class="flex justify-between">
    <span></span>
    <Button on:click={() => addComplaint()} label="Add complaint" />
</div>
