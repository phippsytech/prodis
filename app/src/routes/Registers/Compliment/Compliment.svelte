<script>
  import { onMount } from "svelte";
  import { jspa } from "@shared/jspa.js";
  import { push } from "svelte-spa-router";
  import { toastSuccess, toastError } from "@shared/toastHelper.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";
  import Role from "@shared/Role.svelte";
  import ComplimentForm from "./ComplimentForm.svelte";

  export let params;

  let compliment = {};
  let stored_compliment = {};

  let readOnly = false;

  let staffer = [];

  let mounted = false;

  BreadcrumbStore.set({
    path: [
      { url: "/registers", name: "Registers" },
      { url: "/registers/compliments/", name: "Compliments" },
    ],
  });

  onMount(() => {
    if (params.id != "add") {
      // Fetch compliment details from the API
      jspa("/Register/Compliment", "getCompliment", { id: params.id })
        .then((result) => {
          compliment = result.result;
          stored_compliment = Object.assign({}, compliment);
        })
        .finally(() => {})
        .catch(() => {
          toastError("Failed to load compliment.");
        });
    }
    mounted = true;
  });

  $: {
    if (mounted) {
      const complimentChanged =
        JSON.stringify(compliment) !== JSON.stringify(stored_compliment);

      ActionBarStore.set({
        can_delete: true,
        show: true, // display action bar by default
        undo: () => undo(),
        save: () => save(),
        delete: () => deleteCompliment(),
      });
    }
  }

  $: {
    if (compliment.action_taken && compliment.staffs_id) {
      readOnly = true;
    }
  }

  jspa("/Staff", "listStaff", {}).then((result) => {
    staffer = result.result
      .filter((item) => item.archived !== "1")
      .map((item) => ({
        label: `${item.staff_name}`,
        value: item.id,
      }))
      .sort((a, b) => a.label.localeCompare(b.label));
  });

  function undo() {
    compliment = Object.assign({}, stored_compliment);
  }

  const validations = [
    {
      check: () => !compliment.date,
      message: "Date of compliment must be provided.",
    },
    {
      check: () => !compliment.complimenter,
      message: "Complimenter name must be provided.",
    },
    {
      check: () => !compliment.description,
      message: "Description must be provided.",
    },
    {
      check: () => {
        const currentDate = new Date();
        const complimentDate = new Date(compliment.date);
        return complimentDate > currentDate;
      },
      message: "Compliment date cannot be in the future.",
    },
  ];

  function validate() {
    for (const { check, message } of validations) {
      if (check()) {
        toastError(message);
        return false;
      }
    }
    return true;
  }

  function save() {
    if (validate()) {
      if (compliment.action_taken && compliment.staffs_id) {
        compliment.status = "acknowledged";

        const today = new Date();
        compliment.acknowledgement_date = today.toISOString().split("T")[0];
      }

      jspa("/Register/Compliment", "updateCompliment", { ...compliment })
        .then((result) => {
          if (result.error) {
            toastError(result.error);
            return;
          }
          push("/registers/compliments");
          toastSuccess("Compliment updated successfully!");
        })
        .catch(() => {
          toastError("Error updating compliment, please try again.");
        });
    }
  }

  function deleteCompliment() {
    if (confirm("Are you sure you want to delete this compliment?")) {
      jspa("/Register/Compliment", "deleteCompliment", { id: compliment.id })
        .then((result) => {
          toastSuccess("Compliment successfully deleted");
          push("/registers/compliments");
        })
        .catch((error) => {
          toastError("Error deleting compliment");
        });
    }
  }
</script>

<div class="mb-2 mt-2" style="color: rgb(34, 0, 85);">
  <h1 class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular">
    Compliment Details
  </h1>
</div>

<ComplimentForm bind:compliment {staffer} />
