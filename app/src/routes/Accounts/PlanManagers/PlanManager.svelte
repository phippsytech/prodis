<script>
    import { onMount } from "svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import Role from "@shared/Role.svelte";
    import Container from "@shared/Container.svelte";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

    export let params = {};

    let planmanager_id = params.planmanager_id;
    let planmanager = {};
    let stored_planmanager = {};
    let mounted = false;
    let show = false;
    let clients = [];

    onMount(async () => {
        await jspa("/PlanManager", "getPlanManager", {
            id: planmanager_id,
        }).then((result) => {
            planmanager = result.result;
            // Make a copy of the object
            stored_planmanager = Object.assign({}, planmanager);
            mounted = true;
        });

        await jspa("/PlanManager", "getClientsByPlanManagerId", {
            plan_manager_id: planmanager_id,
        }).then((result) => {
            clients = result.result;
        });

        BreadcrumbStore.set({
            path: [
                { url: "/accounts", name: "Accounts" },
                { url: "/accounts/planmanagers", name: "Plan Managers" },
                // { url: null, name: planmanager.name },
            ],
        });
    });

    function archivePlanManager() {
        jspa("/PlanManager", "archivePlanManager", { id: planmanager_id }).then(
            (result) => {
                planmanager = result.result;
                // Make a copy of the object
                stored_planmanager = Object.assign({}, planmanager);
            },
        );
    }

    function restorePlanManager() {
        jspa("/PlanManager", "restorePlanManager", { id: planmanager_id }).then(
            (result) => {
                planmanager = result.result;
                // Make a copy of the object
                stored_planmanager = Object.assign({}, planmanager);
            },
        );
    }

    function undo() {
        planmanager = Object.assign({}, stored_planmanager);
    }

    function save() {
        jspa("/PlanManager", "updatePlanManager", planmanager).then(
            (result) => {
                // Make a copy of the object
                stored_planmanager = Object.assign({}, planmanager);
            },
        );
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: false,
                show: !(
                    JSON.stringify(planmanager) ===
                    JSON.stringify(stored_planmanager)
                ),
                undo: () => undo(),
                save: () => save(),
            });
        }
    }
</script>

{#if planmanager.archived == 1}
    <div
        class="bg-red-100 rounded-lg py-5 px-6 mb-4 text-base text-red-700 mb-3 flex justify-between"
        role="alert"
    >
        {planmanager.name} has been archived.
        <Role roles={["admin"]}>
            <button
                on:click={() => restorePlanManager()}
                type="button"
                class="inline-block px-6 py-2.5 bg-red-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg transition duration-150 ease-in-out"
                >Restore
            </button>
        </Role>
    </div>
{/if}

<h2 class="text-xl sm:text-2xl font-fredoka-one-regular" style="color:#220055;">
    {planmanager.name}
</h2>

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

<Container>
    <div class=" text-sm leading-6 text-gray-900 font-semibold">
        Managed Participants
    </div>
    {#each clients as client}
        <div
            class="flex justify-between py-2 px-5 text-gray-700 hover:text-indigo-600 hover:bg-gray-50 cursor-pointer"
        >
            <a href="/#/clients/{client.id}">{client.name}</a>
        </div>
    {/each}
</Container>

<Role roles={["admin"]}>
    <div class="flex justify-between">
        <div>
            {#if planmanager.archived != 1}
                <button
                    on:click={() => archivePlanManager()}
                    type="button"
                    class="inline-block px-6 py-2 border-2 border-gray-200 text-gray-300 font-medium text-xs leading-tight uppercase rounded hover:bg-black hover:bg-opacity-5 focus:outline-none focus:ring-0 transition duration-150 ease-in-out"
                    >Archive
                </button>
            {/if}
        </div>
    </div>
</Role>
