<script>
  import { uid, onClickOutside } from "./Context.svelte";

  export let disabled = undefined;
  export let error = undefined;
  export let expand = true;
  export let id = uid();
  export let label = "";
  export let loading = false;
  export let name;
  export let options = [];
  export let placeholder = undefined;
  export let readonly = undefined;
  export let required = undefined;
  export let value = "";

  export let filter = (text) => {
    const sanitized = text.trim().toLowerCase();

    return options.reduce((a, o) => {
      let match;

      if (o.options) {
        const options = o.options.filter((o) =>
          o.text.toLowerCase().includes(sanitized),
        );

        if (options.length) {
          match = { ...o, options };
        }
      } else if (o.text.toLowerCase().includes(sanitized)) {
        match = o;
      }

      match && a.push(match);

      return a;
    }, []);
  };

  let listElement;
  let inputElement;
  let list = [];
  let isListOpen = false;
  let selectedOption;

  async function onInputKeyup(event) {
    switch (event.key) {
      case "Escape":
      case "ArrowUp":
      case "ArrowLeft":
      case "ArrowRight":
      case "Enter":
      case "Tab":
      case "Shift":
        break;
      case "ArrowDown":
        await showList(event.target.value);
        listElement
          .querySelector(`[role="option"]:not([aria-disabled="true"])`)
          ?.focus();

        event.preventDefault();
        event.stopPropagation();
        break;

      default:
        await showList(event.target.value);
    }
  }

  function onInputKeydown(event) {
    let flag = false;

    switch (event.key) {
      case "Escape":
        hideList();
        flag = true;
        break;

      case "Tab":
        hideList();
        break;
    }

    if (flag) {
      event.preventDefault();
      event.stopPropagation();
    }
  }

  async function onInputClick(event) {
    await showList(event.target.value);
    // Scroll selected option into view.
    listElement
      .querySelector(`[role="option"][data-value="${value}"]`)
      ?.scrollIntoView();
  }

  function onOptionClick(event) {
    if (!event.target.matches(`[role="option"]:not([aria-disabled="true"])`))
      return;

    selectOption(event.target);
    hideList();
  }

  function onListKeyDown(event) {
    let flag = false;

    switch (event.key) {
      case "ArrowUp":
        let prevOptionElement = event.target.previousElementSibling;

        while (prevOptionElement) {
          if (
            prevOptionElement.matches(
              `[role="option"]:not([aria-disabled="true"])`,
            )
          )
            break;
          prevOptionElement = prevOptionElement.previousElementSibling;
        }

        prevOptionElement?.focus();
        flag = true;
        break;

      case "ArrowDown":
        let nextOptionElement = event.target.nextElementSibling;

        while (nextOptionElement) {
          if (
            nextOptionElement.matches(
              `[role="option"]:not([aria-disabled="true"])`,
            )
          )
            break;
          nextOptionElement = nextOptionElement.nextElementSibling;
        }

        nextOptionElement?.focus();
        flag = true;
        break;

      case "Enter":
        selectOption(event.target);
        hideList();
        flag = true;
        break;

      case "Escape":
        hideList();
        flag = true;
        break;

      case "Tab":
        hideList();
        break;

      default:
        inputElement.focus();
    }

    if (flag) {
      event.preventDefault();
      event.stopPropagation();
    }
  }

  async function showList(inputValue) {
    const isExactMatch = options.some((o) =>
      o.options
        ? o.options.some((o) => o.text === inputValue)
        : o.text === inputValue,
    );

    list =
      inputValue === "" || isExactMatch ? options : await filter(inputValue);
    isListOpen = true;
  }

  function hideList() {
    if (!isListOpen) return;

    if (selectedOption) {
      inputElement.value = selectedOption.text;
    }

    isListOpen = false;
    inputElement.focus();
  }

  function selectOption(optionElement) {
    value = optionElement.dataset.value;

    selectedOption = {
      text: optionElement.dataset.text,
      value: optionElement.dataset.value,
    };
  }
</script>

<div use:onClickOutside={hideList}>
  <label
    for="combobox"
    class="block text-sm font-medium leading-6 text-gray-900">{label}</label
  >
  <div class="relative mt-2">
    <input
      bind:this={inputElement}
      on:focus
      on:blur
      on:input
      on:keyup={onInputKeyup}
      on:keydown={onInputKeydown}
      on:mousedown={onInputClick}
      {id}
      {name}
      type="text"
      {disabled}
      autocapitalize="none"
      autocomplete="off"
      {readonly}
      {placeholder}
      spellcheck="false"
      role="combobox"
      aria-autocomplete="list"
      aria-expanded={isListOpen}
      aria-required={required ? "true" : undefined}
      class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-12 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
    />
    <button
      on:click={onInputClick}
      type="button"
      class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none"
    >
      <svg
        class="h-5 w-5 text-gray-400"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
      >
        <path
          fill-rule="evenodd"
          d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z"
          clip-rule="evenodd"
        />
      </svg>
    </button>

    <ul
      role="listbox"
      aria-label={label}
      hidden={!isListOpen}
      on:click={onOptionClick}
      on:keydown={onListKeyDown}
      bind:this={listElement}
      class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
    >
      {#each list as option (option)}
        {#if option.options}
          <!--
          Combobox option, manage highlight styles based on mouseenter/mouseleave and keyboard navigation.
  
          Active: "text-white bg-indigo-600", Not Active: "text-gray-900"
        -->
          {#each option.options as option (option)}
            <li
              class="relative cursor-default select-none py-2 pl-3 pr-9 text-gray-900"
              class:--disabled={option.disabled}
              role="option"
              tabindex={option.disabled ? undefined : "-1"}
              data-text={option.text}
              data-value={option.value}
              aria-selected={value === option.value}
              aria-disabled={option.disabled}
            >
              <!-- Selected: "font-semibold" -->
              <span
                class="block truncate {option.value === value
                  ? 'font-semibold'
                  : ''}"
              >
                {option.text}
              </span>

              <!--
            Checkmark, only display for selected option.
  
            Active: "text-white", Not Active: "text-indigo-600"
          -->
              <span
                class="absolute inset-y-0 right-0 flex items-center pr-4 {option.value ===
                value
                  ? 'text-indigo-600'
                  : 'text-white'}"
              >
                <svg
                  class="h-5 w-5"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                    clip-rule="evenodd"
                  />
                </svg>
              </span>
            </li>
          {/each}
        {:else}
          <li
            class="relative cursor-default select-none py-2 pl-3 pr-9 text-gray-900"
            class:--disabled={option.disabled}
            role="option"
            tabindex={option.disabled === true ? undefined : "-1"}
            data-text={option.text}
            data-value={option.value}
            aria-selected={value === option.value}
            aria-disabled={option.disabled}
          >
            <span class="block truncate">
              {option.text}
            </span>

            <span
              class="absolute inset-y-0 right-0 flex items-center pr-4 {option.value ===
              value
                ? 'text-indigo-600'
                : 'text-white'}"
            >
              <svg
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                  clip-rule="evenodd"
                />
              </svg>
            </span>
          </li>
        {/if}
      {:else}
        <li>No results available</li>
      {/each}
    </ul>
  </div>
</div>
