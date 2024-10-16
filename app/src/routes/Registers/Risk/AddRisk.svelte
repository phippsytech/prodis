<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import RiskForm from "./RiskForm.svelte";

    let risk = {};

    risk.status = "open";
    risk.resolution = null;

    BreadcrumbStore.set({ 
        path: [
            { url: "/registers", name: "Registers" },
            { url: "/registers/risks", name: "Risks" },
        ] 
    });

    risk.staff_id = null;

    // get staff id of logged in user
    jspa("/Staff", "getMyStaffId", {})
        .then((result) => {
            risk.staff_id = result.result.id;
        })
        .catch(() => {});

        function addRisk() {
            if (risk.resolution && risk.resolution.trim() !== "") {
                risk.date_resolved = new Date().toISOString().split('T')[0]; 
                risk.status = "resolved";
            } else {
                risk.date_resolved = null;
            }

            if (risk.staff_id && risk.type && risk.description) {
                risk.date_identified = new Date().toISOString().split('T')[0]; 
                risk.status = risk.status !== "resolved" ? "open" : risk.status;
            }

            jspa("/Register/Risk", "addRisk", risk)
                .then(() => {
                    push("/registers/risks/");
                    toastSuccess("Risk added successfully");
                })
                .catch(() => {
                    toastError("Failed to add risk");
                });
        }
</script>

<div class="mb-2 mt-2" style="color: rgb(34, 0, 85);">
    <h1
        class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
    >
        Add Risk
    </h1>
</div>
<RiskForm bind:risk />

<div class="flex justify-between">
    <span></span>
    <Button on:click={() => addRisk()} label="Add Risk" />
</div>
