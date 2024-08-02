<script>
    import { push } from "svelte-spa-router";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    let planmanager = {};

    BreadcrumbStore.set({
        path: [
            { url: "/accounts/planmanagers", name: "Plan Managers" },
            { url: null, name: "Add Plan Manager" },
        ],
    });

    function addPlanManager() {
        jspa("/PlanManager", "addPlanManager", planmanager).then((result) => {
            push("/accounts/planmanagers/" + result.result.id);
        });
    }
</script>

<div class="px-2 mb-2">
    <h2
        class="text-2xl font-fredoka-one-regular tracking-tight text-indigo-900"
    >
        Add a New Plan Manager
    </h2>
</div>

<FloatingInput
    bind:value={planmanager.name}
    label="Name"
    placeholder="eg: NDIA"
/>
<FloatingInput
    bind:value={planmanager.email}
    label="Email"
    placeholder="eg: accounts@ndia.gov.au"
/>

<div class="flex justify-between">
    <span></span>

    <button
        on:click={() => addPlanManager()}
        type="button"
        class="w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
        >Add</button
    >
</div>
