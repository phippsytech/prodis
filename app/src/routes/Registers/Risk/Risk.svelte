<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import RiskForm from "./RiskForm.svelte";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { push } from "svelte-spa-router";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    export let params;

    let risk = {
        status: "open",
    };
    let stored_risk = Object.assign({}, risk);

    let readOnly = false;

    BreadcrumbStore.set({ 
        path: [
            { url: "/registers", name: "Registers" },
            { url: "/registers/risks", name: "Risks" }
        ] 
    });

    let mounted = false;
    let show = false;

    onMount(() => {
        if (params.id != "add") {
            jspa("/Register/Risk", "getRisk", { id: params.id })
                .then((result) => {
                    risk = result.result;
                })
                .catch(() => {})
                .finally(() => {
                    // Make a copy of the object
                    stored_risk = Object.assign({}, risk);
                });
        }
        mounted = true;
    });

    function undo() {
        risk = Object.assign({}, stored_risk);
    }

    function save() {
        if (risk.resolution && risk.resolution.trim() !== "") {
            risk.date_resolved = new Date().toISOString().split('T')[0];
            risk.status = "resolved";
        }
        jspa("/Register/Risk", "updateRisk", risk)
            .then(() => {
                push("/registers/risks/");
                toastSuccess("Risk saved successfully");
            })
            .catch(() => {
                toastError("Failed to save risk");
            });
    }

    function deleteRisk() {
        if (confirm("Are you sure you want to delete this risk?")) {
            jspa("/Register/Risk", "deleteRisk", { id:  risk.id })
            .then((result) => {
                toastSuccess("Risk successfully deleted");
                push("/registers/risks");
            })
            .catch(() => {
                toastError("Error deleting  risk");
            });
        }
    }

    $: {
        if (mounted) {
            const valueChanged = JSON.stringify(risk) !== JSON.stringify(stored_risk);
            ActionBarStore.set({
                can_delete: true,
                show: true,
                undo: () => undo(),
                save: () => save(),
                delete: () => deleteRisk(),
            });
        }
    }
</script>

<div class="mb-2 mt-2" style="color: rgb(34, 0, 85);">
    <h1
        class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
    >
        Risk Details
    </h1>
</div>

<RiskForm bind:risk />
