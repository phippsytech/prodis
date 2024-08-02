<script>
    /*
    This component exposes the user_id
    It will display the related user if a user_id is provided
    It will display a blank user record that will create a user_id (checking for duplicates)
    If user details are updated, it will update the user record accordingly.
*/

    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import { jspa } from "@shared/jspa.js";
    import { PhoneIcon } from "@app/../node_modules/heroicons-svelte/dist/24/outline";
    import { toastError, toastSuccess } from "@shared/toastHelper.js";
    import { createEventDispatcher } from "svelte";
    const dispatch = createEventDispatcher();

    // import CheckboxButtonGroup from '@shared/PhippsyTech/svelte-ui/forms/CheckboxButtonGroup.svelte';
    // import Toggle from '@shared/PhippsyTech/svelte-ui/forms/Toggle.svelte';

    export let user_id = null;

    let user = {};
    let stored_user = {};

    onMount(async () => {
        if (user_id) {
            await jspa("/User", "getUser", { user_id: user_id })
                .then((result) => {
                    user = result.result;
                    stored_user = Object.assign({}, user);
                    dispatch("loaded", user);
                })
                .catch(() => {})
                .finally(() => {
                    // Make a copy of the object
                });
        }
    });

    $: {
        if (JSON.stringify(user) !== JSON.stringify(stored_user)) {
            dispatch("changed", user);
        }
    }

    export function upsert() {
        return new Promise((resolve, reject) => {
            if (user.id) {
                update()
                    .then(() => resolve(user.id))
                    .catch(reject);
            } else {
                create()
                    .then((result) => resolve(user.id))
                    .catch(reject);
            }
        });
    }

    export function undo() {
        user = Object.assign({}, stored_user);
    }

    function create() {
        return jspa("/User", "create", user).then((result) => {
            toastSuccess("User Created");
            user = result.result;
            return user;
        });
    }

    function update() {
        return jspa("/User", "update", user).then(() => {
            toastSuccess("User Updated");
        });
    }
</script>

<Container>
    <div class="text-xs opacity-50 mb-2">User Details</div>

    <div class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2">
        <div class="flex-grow">
            <FloatingInput
                bind:value={user.first_name}
                label="First Name"
                placeholder="eg: Annie"
            />
        </div>
        <div class="flex-grow">
            <FloatingInput
                bind:value={user.last_name}
                label="Last Name"
                placeholder="eg: Walker"
            />
        </div>
    </div>
    <FloatingInput
        bind:value={user.name}
        label="Display Name"
        placeholder="eg: Bella"
        autocomplete="off"
    />
    <FloatingInput
        bind:value={user.email}
        label="Email"
        placeholder="eg: annie.walker@cia.gov"
    />
    <div class="flex justify-between gap-2 items-stretch">
        <div class="flex-grow">
            <FloatingInput
                bind:value={user.phone}
                label="Phone"
                placeholder="eg: 04XX XXX XXX"
            />
        </div>

        <a
            href="tel:{user.phone}"
            class="flex items-center justify-center mb-2 block rounded-md bg-indigo-600 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >
            <PhoneIcon class="h-5 w-5 inline-block" />
        </a>
    </div>
</Container>
