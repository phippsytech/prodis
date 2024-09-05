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
    import {
        PhoneIcon,
        EnvelopeIcon,
    } from "@app/../node_modules/heroicons-svelte/dist/24/outline";
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

    <div class="flex justify-between gap-2 items-stretch">
        <div class="flex-grow">
            <div
                class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular"
            >
                {user.display_name}
            </div>
            <div>
                {user.first_name}
                {user.last_name}

                {#if user.email}
                    <a class="text-indigo-600 ml-2" href="mailto:{user.email}">
                        <EnvelopeIcon
                            class="h-4 w-4 inline-block"
                        />{user.email}</a
                    >
                {/if}
                {#if user.phone}
                    <a class="text-indigo-600 ml-2" href="tel:{user.phone}">
                        <PhoneIcon
                            class="h-4 w-4 inline-block"
                        />{user.phone}</a
                    >
                {/if}
            </div>
        </div>
    </div>
</Container>
