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
  let participant_id = client_id;

  let documents = [];
  let document = {};

  let required_documents = [];
  let optional_documents = [];

  let client = {};

  onMount(async () => {
    client = await getClient(client_id);

    jspa("/Participant/Document", "listDocumentsByParticipantId", {
      client_id: client_id,
    }).then((result) => {
      documents = result.result;

      required_documents = documents.filter((document) => {
        return document.required == true;
      });

      optional_documents = documents.filter((document) => {
        return document.required == false;
      });
    });

    BreadcrumbStore.set({
      path: [
        { url: "/staff", name: "Staff" },
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
<p class="mb-4">
  Green documents are current. Red documents require attention.
</p>

{#if documents.length > 0}
  <!-- <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"> -->
  <Container>
    <div class="font-medium mb-2">Required documents</div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 md:grid-cols-3">
      {#each required_documents as document, index (index)}
        <div
          animate:flip={{ duration: 350 }}
          in:slide|global={{ duration: 50, delay: 10 * index }}
        >
          <ParticipantDocumentCard
            {document}
            {client_id}
            required={document.required}
          />
        </div>
      {/each}
    </div>
  </Container>

  <Container>
    <div class="font-medium mb-2">Optional documents</div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 md:grid-cols-3">
      {#each optional_documents as document, index (index)}
        <div
          animate:flip={{ duration: 350 }}
          in:slide|global={{ duration: 50, delay: 10 * index }}
        >
          <ParticipantDocumentCard
            {document}
            {client_id}
            required={document.required}
          />
        </div>
      {/each}
    </div>
  </Container>
{:else}
  <div class="text-gray p-4 bg-white rounded border border-gray-300">
    No documents found. The most likely cause is you have not added this staff
    member to a staff group.
  </div>
{/if}
