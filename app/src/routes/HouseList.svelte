<script>
    import { onMount } from "svelte";
    import { push, replace } from "svelte-spa-router";
    import ReportPanel from "@shared/ReportPanel.svelte";
    import { jspa } from "@shared/jspa.js";
    import { haveCommonElements } from "@shared/utilities.js";
    import {
        HouseStore,
        StafferStore,
        RolesStore,
        AppData,
    } from "@shared/stores.js";
    import Role from "@shared/Role.svelte";

    $: stafferStore = $StafferStore;
    $: rolesStore = $RolesStore;

    let houses = [];

    onMount(() => {
        if (rolesStore && haveCommonElements(rolesStore, ["admin"])) {
            // are ANY of the elements "house"?
            jspa("/SIL/House", "listHouses", {}).then((result) => {
                houses = result.result;
            });
        } else {
            jspa("/SIL/House", "listHousesByStaffId", {
                staff_id: stafferStore.id,
            }).then((result) => {
                houses = result.result;
            });
        }
    });

    function selectHouse(house_id) {
        let selectedHouse = houses.find((house) => house.id === house_id);
        if (selectedHouse) {
            selectAdmin(false); // turn off admin mode
            HouseStore.update((currentData) => {
                let newData = currentData;
                newData = selectedHouse;
                return newData;
            });
        }
    }

    function selectAdmin(value) {
        AppData.update((currentData) => {
            let newData = JSON.parse(currentData);
            newData["admin"] = value;
            return JSON.stringify(newData);
        });
    }
</script>

<div class="flex justify-center p-4">
    <div class="w-full" style="max-width:400px;">
        <p class="text-gray-700 uppercase mb-0">Crystel Care</p>
        <div class="text-3xl mb-4">Select a House</div>
        <hr class="mb-4" />
        {#each houses as house}
            <ReportPanel
                on:click={() => {
                    selectHouse(house.id);
                }}
                label={house.name + " House"}
            />
        {:else}
            No houses found
        {/each}
        <Role roles={["admin"]}>
            <hr class="my-4" />

            <div class="bg-slate-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div
                            class="text-base font-semibold leading-6 text-white"
                        >
                            Admin Area
                        </div>
                        <button
                            on:click={() => {
                                selectAdmin(true);
                            }}
                            type="button"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                            >Go</button
                        >
                    </div>
                </div>
            </div>
        </Role>
    </div>
</div>
