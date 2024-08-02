<script>
    //   I like the functionality found in this particular combobox component.  I just don't like the design
    // https://carbon-components-svelte.onrender.com/components/ComboBox#default
    // you can probably find code inspiration from their github repo.

    export let label = "Select";
    export let value = "";

    let timeout_id;

    export let option = null;

    export let placeholder = "Select an option";

    export let options = [];

    let selected = false;

    $: filteredOptions =
        value === ""
            ? options
            : options.filter((option) =>
                  option
                      .toLowerCase()
                      .replace(/\s+/g, "")
                      .includes(value.toLowerCase().replace(/\s+/g, "")),
              );

    function handleBlur() {
        // gives time for setValue to work if it is the reason input has been blurred.
        timeout_id = setTimeout(() => {
            selected = true;
        }, "100");
    }

    function setValue(option) {
        value = option;
        selected = true;
    }

    function resetSelected() {
        selected = false;
    }
    $: if (value == filteredOptions[0]) {
        selected = true;
    }
</script>

<div class="relative">
    <div class="form-floating mb-2">
        <input
            on:blur={() => handleBlur()}
            on:keydown={() => resetSelected()}
            type="text"
            class="form-control
    inline-block
    w-full
    px-3
    py-1.5
    text-base
    font-normal
    text-gray-700
    bg-white bg-clip-padding
    border border-solid border-gray-300
    rounded
    transition
    ease-in-out
    m-0
    focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none"
            id="floatingInput"
            {placeholder}
            bind:value
        />
        <label for="floatingInput" class="text-gray-700">{label}</label>
    </div>

    {#if filteredOptions.length > 0 && value && selected == false}
        <div
            class="absolute flex justify-center w-full"
            style="z-index:1; box-shadow: 10px 5px 20px rgba(0,0,0,0.5);"
        >
            <div
                class="bg-white rounded-lg border border-gray-200 w-full text-gray-900"
            >
                {#each filteredOptions as option}
                    <button
                        tabindex="-1"
                        on:mousedown={() => setValue(option)}
                        on:click={() => setValue(option)}
                        type="button"
                        class="
          text-left
          px-6
          py-2
          border-b border-gray-200
          w-full
          hover:bg-gray-100 hover:text-gray-500
          focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600
          transition
          duration-500
          cursor-pointer
        "
                    >
                        {option}
                    </button>
                {/each}
            </div>
        </div>
    {/if}
</div>

<style>
    .form-floating {
        position: relative;
    }

    .form-floating ::-webkit-input-placeholder {
        opacity: 1;
        transition: inherit;
        color: #ccc;
    }

    .form-floating ::-moz-placeholder {
        opacity: 1;
        transition: inherit;
        color: #ccc;
    }

    .form-floating input:focus::-webkit-input-placeholder {
        opacity: 1;
    }

    .form-floating label {
        position: absolute;
        top: -0.5rem;
        left: 0.15rem;
        opacity: 0.65;
        transform: scale(0.85);
    }

    .form-floating .form-control {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }
</style>
