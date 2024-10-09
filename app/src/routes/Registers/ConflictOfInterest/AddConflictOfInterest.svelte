<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import ConflictOfInterestForm from "./ConflictOfInterestForm.svelte";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    
    let conflictofinterest = {};
    

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });

    conflictofinterest.staff_id = null;


    const validations = [
        { check: () => !conflictofinterest.date_identified, message: "Conflict of Interest date must be provided." },
        { check: () => !conflictofinterest.type, message: "Conflict of Interest type must be provided." },
        { check: () => !conflictofinterest.status, message: "Conflict of Interest status must be provided." },
        { check: () => !conflictofinterest.staff_id , message: "Please select a staff." },
        { check: () => !conflictofinterest.parties_involved, message: "Parties Involved name must be provided." },
        { check: () => !conflictofinterest.description, message: "Details must be provided." },
        { check: () => (conflictofinterest.date_resolved && !conflictofinterest.resolution) , message: "Resolution taken must be provided." },
        
    ];

    function validate() {
        for (const { check, message } of validations) {
            if (check()) {
                toastError(message);
                return false;
            }
        }
        return true;
    }

    // get staff id of logged in user
    jspa("/Staff", "getMyStaffId", {})
        .then((result) => {
            conflictofinterest.staff_id = result.result.id;
        })
        .catch(() => {});

    function addconflictofinterest() {
        if (!validate()) {
            return; 
        }
        
        jspa(
            "/Register/ConflictOfInterest",
            "addConflictOfInterest",
            conflictofinterest,
        )
            .then((result) => {
                let conflictofinterest_id = result.result.id;
                toastSuccess("Conflict of Interest added successfully.");
                push("/registers/conflictofinterests");
            })
            .catch(() => {});
    }
</script>

<ConflictOfInterestForm bind:conflictofinterest />

<div class="flex justify-between">
    <span></span>
    <Button
        on:click={() => addconflictofinterest()}
        label="Add conflict of interest"
    />
</div>
