<script>
    import { push } from "svelte-spa-router";

    import AuthyClass from "@shared/appsec/Authy.class.js";

    async function logout() {
        try {
            let authy = new AuthyClass();
            await authy.deauthenticate();
        } catch (error) {
            console.error(error);
        }
    }

    logout();
</script>
