<script>
    import { replace } from "@app/../node_modules/svelte-spa-router";

    import AuthyClass from "@shared/appsec/Authy.class.js";

    export let params;

    let registrationToken = params.token;

    let authy = new AuthyClass();

    async () => {
        await authy.hasCredentials(); // get / create the key pair
        await authy.register(registrationToken); // send the public key to the server
        replace("/"); // redirect to the home page after registration which should do authentication
    };
</script>
