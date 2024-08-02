<script context="module">
    import Xero from "./Xero.svelte";
    import XeroPanel from "@app/routes/Settings/Accounting/Xero.svelte";
    import { jspa } from "@shared/jspa.js";
    import { AppData } from "@shared/stores.js";

    let xeroConnected = false;

    /*
    Must be connected to Xero

    The NDIA contact should be set

    Item Codes should contain the all codes in the services table

    Tracking Categories should contain a category called Therapist
    The Therapist category should contain options containing each therapist's name (as set in staffs table.)

*/

    export function checkXeroRoadblock() {
        return new Promise(function (resolve, reject) {
            jspa("/Xero", "testConnection", {})
                .then((result) => {
                    // xeroConnected = result.result;
                    // if(xeroConnected===true){
                    //     resolve(true);
                    // }else{
                    //     reject(Xero);
                    // }
                    resolve(true);
                })
                .catch((result) => {
                    reject(Xero);
                });
        });
    }
</script>

<div class="flex items-center justify-center p-4" style="height:90vh">
    <div class="w-full" style="max-width:400px;">
        <div class="text-3xl mb-2">You need to connect Xero</div>
        <p class="mb-4">
            Before you can use invoicing, you need to connect your Xero account.
        </p>

        <XeroPanel />
    </div>
</div>
