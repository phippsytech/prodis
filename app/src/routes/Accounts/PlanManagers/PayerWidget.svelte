<script>
    import Container from "@shared/Container.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import PlanManagerSelector from "./PlanManagerSelector.svelte";

    export let service = {};
</script>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">Payer</h1>

<div class="form-check mb-2">
    <PlanManagerSelector bind:planmanager_id={service.plan_manager_id} />
    <div class="text-xs px-4">
        <a class=" text-blue-500" href="/#/planmanagers/add"
            >Add a plan manager</a
        > if not available in the list
    </div>
</div>
