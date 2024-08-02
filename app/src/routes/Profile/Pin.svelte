<script>
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";

  import { jspa } from "@shared/jspa.js";

  import { StafferStore } from "@shared/stores.js";
  import { onMount } from "svelte";

  let pin;
  let pin_confirm;
  let show_success = false;

  $: stafferStore = $StafferStore;

  function toggleShowSuccess() {
    show_success = !show_success;
  }

  function updatePin(client_id) {
    if (pin != pin_confirm) {
      alert("Pins do not match");
      return;
    }

    if (pin.length < 4) {
      alert("Pin must be at least 4 characters");
      return;
    }

    jspa("/Staff", "updatePin", { staff_id: stafferStore.id, pin: pin })
      .then((result) => {
        pin = pin_confirm = null; // clear the pin from the field
        show_success = true;
      })
      .catch((error) => {
        alert("There was an error updating your pin");
      });
  }
</script>

<b>Change Pin</b>
<p class="mb-2 text-sm">
  Your pin is used in shared logins to prevent other staff using your account.
</p>

<FloatingInput
  label="Pin"
  type="password"
  placeholder="Enter your new pin"
  inputmode="numeric"
  bind:value={pin}
/>
<FloatingInput
  label="Pin Again"
  type="password"
  placeholder="Confirm your new pin"
  inputmode="numeric"
  bind:value={pin_confirm}
/>
<p class="text-xs opacity-50 mb-4">Minimum length 4 numbers.</p>

{#if show_success}
  <div class="rounded-md bg-green-50 p-4">
    <div class="flex">
      <div class="flex-shrink-0">
        <svg
          class="h-5 w-5 text-green-400"
          viewBox="0 0 20 20"
          fill="currentColor"
          aria-hidden="true"
        >
          <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
      <div class="ml-3">
        <h3 class="text-sm font-medium text-green-800">Pin Updated</h3>
        <div class="mt-2 text-sm text-green-700">
          <p>
            No other staff members (including admin) should ever ask for or be
            told your pin.
          </p>
        </div>
        <div class="mt-4">
          <div class="-mx-2 -my-1.5 flex">
            <button
              on:click={() => toggleShowSuccess()}
              type="button"
              class="ml-3 rounded-md bg-green-50 px-2 py-1.5 text-sm font-medium text-green-800 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50"
              >Dismiss</button
            >
          </div>
        </div>
      </div>
    </div>
  </div>
{/if}

<div class="flex justify-end">
  <button
    on:click={() => updatePin()}
    type="button"
    class="w-full sm:w-auto inline-flex justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500"
    >Update Pin</button
  >
</div>
