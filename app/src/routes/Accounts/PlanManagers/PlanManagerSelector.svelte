<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/NewFloatingSelect.svelte";
    import { jspa } from "@shared/jspa.js";

    export let planmanager_id;
    export let readOnly = false;

    let planmanagers = [];
    let planmanagersList = [];
    let planmanagersSelectElement = null;

    jspa("/PlanManager", "listPlanManagers", {})
        .then((result) => {
            planmanagers = result.result;
            planmanagers.forEach((planmanager) => {
                if (planmanager.archived != 1)
                    planmanagersList.push({
                        option: planmanager.name,
                        value: planmanager.id,
                    });
            });
            planmanagersList.sort((a, b) => a.option.localeCompare(b.option));
            planmanagersList = planmanagersList;
        })
        .catch(() => {});
</script>

<FloatingSelect
    on:change
    bind:this={planmanagersSelectElement}
    bind:value={planmanager_id}
    label="Plan Manager"
    instruction="Choose Plan Manager"
    options={planmanagersList}
    hideValidation={true}
    {readOnly}
/>
