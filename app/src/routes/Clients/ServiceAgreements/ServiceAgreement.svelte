<script>
    import { DocumentTextIcon } from "heroicons-svelte/24/outline";
    import { formatDate } from "@shared/utilities.js";
    import { isDate } from "@shared/validators";
    import { ModalStore } from "@app/Overlays/stores";
    import { toastError, toastSuccess } from "@shared/toastHelper";
    import Role from "@shared/Role.svelte";
    import ServiceAgreementForm from "./ServiceAgreementForm.svelte";
    import Services from "./Services/Services.svelte";
    import { push } from "svelte-spa-router";

    export let service_agreement;
    export let ServiceAgreementStore;

    function editServiceAgreement(service_agreement) {
        ModalStore.set({
            label: "Update Service Agreement",
            show: true,
            props: service_agreement,
            component: ServiceAgreementForm,
            action_label: "Update",
            action: () => updateServiceAgreement(service_agreement),
            // delete: () => deleteServiceAgreement(service_agreement),
        });
    }

    function updateServiceAgreement(service_agreement) {
        if (
            !service_agreement.service_agreement_signed_date ||
            !isDate(service_agreement.service_agreement_signed_date)
        ) {
            toastError("Signed Date is not a valid date");
            return;
        }

        if (
            !service_agreement.service_agreement_end_date ||
            !isDate(service_agreement.service_agreement_end_date)
        ) {
            toastError("End Date is not a valid date");
            return;
        }
        ServiceAgreementStore.updateItem(service_agreement);
    }

    function deleteServiceAgreement(service_agreement) {
        ServiceAgreementStore.remove(service_agreement);
    }

    function amendServiceAgreement(service_agreement) {
        push(
            "/clients/" +
                service_agreement.client_id +
                "/serviceagreements/" +
                service_agreement.id +
                "/amend",
        );
    }
</script>

<ul
    class="bg-white border-b border-x rounded-md w-full text-slate-900 mb-2 {service_agreement.is_active
        ? ' border-indigo-600 '
        : '  border-indigo-100 '}"
>
    <li
        class="px-2 py-1 w-full cursor-default text-sm border-t rounded-t-md {service_agreement.is_active
            ? 'bg-indigo-600 text-white  border-indigo-600'
            : 'bg-slate-100 text-slate-400  border-indigo-100'}"
    >
        <div class="flex justify-between items-center">
            <h3 class="flex items-center">
                <DocumentTextIcon class="h-4 w-4 inline mr-2" />
                {formatDate(service_agreement.service_agreement_signed_date)} - {formatDate(
                    service_agreement.service_agreement_end_date,
                )}
                {#if service_agreement.is_active}
                    - ACTIVE{:else}
                    - INACTIVE{/if}
            </h3>

            <Role roles={["serviceagreement.modify"]}>
                <div>
                    <!-- <button
                        on:click={() =>
                            amendServiceAgreement(service_agreement)}
                        class="px-1 hover:rounded-md hover:bg-white text-slate-400 hover:text-indigo-600 cursor-pointer"
                    >
                        Amend
                    </button> -->
                    <button
                        on:click={() => editServiceAgreement(service_agreement)}
                        class="px-1 hover:rounded-md hover:bg-white text-slate-400 hover:text-indigo-600 cursor-pointer"
                    >
                        Edit
                    </button>
                </div>
            </Role>
        </div>
    </li>

    <Services bind:service_agreement />
</ul>
