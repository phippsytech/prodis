<script>
    import Container from "@shared/Container.svelte";
    import { push } from "svelte-spa-router";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa";
    import { formatDate } from "@shared/utilities.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    let expiredList = [];

    BreadcrumbStore.set({
        path: [{ url: null, name: "Credentials" }],
    });

    jspa("/Staff/Credential", "listExpiredCredentials", {}).then((result) => {
        expiredList = result.result;

        // credentials.sort(function(a, b) {
        //     const nameA = a.name.toUpperCase(); // ignore upper and lowercase
        //     const nameB = b.name.toUpperCase(); // ignore upper and lowercase
        //     if (nameA < nameB) return -1;
        //     if (nameA > nameB) return 1;
        //     return 0; // names must be equal
        // });
    });
</script>

<div class="mb-2">
    <div
        class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
        style="color:#220055;"
    >
        Expired Credentials
    </div>
    <p class=" text-sm text-gray-700">
        Supplied credentials that have expired.
    </p>
</div>

<table class="min-w-full divide-y divide-gray-300">
    <thead class=" text-xs">
        <tr>
            <th class="p-2 text-left font-medium">Staff Name</th>
            <th class="p-2 text-left font-medium">Credential</th>
            <th class="p-2 text-left font-medium">Date Expired</th>
            <!-- <th class="p-2 text-right  font-medium" >Action</th> -->
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white">
    {#each expiredList as expired, index (expired.credential_id)}
        <tr
            in:slide={{ duration: 200 }}
            out:slide={{ duration: 200 }}
            on:click={() =>
                push("/staff/" + expired.staff_id + "/credentials")}
            class="px-6 py-2 hover:bg-indigo-600 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 cursor-pointer {expiredList.length -
                1 ==
            index
                ? 'rounded-b-lg'
                : ''}border-b border-gray-200 w-full {expired.archived == 1
                ? 'text-gray-400 '
                : 'text-gray-900 '}"
        >
            <td class=" flex items-center pr-2 pl-2">{expired.name}</td>
            <td class="whitespace-nowrap py-2 text-gray-900 text-left pl-0"
                >{expired.credential}</td
            >
            <td class="whitespace-nowrap py-2 text-gray-900 text-left pl-0"
                >{formatDate(expired.expiry_date)}</td
            >
            <!-- <td class="whitespace-nowrap px-3 py-2 text-sm text-right">action</td> -->
        </tr>
    {:else}
        <tr>
            <td colspan="3">
                <div
                    class="relative flex justify-center items-center space-x-3 rounded-lg bg-white px-10 py-3 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:bg-indigo-600 hover:text-white group"
                >
                    <svg
                        class="animate-spin flex-shrink-0 h-5 w-5 text-indigo-600"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                </div>
            </td>
        </tr>
    {/each}
    </tbody>
</table>
