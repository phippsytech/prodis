<script>
    import { onMount } from "svelte";
    import { push, pop, replace } from "svelte-spa-router";
    import BillableForm from "./BillableForm.svelte";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { toast } from "@zerodevx/svelte-toast";
    export let params;

    let timetracking = {};
    let stored_timetracking = {};
    let mounted = false;
    let budget_exceeded = false;

    onMount(() => {
        BreadcrumbStore.set({
            path: [
                { url: "/billables", name: "Billables" },
                { url: null, name: "Edit" },
            ],
        });

        jspa("/TimeTracking", "getTimeTracking", { id: params.id })
            .then((result) => {
                timetracking = result.result;
                if (timetracking.internal == "1") timetracking.internal = true;
                if (timetracking.internal == "0") timetracking.internal = false;
                stored_timetracking = timetracking;
            })
            .catch(() => {})
            .finally(() => {
                // Make a copy of the object
                stored_timetracking = Object.assign({}, timetracking);
            });
        mounted = true;
    });

    function undo() {
        timetracking = Object.assign({}, stored_timetracking);
    }

    function validateBillable() {
        if (
            timetracking.claim_type == "CANC" &&
            timetracking.cancellation_reason == "Choose reason for cancellation"
        ) {
            // error
            toast.push("Please select a reason for cancellation", {
                theme: {
                    "--toastColor": "white",
                    "--toastBackground": "#ef4444",
                    "--toastBarBackground": "#dc2626",
                },
            });

            // success
            // toast.push("Please select a reason for cancellation", {
            //     theme: {
            //         "--toastColor": "white",
            //         "--toastBackground": "#16a34a",
            //         "--toastBarBackground": "#15803d",
            //     },
            // });

            return false;
        }

        if (!timetracking.client_id) {
            toast.push("Please select a client");
            return false;
        }
        if (!timetracking.service_id) {
            toast.push("Please select a service");
            return false;
        }
        if (!timetracking.session_date) {
            toast.push("Please select a date");
            return false;
        }
        if (!timetracking.session_duration) {
            toast.push("Please enter a duration");
            return false;
        }
        return true;
    }

    function save() {
        if (!validateBillable()) return;
        jspa("/TimeTracking", "updateTimeTracking", timetracking)
            .then((result) => {
                stored_timetracking = Object.assign({}, timetracking);
                pop();
            })
            .catch(() => {});
    }

    function del() {
        jspa("/TimeTracking", "deleteTimeTracking", { id: timetracking.id })
            .then((result) => {
                stored_timetracking = Object.assign({}, timetracking);
                pop();
            })
            .catch(() => {});
    }

    $: {
        let show =
            params.id != null ||
            !(
                JSON.stringify(timetracking) ===
                JSON.stringify(stored_timetracking)
            );
        let can_delete = params.id != null;

        if (
            budget_exceeded
        ) {
            show = false;
        }

        if (mounted) {
            ActionBarStore.set({
                can_delete: can_delete,
                show: show,
                undo: () => undo(),
                save: () => save(),
                delete: () => del(),
            });
        }
    }
</script>

<div
    class="text-2xl tracking-tight font-fredoka-one-regular mb-2 text-indigo-900"
>
    Edit Billable
</div>
{#if timetracking.id}
    <BillableForm 
        bind:timetracking={timetracking}
        bind:stored_edit_timetracking={stored_timetracking}
        bind:budget_exceeded
        mode="edit" />
{/if}
