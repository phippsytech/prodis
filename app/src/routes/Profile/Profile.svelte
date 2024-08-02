<script>
    import { onMount } from "svelte";

    import { UserStore, BreadcrumbStore } from "@shared/stores.js";

    import { jspa } from "@shared/jspa.js";
    // import Credentials from './Credentials/Credentials.svelte';
    import UserComponent from "@shared/UserComponent.svelte";

    import Container from "@shared/Container.svelte";

    import {} from "@shared/stores.js";

    $: userStore = $UserStore;

    // let staff_id = params.staff_id

    // export let staff_id = null
    let userComponent;
    let user = {};

    let loaded = false;

    onMount(async () => {
        if (userStore.id) {
            BreadcrumbStore.set({
                path: [{ name: userStore.name, url: null }],
            });
        }
    });

    function handleLoaded(event) {
        user = event.detail.user;
        // loaded = true;
    }

    function handleChanged(event) {
        user = event.detail.user;
        // loaded = true;
    }
</script>

<!-- <div class="mx-auto  px-4 sm:px-6 lg:px-8 mt-8">
    
    
    </div>
     -->

<div class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular">
    {$UserStore.name}
</div>

{#if userStore.id}
    <UserComponent
        bind:this={userComponent}
        bind:user_id={userStore.id}
        on:loaded={(event) => handleLoaded(event)}
        on:changed={(event) => handleChanged(event)}
    />
{/if}
