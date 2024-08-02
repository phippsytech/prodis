<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import Badge from "@shared/PhippsyTech/Tailwind/App/Elements/Badge.svelte";
    import CheckboxButtonGroup from "@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte";
    import CredentialList from "./CredentialList.svelte";

    export let credential_options;
    export let credential_list = [];

    let point_goal = 100;
    let credential_id;
    let points;
    let edit = false;
    let verification_method_options = [
        { option: "Any of the listed credentials", value: "any" },
        { option: "All of the listed credentials", value: "all" },
        { option: "Meet a points value", value: "points" },
    ];
    let rule = {
        name: "100 Points of ID",
        staff_groups: ["therapist", "sil"],
        verification_method: "points",
        point_goal: 100,
        credentials: [
            { id: 1, points: 70 },
            { id: 2, points: 70 },
            { id: 3, points: 70 },
            { id: 4, points: 70 },
            { id: 5, points: 40 },
            { id: 6, points: 40 },
            { id: 7, points: 40 },
            { id: 8, points: 40 },
        ],
    };

    function getVerificationMethod(value) {
        let verification_method = verification_method_options.find(
            (verification_method) => verification_method.value == value,
        ).option;
        return verification_method;
    }

    function editRule() {
        edit = true;
    }

    function close() {
        edit = false;
    }

    function deleteRule() {
        edit = false;
    }

    function saveRule() {
        edit = false;
    }

    function addCredential() {
        // get credential name from credential_options by looking up value and returning option
        let credential_name = credential_options.find(
            (credential) => credential.value == credential_id,
        ).option;
        rule.credentials = [
            ...rule.credentials,
            { id: credential_id, name: credential_name, points: points },
        ];
    }

    function filterCredentialOptions() {
        return credential_options.filter(
            (credential_option) =>
                !rule.credentials.some(
                    (credential) => credential.id == credential_option.value,
                ),
        );
    }

    $: credential_options_list = filterCredentialOptions();
</script>

<div
    class="min-h-400 overflow-y-auto rounded-lg border border-gray-200 outline-none bg-white mb-4 {edit
        ? ''
        : 'hover:border-indigo-600'}"
>
    <div class="px-4 py-2">
        {#if !edit}
            <div class="flex items-center justify-between">
                <div class="font-semibold text-xl">{rule.name}</div>
                <div class="flex-shrink-0">
                    <button
                        on:click={() => editRule()}
                        type="button"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-transparent bg-white text-gray-400 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <span class="sr-only">Open options</span>
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <p class="text-sm">
                <span class="opacity-50">Staff Groups:</span>
                {#each rule.staff_groups as staff_group}
                    <Badge label={staff_group} />
                {/each}
            </p>
            <p class="text-sm mb-2">
                <span class="opacity-50">Verification Method:</span>
                {getVerificationMethod(rule.verification_method)}
            </p>

            <CredentialList
                bind:credential_options
                bind:credential_list={rule.credentials}
                edit={false}
            />
        {:else}
            <FloatingInput
                label="Rule Name"
                type="text"
                bind:value={rule.name}
            />

            <div
                class="bg-white px-3 pt-2 pb-4 mb-2 border border-gray-300 rounded-md"
            >
                <div class="text-xs opacity-50 mb-2">Staff Groups</div>
                <CheckboxButtonGroup
                    options={[
                        { value: "therapist", option: "Therapist" },
                        { value: "sil", option: "SIL" },
                    ]}
                    bind:values={rule.staff_groups}
                />
            </div>

            <FloatingSelect
                on:change
                bind:value={rule.verification_method}
                label="Verfication Method"
                instruction="Choose verification method"
                options={[
                    { option: "Any of the listed credentials", value: "any" },
                    { option: "All of the listed credentials", value: "all" },
                    { option: "Meet a points value", value: "points" },
                ]}
                hideValidation={true}
            />

            {#if rule.verification_method == "points"}
                <FloatingInput
                    label="Point Goal"
                    type="number"
                    bind:value={point_goal}
                />
            {/if}

            <CredentialList
                bind:credential_options
                bind:credential_list={rule.credentials}
                points={rule.verification_method == "points"}
                edit={true}
            />

            <div class="flex justify-start gap-x-2 items-center">
                <FloatingSelect
                    on:change
                    bind:value={credential_id}
                    label="Credential"
                    instruction="Choose a credential"
                    options={credential_options_list}
                    hideValidation={true}
                />

                {#if rule.verification_method == "points"}
                    <FloatingInput
                        label="Points"
                        type="number"
                        bind:value={points}
                    />
                {/if}

                <button
                    on:click={() => addCredential()}
                    type="button"
                    class="w-full sm:w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                    >Add Credential</button
                >
            </div>
        {/if}
    </div>

    {#if edit}
        <div class="bg-gray-50 px-4 py-3 sm:px-6">
            <div class="flex justify-between">
                <button
                    on:click={() => deleteRule()}
                    type="button"
                    class="inline-flex w-auto justify-start rounded-md bg-white px-3 py-2 text-sm font-semibold text-red-600 shadow-sm hover:bg-white sm:mr-3 sm:w-auto"
                    >Delete</button
                >
                <div class="flex gap-1">
                    <button
                        on:click={() => close()}
                        type="button"
                        class="w-full sm:w-auto sm:mr-3 inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                        >Close</button
                    >
                    <button
                        on:click={() => saveRule()}
                        type="button"
                        class="w-full sm:w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                        >Save</button
                    >
                </div>
            </div>
        </div>
    {/if}
</div>
