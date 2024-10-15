<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    import RiskForm from "./RiskForm.svelte";

    let risk = {};

    risk.status = "open";

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });

    risk.staff_id = null;

    // get staff id of logged in user
    jspa("/Staff", "getMyStaffId", {})
        .then((result) => {
            risk.staff_id = result.result.id;
        })
        .catch(() => {});

    function addRisk() {
        jspa("/Register/Risk", "addRisk", risk)
            .then((result) => {
                let risk_id = result.result.id;
                push("/registers/risks/" + risk_id);
            })
            .catch(() => {});
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
