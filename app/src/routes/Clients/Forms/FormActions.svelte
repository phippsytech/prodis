<script>
    import { onMount, onDestroy } from "svelte";
    import { push } from "svelte-spa-router";
    import { ClientStore, RolesStore, StafferStore } from "@shared/stores.js";
    import { haveCommonElements } from "@shared/utilities.js";
    import { jspa } from "@shared/jspa.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";

    export let form = {};
    export let valid;

    $: stafferStore = $StafferStore;

    let stored_form = Object.assign({}, form);
    let client = {};
    let canDelete = false;

    // Stores
    $: rolesStore = $RolesStore;
    const unsubscribe = ClientStore.subscribe((value) => {
        client = value;
    });
    onDestroy(unsubscribe);

    onMount(async () => {
        if (
            rolesStore &&
            haveCommonElements(rolesStore, ["sil.admin"]) &&
            form._id
        ) {
            canDelete = true;
        }
    });

    function save() {
        if (form._id) {
            const reason = prompt("Please enter the reason for your edit:");

            if (reason) {
                // User clicked OK and entered a reason
                const edit = {
                    reason: reason,
                    timestamp: new Date().toISOString(),
                    staff_id: stafferStore.id,
                };

                if (!form.edit_history) {
                    form.edit_history = [];
                }
                form.edit_history.push(edit);
            } else {
                // User clicked Cancel
                return;
            }
        }

        SpinnerStore.set({ show: true, message: "Saving Form" });
        jspa("/SIL/House/Timeline", "saveTimeline", form)
            .then((result) => {
                stored_form = Object.assign({}, form);
                push("/clients/" + client.id + "/timeline");
            })
            .catch((error) => {})
            .finally(() => {
                SpinnerStore.set({ show: false });
            });
    }

    function del() {
        jspa("/SIL/House/Form", "deleteForm", form)
            .then((result) => push("/clients/" + client.id + "/timeline"))
            .catch((error) => {});
    }

    function undo() {
        let date = form.date;
        let time = form.time;
        form = Object.assign({}, stored_form);
        form.date = date;
        form.time = time;
    }

    $: {
        ActionBarStore.set({
            can_delete: canDelete,
            valid: valid,
            show:
                !(JSON.stringify(form) === JSON.stringify(stored_form)) ||
                valid == false,
            undo: () => undo(),
            save: () => save(),
            delete: () => del(),
        });
    }
</script>
