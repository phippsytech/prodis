<script>
 import {
        location,
        querystring,
        replace,
    } from "@app/../node_modules/svelte-spa-router";

    import AuthyClass from "@shared/appsec/Authy.class.js";

export let params;

let registrationToken = params.token;


    let authy = new AuthyClass();

    (async ()=> {
        
        await authy.hasCredentials(); // get / create the key pair
        await authy.register(registrationToken); // send the public key to the server
        replace('/'); // redirect to the home page after registration which should do authentication
    });
    

/*




Client sends public key to server using the registration token. 


This stores the public key against the user id using the email address to look up the user id


then authenticate

*/


</script>

Hi there!

