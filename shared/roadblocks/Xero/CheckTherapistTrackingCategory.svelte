<script context="module">
    import CheckTherapistTrackingCategory from "./CheckTherapistTrackingCategory.svelte";
    // import XeroPanel from '../../routes/Settings/Xero.svelte';
    import { jspa } from "../../jspa.js";

    let xeroTherapistTrackingCategory = true;

    let missing_therapists = [];

    export function checkXeroTherapistTrackingCategoryRoadblock() {
        return new Promise(function (resolve, reject) {
            jspa("/Xero", "getTherapistTrackingCategories", {})
                .then((result) => {
                    resolve(true);
                })
                .catch((error) => {
                    missing_therapists = error.missing_therapists;
                    reject(CheckTherapistTrackingCategory);
                });
        });
    }
</script>

<div class="flex items-center justify-center p-4" style="height:90vh">
    <div class="w-full" style="max-width:400px;">
        <div class="text-3xl mb-2">Missing Therapists</div>
        <p class="mb-4">
            Your Xero account does not have all your therapists. Please go to
            the <a
                class="underline text-blue-500"
                href="https://go.xero.com/Setup/Tracking.aspx"
                target="_blank">Tracking Categories</a
            > page in your Xero account and add the following therapists as options
            in the Therapist tracking category, then reload this page.
        </p>

        <ul
            class="bg-white rounded-lg border border-gray-200 w-full text-gray-900"
        >
            {#each missing_therapists as therapist, index}
                <li
                    class="px-4 py-2 focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 {missing_therapists.length -
                        1 ==
                    index
                        ? 'rounded-b-lg'
                        : ''}border-b border-gray-200 w-full"
                >
                    <div class="font-bold">{therapist}</div>
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
