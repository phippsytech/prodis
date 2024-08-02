<script>
    import ServiceForm from "./ServiceForm.svelte";
    import { push } from "svelte-spa-router";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { jspa } from "@shared/jspa.js";

    let service = {};

    function save() {
        jspa("/Service", "addService", service).then((result) => {
            push("/services");
        });
    }

    $: {
        let show = false;
        if (
            service.billing_code != "Select NDIS Support Item" &&
            service.location != "Choose Location" &&
            service.billing_unit != "Choose billing unit"
        ) {
            show = true;
        }
        ActionBarStore.set({
            can_delete: false,
            show: show,
            save: () => save(),
        });
    }
</script>

<ServiceForm bind:service />
