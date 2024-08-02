<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import RiskForm from "./RiskForm.svelte";

    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    export let params;

    let risk = {
        status: "open",
    };
    let stored_risk = Object.assign({}, risk);

    let readOnly = false;

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });

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
        jspa("/Register/Risk", "updateRisk", risk)
            .then((result) => {
                risk = result.result;
                stored_risk = Object.assign({}, risk);
                // let result = result.result.id;
            })
            .catch(() => {});
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: false,
                show: !(JSON.stringify(risk) === JSON.stringify(stored_risk)),
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
    Add Risk
</div>

<RiskForm bind:risk />
