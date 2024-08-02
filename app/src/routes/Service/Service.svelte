<script>
    import ServiceForm from "./ServiceForm.svelte";
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { push } from "svelte-spa-router";

    export let params = {};

    let service_id = params.service_id;
    let service = {};
    let stored_service = {};

    onMount(async () => {
        await jspa("/Service", "getService", { id: service_id }).then(
            (result) => {
                service = result.result;
                stored_service = Object.assign({}, service);
            },
        );
        BreadcrumbStore.set({ path: [{ url: "/services", name: "Services" }] });
    });

    function undo() {
        service = Object.assign({}, stored_service);
    }

    function save() {
        jspa("/Service", "updateService", service).then((result) => {
            stored_service = Object.assign({}, service);
            push("/services");
        });
    }

    $: {
        ActionBarStore.set({
            can_delete: false,
            show: !(JSON.stringify(service) === JSON.stringify(stored_service)),
            undo: () => undo(),
            save: () => save(),
        });
    }
</script>

<ServiceForm bind:service />
