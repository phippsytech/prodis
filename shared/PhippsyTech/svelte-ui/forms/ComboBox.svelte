<script>
  import { makeUniqueId } from "../js/base.js";

  export let label = "";
  export let options = [];
  export let value = "";
  export let ticket_id = null;

  let id;
  let me = false; // me is used to identify if this component is being called directly
  if (!id) {
    id = makeUniqueId();
    me = true;
  }

  function handleInput(event) {
    const inputValue = event.target.value;

    // Check if the entered value matches an option
    const matchingOption = options.find(
      (option) => option.value === inputValue,
    );

    if (matchingOption) {
      // If a matching option is found, set ticket_id to its id
      ticket_id = matchingOption.id;
    } else {
      // If no matching option, set ticket_id to null and value to the entered text
      ticket_id = null;
      value = inputValue;
    }
  }
</script>

<div
  class="rounded-md px-3 pb-1.5 pt-2.5 shadow-sm ring-1 ring-inset ring-indigo-100 focus-within:ring-2 focus-within:ring-indigo-600 bg-white mb-2"
>
  <label for={id} class="block text-xs text-gray-900/50">{label}</label>
  <input
    type="text"
    bind:value
    on:input={handleInput}
    list="datalistOptions"
    class="
            block
            w-full
            p-0
            -mx-1
            text-base
            font-normal
            text-gray-700
            bg-white bg-clip-padding
            block w-full border-0 p-0 text-gray-900 placeholder:text-gray-400 outline-none"
    required
  />
  <datalist id="datalistOptions">
    {#each options as option (option.id)}
      <option value={option.value} />
    {/each}
  </datalist>
</div>

<style>
  /* You can add custom styling here */

  /* Style the datalist for Chrome */
  input[type="text"]::-webkit-calendar-picker-indicator {
    display: none;
  }

  /* Style the datalist for Firefox */
  input[type="text"]::-moz-calendar-picker-indicator {
    display: none;
  }

  /* Style the options in the datalist */
  datalist option {
    background-color: white;
    color: black;
    padding: 0.5rem;
    border: none;
  }
</style>
