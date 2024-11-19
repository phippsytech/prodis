<script>
  import { push } from "svelte-spa-router";
  import { BreadcrumbStore, UserStore } from "@shared/stores.js";
  import { jspa } from "@shared/jspa.js";
  import ComplaintForm from "./ComplaintForm.svelte";
  import Button from "@shared/PhippsyTech/svelte-ui/Button.svelte";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";

  let complaint = {};

  complaint.status = "open";
  complaint.complaint_type = null;
  complaint.user_id = $UserStore.id;

  const validations = [
    {
      check: () => !complaint.date_complaint,
      message: "Complaint date must be provided.",
    },
    // { check: () => !complaint.complaint_type, message: "Complaint type must be provided." },
    {
      check: () => !complaint.status,
      message: "Complaint status must be provided.",
    },
    // { check: () => !complaint.complainant_client_id || complaint.complainant_client_id.length === 0, message: "Please select a client." },
    {
      check: () => !complaint.complainant_name,
      message: "Complainant's name must be provided.",
    },
    { check: () => !complaint.details, message: "Details must be provided." },
    {
      check: () =>
        !complaint.resolution_date &&
        complaint.date_complaint > complaint.resolution_date,
      message:
        "Resolution date must not be before the complaint date provided.",
    },
    {
      check: () => complaint.resolution_date && !complaint.notified_of_outcome,
      message: "Actions taken must be provided.",
    },
  ];

  BreadcrumbStore.set({
    path: [
      { url: "/registers", name: "Registers" },
      { url: "/registers/complaints/", name: "Complaints" },
    ],
  });

  function validate() {
    for (const { check, message } of validations) {
      if (check()) {
        toastError(message);
        return false;
      }
    }
    return true;
  }

  function addComplaint() {
    if (!validate()) {
      return;
    }
    jspa("/Register/Complaint", "addComplaint", complaint)
      .then((result) => {
        let complaintId = result.result.id;

        toastSuccess("Complaint added successfully.");
        push("/registers/complaints/");
      })
      .catch(() => {});
  }

  $: {
    complaint.status =
      complaint.investigation_result && complaint.recommended_actions
        ? "closed"
        : "open";
    if (complaint.status === "closed" && !complaint.date_actioned) {
      let today = new Date();
      complaint.date_actioned = today.toISOString().split("T")[0];
    }
  }
</script>

<div class="mb-2 mt-2" style="color: rgb(34, 0, 85);">
  <h1 class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    Add Complaint
  </h1>
</div>

<ComplaintForm bind:complaint />

<div class="flex justify-between">
  <span></span>
  <Button on:click={() => addComplaint()} label="Add Complaint" />
</div>
