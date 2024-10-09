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
        { check: () => (conflictofinterest.date_identified > conflictofinterest.date_resolved), message: "Resolution date date must no be before the conflict date." },    
    ];

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });

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

    function undo() {
        conflictofinterest = Object.assign({}, stored_conflictofinterest);
    }

    function save() {
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

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: false,
                show: !(
                    JSON.stringify(conflictofinterest) ===
                    JSON.stringify(stored_conflictofinterest)
                ),
                undo: () => undo(),
                save: () => save(),
            });
        }
    }
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
    style="color:#220055;"
>
    Add Conflict of Interest
</div>
<ConflictOfInterestForm bind:conflictofinterest />
