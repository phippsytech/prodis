<script>
    import { jspa } from "@shared/jspa.js";
    import { onMount } from "svelte";
    import Router from "svelte-spa-router";
    import routes from "./routes";
    import Guard from "@shared/appsec/Guard.svelte";
    import Roadblock from "@shared/appsec/Roadblock.svelte";
    import Role from "@shared/Role.svelte";
    import SignIn from "./routes/SignIn.svelte";
    // import {checkWelcomeRoadblock} from '@shared/roadblocks/Welcome.svelte'

    import {
        RolesStore,
        StafferStore,
        AppData,
        API_URL,
    } from "@shared/stores.js";

    // let roadblockChecks=[checkWelcomeRoadblock];
    let roadblockChecks = [];

    // Interrupting users with a roadblock is this easy.
    // setInterval(() => {
    //     AppData.update((currentData)=>{
    //         let newData = JSON.parse(currentData);
    //         newData['welcomed'] = false;
    //         return JSON.stringify(newData);
    //     });
    // }, 10000);

    let ready = false;

    function getUser() {
        jspa("/User", "getRoles", {})
            .then((result) => {
                let roles = result.result;
                RolesStore.update((currentData) => {
                    let newData = currentData;
                    newData = roles;
                    return newData;
                });

                jspa("/Staff", "getMyStaffId", {})
                    .then((result) => {
                        StafferStore.update((currentData) => {
                            let newData = currentData;
                            newData = result.result;
                            return newData;
                        });
                    })
                    .catch((error) => console.log(error))
                    .finally(() => (ready = true));
            })
            .catch((error) => console.log(error));
    }

    // Use the onMount lifecycle method to remove the spinner when the app is ready
    onMount(() => {
        const spinner = document.getElementById("spinner");
        // console.log(spinner);
        if (spinner) spinner.parentNode.removeChild(spinner);
    });
</script>

<SignIn />
<Guard
    authentication_server={$API_URL + "/appsec"}
    on:authenticated|once={() => getUser()}
>
    {#if ready === true}
        <Roadblock {roadblockChecks}>
            <Router {routes} restoreScrollState={true} />
        </Roadblock>
    {/if}
</Guard>
