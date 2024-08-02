<script>
    import { push } from "svelte-spa-router";
    import TimeEntryForm from "./TimeEntryForm.svelte";
    import { jspa } from "@shared/jspa.js";
    import { StafferStore, BreadcrumbStore } from "@shared/stores.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

    let timetracking = {};
    let date = new Date();

    timetracking.staff_id = $StafferStore.id;
    timetracking.session_date = date.toISOString().slice(0, 10);

    BreadcrumbStore.set({
        path: [
            { url: "/billing", name: "Billing" },
            { url: null, name: "Add Entry" },
        ],
    });

    function addTimeTracking() {
        jspa("/TimeTracking", "addTimeTracking", timetracking)
            .then((result) => {
                push("/billables/unbilled");
            })
            .catch(() => {});
    }

    $: {
        let show = false;

        if (
            timetracking.staff_id &&
            timetracking.client_id &&
            timetracking.service_id
        ) {
            show = true;
        }

        ActionBarStore.set({
            can_delete: false,
            show: show,
            save: () => addTimeTracking(),
        });
    }
</script>

<TimeEntryForm bind:timetracking />
