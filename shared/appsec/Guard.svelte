<script>
    import {
        location,
        querystring,
        replace,
    } from "@app/../node_modules/svelte-spa-router";
    import Authenticate from "./Authenticate.svelte";
    import AuthyClass from "./Authy.class.js";
    import { SpinnerStore } from "@shared/Overlays/stores.js";
    import { createEventDispatcher } from "svelte";
    const dispatch = createEventDispatcher();

    let isAuthenticated;
    let querystring_params = [];

    async function checkAuth() {
        try {
            let authy = new AuthyClass();

            await authy.checkAuthenticated();
            if ($querystring) {
                const searchParams = new URLSearchParams($querystring);
                for (const [k, v] of searchParams) {
                    querystring_params[k] = v;
                }

                if (querystring_params.token) {
                    replace($location);
                }
            }
            dispatch("authenticated");
            isAuthenticated = true;
        } catch (error) {
            console.log(error);
            isAuthenticated = false;
        } finally {
            SpinnerStore.set({ show: false });
        }
    }

    checkAuth();
</script>

{#if isAuthenticated === undefined}
    <!-- This prevents the login from being shown during authentication checks -->
{:else if isAuthenticated}
    <slot></slot>
{:else}
    <Authenticate />
{/if}
