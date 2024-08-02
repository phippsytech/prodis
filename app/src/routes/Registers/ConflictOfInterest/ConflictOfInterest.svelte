<script>
    import { onMount } from "svelte";
    import ConflictOfInterestForm from "./ConflictOfInterestForm.svelte";
    import { jspa } from "@shared/jspa.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    export let params;

    let conflictofinterest = {
        status: "open",
    };
    let stored_conflictofinterest = Object.assign({}, conflictofinterest);
    let readOnly = false;
    let mounted = false;

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
