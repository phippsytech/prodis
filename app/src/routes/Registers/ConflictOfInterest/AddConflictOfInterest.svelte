<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import ConflictOfInterestForm from "./ConflictOfInterestForm.svelte";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    
    let conflictofinterest = {};
    conflictofinterest.status = "open";

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });

    conflictofinterest.staff_id = null;

    // get staff id of logged in user
    jspa("/Staff", "getMyStaffId", {})
        .then((result) => {
            conflictofinterest.staff_id = result.result.id;
        })
        .catch(() => {});

    function addconflictofinterest() {
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
