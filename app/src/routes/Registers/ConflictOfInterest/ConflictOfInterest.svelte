<script>
    import { onMount } from "svelte";
    import ConflictOfInterestForm from "./ConflictOfInterestForm.svelte";
    import { jspa } from "@shared/jspa.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { push } from "svelte-spa-router";

    export let params;

    let conflictofinterest = {
        status: "unresolved",
    };
    let stored_conflictofinterest = Object.assign({}, conflictofinterest);
    let readOnly = false;
    let mounted = false;


    const validations = [
        { check: () => !conflictofinterest.date_identified, message: "Conflict of Interest date must be provided." },
        { check: () => !conflictofinterest.type, message: "Conflict of Interest type must be provided." },
        { check: () => !conflictofinterest.status, message: "Conflict of Interest status must be provided." },
        { check: () => !conflictofinterest.staff_id , message: "Please select a staff." },
        { check: () => !conflictofinterest.parties_involved, message: "Parties Involved name must be provided." },
        { check: () => !conflictofinterest.description, message: "Details must be provided." },
        { check: () => (conflictofinterest.date_identified > conflictofinterest.date_resolved) && conflictofinterest.resolution.trim() !== "", message: "Resolution date must not be before the conflict date." },
        { check: () => (conflictofinterest.date_resolved && !conflictofinterest.resolution) , message: "Resolution taken must be provided." },
    ];

    

    BreadcrumbStore.set({ 
        path: [
            { url: "/registers", name: "Registers" },
            { url: "/registers/conflictofinterests/", name: "Conflict of Interests" }
        ] 
    });

    onMount(() => {
        if (params.id != "add") {
            jspa("/Register/ConflictOfInterest", "getConflictOfInterest", {
                id: params.id,
            })
                .then((result) => {
                    conflictofinterest = result.result;
                })
                .catch(() => {})
                .finally(() => {
                    // Make a copy of the object
                    stored_conflictofinterest = Object.assign(
                        {},
                        conflictofinterest,
                    );
                });
        }
        mounted = true;
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


    function undo() {
        conflictofinterest = Object.assign({}, stored_conflictofinterest);
    }

    function save() {
        if (!validate()) {
            return; 
        }

        
        if (conflictofinterest.date_resolved && conflictofinterest.resolution.trim() != "") {
            conflictofinterest.status = 'resolved';     
        } else {
            conflictofinterest.status = 'unresolved';   
        }

        jspa(
            "/Register/ConflictOfInterest",
            "updateConflictOfInterest",
            conflictofinterest,
        )
            .then((result) => {
                conflictofinterest = result.result;
                stored_conflictofinterest = Object.assign(
                    {},
                    conflictofinterest,
                );
                // let result = result.result.id;

                toastSuccess("Conflict of Interest updated successfully.");
                push("/registers/conflictofinterests");
            })
            .catch(() => {});
    }

    function deleteConflictOfInterest() {
        if (confirm("Are you sure you want to delete this conflict of interest?")) {
            jspa("/Register/ConflictOfInterest", "deleteConflictOfInterest", { id: conflictofinterest.id })
           .then((result) => {
                toastSuccess("Complaint deleted successfully.");
                push("/registers/conflictofinterests");
            })
           .catch((error) => {
                toastError("Error deleting complaint, please try again.");
            });
        }
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: true,
                show: true,
                undo: () => undo(),
                save: () => save(),
                delete: () => deleteConflictOfInterest()
            });
        }

        if (stored_conflictofinterest.date_resolved && stored_conflictofinterest.resolution.trim() != "") {
            conflictofinterest.status = 'resolved';     
        }
    }
</script>

<div class="mb-2 mt-2" style="color: rgb(34, 0, 85);">
    <h1
        class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
    >
        Conflict of Interest Details
    </h1>
</div>
<ConflictOfInterestForm bind:conflictofinterest/>
