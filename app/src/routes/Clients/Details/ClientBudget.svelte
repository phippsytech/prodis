<script>
    import Container from "@shared/Container.svelte";
    import { jspa } from "@shared/jspa.js";

    export let client = {};

    let plan = {};

    jspa("/Client/ServiceAgreement", "getserviceagreement", {
        client_id: client.id,
    })
        .then((result) => {
            plan = result.result;
        })
        .catch((error) => {});
</script>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Client Budget
</h1>

<div class="p-3 rounded-lg bg-white w-full mb-1">
    <div class="flex justify-between mb-2">
        <div class="text-sm"><span class="font-bold">Michael Phipps</span></div>
        <p class="text-xs font-bold">40 minutes over</p>
    </div>

    <div class="grid grid-cols-2 gap-2">
        <div class="text-xs">1 Jun 2022 - 30 May 2023</div>
        <div class="w-full bg-gray-200">
            <div
                class="bg-gray-400 text-xs font-bold text-white text-center p-0.5 leading-none"
                style="width: 25%"
            >
                <span class="drop-shadow-md">25%</span>
            </div>
        </div>

        <div class="text-xs">$1,000 of $12,000</div>
        <div class="w-full bg-gray-300">
            <div
                class="bg-blue-600 text-xs font-bold text-white text-center p-0.5 leading-none"
                style="width: 8.33%"
            >
                <span class="drop-shadow-md">8.33%</span>
            </div>
        </div>
    </div>
</div>
