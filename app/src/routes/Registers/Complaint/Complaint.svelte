<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
    import Complaint from "./Complaint.svelte";
    import ComplaintForm from "./ComplaintForm.svelte";

    export let params;

    let complaint={
        status: "under investigation",
        complainant_client_id: null,
        complainant_contact_details: null,
        department: null,
        complaint_type: null,
        received_by: null,
        details: null,
        outcome_wanted: null,
        is_staff_notified: false,
        notified_staffs_id: null,
        date_actioned: null,
        complainant_notified: false,
        complainant_feedback: null,
        continuous_improvement: null,
        ndis_commission_notified: false,
        ndis_commission_id: null,
        recommended_actions: null,
        archived: false
    };


    let storedComplaint = Object.assign({}, complaint);
    let mounted = false;
    let readOnly = false;

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });

    onMount(() => {
        if (params.id != "add") {
            jspa("/Register/Complaint", "getComplaint", { id: params.id })
                .then((result) => {
                    complaint = result.result;
                })
                .catch(() => {})
                .finally(() => {
                    // Make a copy of the object
                    storedComplaint = Object.assign({}, complaint);
                });
        }
        mounted = true;
    });

    function undo() {
        complaint = Object.assign({}, storedComplaint);
    }

    function save() {
        jspa("/Register/Feedback", "updateFeedback", feedback)
            .then((result) => {
                complaint = result.result;
                storedComplaint = Object.assign({}, feedback);
            })
            .catch(() => {});
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: false,
                show: !(
                    JSON.stringify(complaint) === JSON.stringify(storedComplaint)
                ),
                undo: () => undo(),
                save: () => save(),
            });
        }
    }

    $: {
        readOnly = complaint.status == "closed";
    }
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
    style="color:#220055;"
>
    Add Feedback
</div>

<ComplaintForm bind:complaint bind:readOnly />
