<script>
    import { jspa } from "./jspa.js";
    import { RolesStore, StafferStore } from "./stores.js";

    import { haveCommonElements } from "./utilities.js";

    export let roles;

    export let client_id;

    async function checkAccess() {
        return new Promise(function (resolve, reject) {
            if (haveCommonElements($RolesStore, roles)) {
                return resolve(true);
            }

            jspa("/Client/Staff", "checkAccess", {
                client_id: client_id,
                staff_id: $StafferStore.id,
            })
                .then((result) => {
                    if (result.result) {
                        return resolve(true);
                    } else {
                        return reject(false);
                    }
                })
                .catch(() => {
                    return reject(false);
                });
        });
    }
</script>

{#await checkAccess()}
    <div
        class="flex justify-center items-center h-full text-center text-gray-400"
    >
        <svg
            class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600"
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
            />
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            />
        </svg>
        checking permission
    </div>
{:then}
    <slot />
{:catch}
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg
                    class="h-5 w-5 text-red-400"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    You don't have permission to access this participant.
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <!-- <ul role="list" class="list-disc space-y-1 pl-5">
                <li>Your password must be at least 8 characters</li>
                <li>Your password must include at least one pro wrestling finishing move</li>
              </ul> -->
                </div>
            </div>
        </div>
    </div>
{/await}
