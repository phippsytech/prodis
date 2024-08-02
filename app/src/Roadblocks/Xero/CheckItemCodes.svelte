<script context="module">
    import CheckItemCodes from "./CheckItemCodes.svelte";

    import { jspa } from "../../jspa.js";
    import { AppData } from "../../stores.js";

    let xeroItemCodes = false;

    let xero_items = [];

    export function checkXeroItemCodesRoadblock() {
        return new Promise(function (resolve, reject) {
            jspa("/Xero", "getItems", {})
                .then((result) => {
                    resolve(true);
                })
                .catch((error) => {
                    xero_items = error.missing_item_codes;
                    reject(CheckItemCodes);
                });
        });
    }
</script>

<div class="flex items-center justify-center p-4" style="height:90vh">
    <div class="w-full" style="max-width:400px;">
        <div class="text-3xl mb-2">Missing Item Codes</div>
        <p class="mb-4">
            Xero does not contain item codes for all your <b>Services</b>.
            Please open the
            <a
                class="underline text-blue-500"
                href="https://go.xero.com/app/products-and-services"
                target="_blank">Products and services</a
            > page in your xero account and add the following item codes and then
            reload this page.
        </p>

        <ul
            class="bg-white rounded-lg border border-gray-200 w-full text-gray-900"
        >
            {#each xero_items as item, index}
                <li
                    class="px-4 py-2 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 {xero_items.length -
                        1 ==
                    index
                        ? 'rounded-b-lg'
                        : ''}border-b border-gray-200 w-full"
                >
                    <div class="font-bold">{item}</div>
                </li>
            {:else}
                <!-- <li class="px-4 py-2 border-b border-gray-200 w-full rounded-t-lg text-gray-400 cursor-default">
            No clients found.
        </li>
     -->
            {/each}
        </ul>
    </div>
</div>
