<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore, UserStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import ConflictOfInterestForm from "./ConflictOfInterestForm.svelte";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    
    let conflictofinterest = {};

    conflictofinterest.user_id = $UserStore.id;

    BreadcrumbStore.set({
        path: [
            { url: "/registers", name: "Registers" },
            { url: "/registers/conflictofinterests/", name: "Conflict of Interests" },
        ]
    });

    conflictofinterest.staff_id = null;


    const validations = [
        { check: () => !conflictofinterest.date_identified, message: "Conflict of Interest date must be provided." },
        { check: () => !conflictofinterest.type, message: "Conflict of Interest type must be provided." },
        { check: () => !conflictofinterest.status, message: "Conflict of Interest status must be provided." },
        // { check: () => !conflictofinterest.staff_id , message: "Please select a staff." },
        { check: () => !conflictofinterest.parties_involved, message: "Parties Involved name must be provided." },
        { check: () => !conflictofinterest.description, message: "Details must be provided." },
        
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


    $: conflictofinterest.date_resolved = conflictofinterest.resolution ? new Date().toISOString().slice(0, 10) : conflictofinterest.date_resolved;
    
    function addconflictofinterest() {
        if (!validate()) {
            return; 
        }

        // conflictofinterest.date_identified = new Date().toISOString().split('T')[0];

        if (conflictofinterest.date_resolved && conflictofinterest.resolution.trim() != "") {
            conflictofinterest.status = 'resolved';
        } else {
            conflictofinterest.status = 'unresolved';   
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
<div class="mb-2 mt-2" style="color: rgb(34, 0, 85);">
    <h1
        class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
    >
        Add Conflict of Interest
    </h1>
</div>

<ConflictOfInterestForm bind:conflictofinterest />

<div class="flex justify-between mt-2">
    <span></span>
    <Button
        on:click={() => addconflictofinterest()}
        label="Add Conflict of Interest"
    />
</div>
