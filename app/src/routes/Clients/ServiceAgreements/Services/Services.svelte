<script>
    import ServiceForm from "./ServiceForm.svelte";
    import ServiceItem from "./NewServiceItem.svelte";
    import { PlusIcon } from "heroicons-svelte/24/outline";
    import Role from "@shared/Role.svelte";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";
    import { haveCommonElements } from "@shared/utilities.js";
    import { convertFieldsToBoolean } from "@shared/utilities/convertFieldsToBoolean";
    import { ModalStore } from "@app/Overlays/stores";
    import { onMount } from "svelte";
    import { RolesStore } from "@shared/stores.js";
    import { toastError, toastSuccess } from "@shared/toastHelper";
    import createStore from "@shared/createStore";

    export let service_agreement;

    const client_id = service_agreement.client_id;
    const participant_id = client_id; // progressively renaming client_id to participant_id

    $: roles = $RolesStore;

    let participant_service = {
        client_id: client_id,
        service_agreement_id: service_agreement.id,
        participant_id: participant_id,
    };

    let ParticipantServiceStore = createStore(
        "/Participant/Service",
        {
            list: "listServicesByServiceAgreementId",
            add: "addService",
            update: "updateService",
            delete: "deleteService",
        },
        {
            // load: async (result) => {
            // return result.map((item) =>
            //     convertFieldsToBoolean(item, ["include_travel"]),
            // );
            // },
            add: async (result) => {
                result = await getParticipantService(result.id);
                return result;
            },
            update: async (result) => {
                result.plan_manager_name = await getPlanManagerName(
                    result.plan_manager_id,
                );
                return result;
            },
        },
    );

    function getParticipantService(participant_service_id) {
        return jspa("/Participant/Service", "getParticipantService", {
            id: participant_service_id,
        }).then((result) => {
            // let participant_service = convertFieldsToBoolean(result.result, [
            //     "include_travel",
            // ]);
            let participant_service = result.result;
            return participant_service;
        });
    }

    function getPlanManagerName(plan_manager_id) {
        return jspa("/PlanManager", "getPlanManager", {
            id: plan_manager_id,
        }).then((result) => {
            let plan_manager = result.result;
            return plan_manager.name;
        });
    }

    onMount(() => {
        ParticipantServiceStore.load({
            service_agreement_id: service_agreement.id,
        });
    });

    function editClientPlanService(participant_service) {
        if (haveCommonElements(roles, ["serviceagreement.modify"])) {
            editService(participant_service);
        }
    }

    function validateField(field, errorMessage) {
        if (field == null || field == "") {
            return null;
        } else if (isNaN(parseFloat(field))) {
            toastError(errorMessage);
            return false;
        }
        return field;
    }

    function validateParticipantService() {
        participant_service.budget = validateField(
            participant_service.budget,
            "Please enter a valid number for the budget.",
        );
        if (participant_service.budget === false) return false;

        participant_service.kilometer_budget = validateField(
            participant_service.kilometer_budget,
            "Please enter a valid number for the kilometer budget.",
        );
        if (participant_service.kilometer_budget === false) return false;

        participant_service.max_claimable_travel_duration = validateField(
            participant_service.max_claimable_travel_duration,
            "Please enter a valid number for the maximum claimable travel duration.",
        );
        if (participant_service.max_claimable_travel_duration === false)
            return false;
    }

    function showService() {
        let participant_service = {
            plan_id: service_agreement.id,
            client_id: client_id,
            participant_id: participant_id,
        };

        ModalStore.set({
            label: "Add Service",
            show: true,
            props: participant_service,
            component: ServiceForm,
            action_label: "Add",
            action: () => addService(participant_service),
        });
    }

    function editService(participant_service) {
        ModalStore.set({
            label: "Update Service",
            show: true,
            props: participant_service,
            component: ServiceForm,
            action_label: "Update",
            action: () => updateService(participant_service),
            delete: () => deleteService(participant_service),
        });
    }

    function addService(participant_service) {
        if (validateParticipantService() == false) {
            return;
        }
        ParticipantServiceStore.add(participant_service);
    }

    function updateService(participant_service) {
        if (validateParticipantService() == false) {
            return;
        }
        ParticipantServiceStore.updateItem(participant_service);
        // .then(() => {
        //     ParticipantServiceStore.plan
        //     getPlanManagerName(participant_service.plan_manager_id);
        //     // toastSuccess("Service updated successfully.");
        // });
    }

    function deleteService(participant_service) {
        ParticipantServiceStore.remove(participant_service);
    }
</script>

{#each $ParticipantServiceStore as participant_service, index}
    <li
        in:slide={{ duration: 200 }}
        out:slide={{ duration: 200 }}
        class="px-3 py-2 w-full rounded-t-lg {$ParticipantServiceStore.length -
            1 ==
        index
            ? ''
            : 'border-b border-slate-200'}
            
            {service_agreement.is_active
            ? 'hover:bg-indigo-50/50'
            : 'opacity-25'}
            "
    >
        <Role roles={["serviceagreement.modify"]} conditional={true}>
            <div slot="authorised">
                {#if service_agreement.is_active}
                    <button
                        class="w-full text-left text-slate-400 cursor-pointer text-sm"
                        on:click={() =>
                            editClientPlanService(participant_service)}
                    >
                        <ServiceItem
                            {service_agreement}
                            service={participant_service}
                        />
                    </button>
                {:else}
                    <ServiceItem
                        {service_agreement}
                        service={participant_service}
                    />
                {/if}
            </div>
            <div slot="declined">
                <ServiceItem
                    {service_agreement}
                    service={participant_service}
                />
            </div>
        </Role>
    </li>
{:else}
    {#if service_agreement.is_active}
        <Role roles={["serviceagreement.modify"]}>
            <li
                class="px-3 py-2 border-t border-indigo-100 w-full text-slate-400 cursor-default text-sm"
            >
                No services have been added to this agreement. &nbsp;<button
                    on:click={() => showService()}
                    class="text-indigo-600 cursor-pointer hover:underline"
                    >Add a service</button
                >
            </li>
        </Role>
    {/if}
{/each}

{#if $ParticipantServiceStore.length > 0}
    {#if service_agreement.is_active}
        <Role roles={["serviceagreement.modify"]}>
            <li
                class="px-3 py-2 border-t border-indigo-100 w-full text-slate-400 cursor-default text-sm"
            >
                <button
                    on:click={() => showService()}
                    class="text-indigo-600 cursor-pointer hover:underline"
                    >Add another service</button
                >
            </li>
        </Role>
    {/if}
{/if}
