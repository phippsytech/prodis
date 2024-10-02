<script>
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { jspa } from "@shared/jspa.js";
    import ComplaintForm from "./ComplaintForm.svelte";
    import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
    
    let complaint = {};

    complaint.status = "open";

    BreadcrumbStore.set({ path: [{ url: "/registers", name: "Registers" }] });

    function addfeedback() {
        jspa("/Register/Complaint", "addComplaint", complaint)
            .then((result) => {
                let feedback_id = result.result.id;
                push("/registers/complaints/" + feedback_id);
            })
            .catch(() => {});
    }
</script>

<div
    class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
    style="color:#220055;"
>
    Add Complaint
</div>

<ComplaintForm bind:complaint />

<div class="flex justify-between">
    <span></span>
    <Button on:click={() => addfeedback()} label="Add complaint" />
</div>
