<script>
    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";
    import { formatDate } from "@shared/utilities.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { ModalStore } from "@app/Overlays/stores.js";
    import Document from "./Document.svelte";

    import { jspa } from "@shared/jspa.js";
    import { getClient } from "@shared/api.js";
    import { slide } from "svelte/transition";
    import { flip } from "svelte/animate";

    import ParticipantDocumentCard from "./ParticipantDocumentCard.svelte";

    export let params;
    let client_id = params.client_id;

    let documents = [];
    let required_documents = [];
    let optional_documents = [];

    let client = {};

    onMount(async () => {
        client = await getClient(client_id);

        jspa("/Participant/Document", "listDocumentsByParticipantId", {
            participant_id: parseInt(client_id),
        }).then((result) => {
            documents = result.result;

            required_documents = documents.filter((document) => {
                return document.is_required == true;
            });

            optional_documents = documents.filter((document) => {
                return document.is_required == false;
            });
        });

        BreadcrumbStore.set({
            path: [
                { url: "/client", name: "Client" },
                { url: null, name: client.name },
            ],
        });
    });
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
    style="color:#220055;"
>
    Documents for {client.name}
</div>
<!-- <p class="mb-4">
    
</p> -->

{#if documents.length > 0}
    <!-- <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"> -->
    <Container>
        <div class="font-medium mb-2">Required Documents</div>
        <div
            class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 md:grid-cols-3"
        >
            {#each required_documents as document, index (index)}
                <div
                    animate:flip={{ duration: 350 }}
                    in:slide|global={{ duration: 50, delay: 10 * index }}
                >
                    <ParticipantDocumentCard
                        {document}
                        bind:participant_id={client_id}
                        required={document.required}
                    />
                </div>
            {/each}
        </div>
    </Container>

    <Container>
        <div class="font-medium mb-2">Optional Documents</div>
        <div
            class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 md:grid-cols-3"
        >
            {#each optional_documents as document, index (index)}
                <div
                    animate:flip={{ duration: 350 }}
                    in:slide|global={{ duration: 50, delay: 10 * index }}
                >
                    <ParticipantDocumentCard
                        {document}
                        bind:participant_id={client_id}
                        required={document.required}
                    />
                </div>
            {/each}
        </div>
    </Container>
{:else}
    <div class="text-gray p-4 bg-white rounded border border-gray-300">
        No documents found.
    </div>
{/if}
