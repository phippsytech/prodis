<script>
  import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
  import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";

  export let props = {};

  function makeRepresentative() {
    props.representative = 1;
  }
</script>

{#if props.id}
  {#if props.representative == 1}
    <div
      class="bg-blue-100 rounded-lg py-5 px-6 mb-4 text-base text-blue-700 mb-3"
      role="alert"
    >
      <b>{props.name} is the representative.</b><br />
      <span class="text-xs"
        >To change this, make another stakeholder the representative.</span
      >
    </div>
  {:else}
    <div
      class="bg-blue-100 rounded-lg py-5 px-6 mb-4 text-base text-blue-700 mb-3 text-center"
      role="alert"
    >
      <button
        on:click={() => makeRepresentative()}
        type="button"
        class="inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out"
        >Make {props.name} Representative</button
      >
      <div class="text-xs mt-2">
        Note: This will replace the current representative.
      </div>
    </div>
  {/if}
{/if}

<FloatingInput
  label="Name"
  bind:value={props.name}
  placeholder="Alex Citizen"
/>
<FloatingInput
  label="Email"
  bind:value={props.email}
  placeholder="alex@citizen.com"
/>
<FloatingInput
  label="Phone"
  bind:value={props.phone}
  placeholder="04XX XXX XXX"
/>
<FloatingInput
  label="Relationship"
  bind:value={props.relationship}
  placeholder="eg: Parent, Guardian, School, Doctor, etc."
/>
<FloatingTextArea
  bind:value={props.notes}
  label="Notes"
  placeholder="Add any notes applicable to this stakeholder."
  style="height:175px"
/>
