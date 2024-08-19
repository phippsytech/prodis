<script>
    import Router from "svelte-spa-router";
    import routes from "./routes";
    import Guard from "@shared/appsec/Guard.svelte";
    import Offline from "./Layout/Offline.svelte";
    import NavBar from "./Layout/NavBar/NavBar.svelte";
    import Tabs from "./Layout/Tabs/Tabs.svelte";
    import Modal from "./Overlays/Modal.svelte";
    import Spinner from "./Overlays/Spinner.svelte";
    import SlideOver from "./Overlays/SlideOver.svelte";
    import ContextMenu from "./Overlays/ContextMenu.svelte";
    import Tooltip from "./Overlays/Tooltip.svelte";
    import BottomNav from "./Layout/BottomNav/BottomNav.svelte";
    import ActionBar from "./Layout/BottomNav/ActionBar.svelte";
    import Websocket from "./Websocket.svelte";
    import { SvelteToast } from "@zerodevx/svelte-toast";
    import { jspa } from "@shared/jspa.js";

    import { onMount } from "svelte";
    import { UserStore, RolesStore } from "@shared/stores.js";

    let ready = false;

    function getUser() {
        jspa("/User", "getUser", {})
            .then((result) => {
                let roles = result.result.roles;
                RolesStore.update((currentData) => {
                    let newData = currentData;
                    newData = roles;
                    return newData;
                });

                UserStore.update((currentData) => {
                    let newData = currentData;
                    newData = result.result;
                    return newData;
                });
            })
            .catch((error) => {})
            .finally(() => (ready = true));
    }

    // Use the onMount lifecycle method to remove the spinner when the app is ready
    onMount(() => {
        const spinner = document.getElementById("spinner");
        if (spinner) spinner.parentNode.removeChild(spinner);
    });
</script>

<Offline />

<Guard on:authenticated|once={() => getUser()}>
    {#if ready === true}
        <NavBar />
        <Tabs />
        <main class="mx-auto max-w-7xl m-0 p-0">
            <div class=" backdrop-blur px-4 py-4">
                <Router {routes} restoreScrollState={true} />
            </div>
        </main>
    {/if}
</Guard>

<ActionBar />
<BottomNav />
<Modal />
<SlideOver />
<ContextMenu />
<Tooltip />
<Spinner />

<SvelteToast
    options={{
        duration: 4000, // duration of progress bar tween to the `next` value
        initial: 1, // initial progress bar value
        next: 0, // next progress value
        pausable: false, // pause progress bar tween on mouse hover
        dismissable: true, // allow dismiss with close button
        reversed: false, // insert new toast to bottom of stack
        intro: { y: 192 }, // toast intro fly animation settings
        theme: {}, // css var overrides
        classes: [], // user-defined classes
    }}
/>
<Websocket />

<style>
    /*
    :root {
        --toastContainerTop: auto;
        --toastContainerRight: auto;
        --toastContainerBottom: 6rem;
        --toastContainerLeft: calc(50vw - 8rem);
    }
*/

    :root {
        --toastContainerTop: calc(
            50vh - 4rem
        ); /* Adjust top position as needed */
        --toastContainerRight: auto;
        --toastContainerBottom: auto;
        --toastContainerLeft: calc(
            50vw - 8rem
        ); /* Adjust left position as needed */
    }
</style>
