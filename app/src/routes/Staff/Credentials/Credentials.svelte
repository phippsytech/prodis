<script>
    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";
    import { formatDate } from "@shared/utilities.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { ModalStore } from "@app/Overlays/stores.js";
    import Credential from "./Credential.svelte";

    import { jspa } from "@shared/jspa.js";
    import { getStaffer } from "@shared/api.js";
    import { slide } from "svelte/transition";
    import { flip } from "svelte/animate";

    import StaffCredentialCard from "./StaffCredentialCard.svelte";

    export let params;
    let staff_id = params.staff_id;

    let credentials = [];
    let credential = {};

    let required_credentials = [];
    let optional_credentials = [];

    let staffer = {};

    onMount(async () => {
        staffer = await getStaffer(staff_id);

        jspa("/Staff/Credential", "listCredentialsByStaffId", {
            staff_id: staff_id,
        }).then((result) => {
            credentials = result.result;

            required_credentials = credentials.filter((credential) => {
                return credential.required == true;
            });

            optional_credentials = credentials.filter((credential) => {
                return credential.required == false;
            });
        });

        BreadcrumbStore.set({
            path: [
                { url: "/staff", name: "Staff" },
                { url: null, name: staffer.name },
            ],
        });
    });
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
    style="color:#220055;"
>
    Credentials for {staffer.name}
</div>
<p class="mb-4">
    Green credentials are current. Red credentials require attention.
</p>

{#if credentials.length > 0}
    <!-- <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"> -->
    <Container>
        <div class="font-medium mb-2">Required Credentials</div>
        <div
            class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 md:grid-cols-3"
        >
            {#each required_credentials as credential, index (index)}
                <div
                    animate:flip={{ duration: 350 }}
                    in:slide|global={{ duration: 50, delay: 10 * index }}
                >
                    <StaffCredentialCard
                        {credential}
                        {staff_id}
                        required={credential.required}
                    />
                </div>
            {/each}
        </div>
    </Container>

    <Container>
        <div class="font-medium mb-2">Optional Credentials</div>
        <div
            class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 md:grid-cols-3"
        >
            {#each optional_credentials as credential, index (index)}
                <div
                    animate:flip={{ duration: 350 }}
                    in:slide|global={{ duration: 50, delay: 10 * index }}
                >
                    <StaffCredentialCard
                        {credential}
                        {staff_id}
                        required={credential.required}
                    />
                </div>
            {/each}
        </div>
    </Container>
{:else}
    <div class="text-gray p-4 bg-white rounded border border-gray-300">
        No credentials found. The most likely cause is you have not added this
        staff member to a staff group.
    </div>
{/if}
