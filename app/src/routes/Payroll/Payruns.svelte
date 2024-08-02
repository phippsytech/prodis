<script>
    import { onMount } from "svelte";
    import PayrunsTable from "./PayrunsTable.svelte";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import Container from "@shared/Container.svelte";
    import Role from "@shared/Role.svelte";
    import { SpinnerStore } from "@app/Overlays/stores.js";

    BreadcrumbStore.set({
        path: [
            { url: "/payroll", name: "Payroll" },
            { url: null, name: "Payruns" },
        ],
    });

    let payruns = [];

    let ready = false;

    function getPayruns() {
        SpinnerStore.set({ show: true, message: "Loading Payroll Data" });
        jspa("/Payroll/Payrun", "getPayruns", {})
            .then((result) => {
                payruns = result.result;
            })
            .catch((error) => {})
            .finally(() => {
                SpinnerStore.set({ show: false });
                ready = true;
            });
    }

    onMount(() => {
        getPayruns();
    });

    function createPayrun() {
        SpinnerStore.set({ show: true, message: "Creating Draft Pay Run" });
        jspa("/Payroll/Payrun", "createPayrun", {})
            .then((result) => {
                getPayruns();
            })
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    }

    let draft_payruns = [];
    $: {
        draft_payruns = payruns.filter(
            (payrun) => payrun.PayRunStatus === "DRAFT",
        );
    }

    let posted_payruns = [];
    $: {
        posted_payruns = payruns.filter(
            (payrun) => payrun.PayRunStatus === "POSTED",
        );
    }
</script>

{#if ready}
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1
                    class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular"
                >
                    Pay Runs
                </h1>
                <!-- <p class="mt-2 text-sm text-gray-700">A list of all the users in your account including their name, title, email and role.</p> -->
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none"></div>
        </div>

        <h1 class="mt-8 text-base font-semibold leading-6 text-slate-900">
            Draft Payrun
        </h1>
        <div class=" border bg-white rounded-lg px-4 py-2">
            <div class=" flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div
                        class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8"
                    >
                        <PayrunsTable payruns={draft_payruns} />
                    </div>
                </div>
            </div>

            {#if draft_payruns.length === 0}
                <Role roles={["sysadmin"]}>
                    <button
                        on:click={() => createPayrun()}
                        type="button"
                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >Add Pay Run</button
                    >
                </Role>
            {/if}
        </div>

        <h1 class="mt-8 text-base font-semibold leading-6 text-gray-900">
            Pay run history
        </h1>
        <div class=" flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div
                    class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8"
                >
                    <PayrunsTable payruns={posted_payruns} />
                </div>
            </div>
        </div>
    </div>
{/if}
