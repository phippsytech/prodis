<script>
    export let credential_options = [];
    export let credential_list = [];
    export let edit = false;
    export let points = false;

    function deleteCredential(credential_id) {
        credential_list = credential_list.filter(
            (credential) => credential.id != credential_id,
        );
    }

    function getCredentialName(credential_id) {
        let credential_name = credential_options.find(
            (credential_option) => credential_option.value == credential_id,
        ).option;
        return credential_name;
    }
</script>

{#if credential_options.length > 0}
    {#each credential_list as credential}
        <span
            class="flex items-center inline-flex rounded-full {edit
                ? 'hover:bg-indigo-600 hover:text-white'
                : ''}  bg-indigo-100 px-4 py-1 ml-1 mb-2 text-sm font-normal text-indigo-700 gap-x-2"
            >{getCredentialName(credential.id)}
            {#if points}<span
                    class="text-xs bg-white/25 rounded-full px-1 items-center"
                    >{credential.points}</span
                >{/if}
            {#if edit}
                <svg
                    on:click={() => deleteCredential(credential.id)}
                    class="h-4 w-4 cursor-pointer"
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
                        d="M6 18L18 6M6 6l12 12"
                    ></path>
                </svg>
            {/if}
        </span>
    {/each}
{/if}
