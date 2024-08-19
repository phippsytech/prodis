<script>
    import { onMount } from "svelte";

    import { push } from "svelte-spa-router";
    import Role from "@shared/Role.svelte";
    import UserForm from "./UserForm.svelte";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

    export let params;

    let user = {
        roles: [],
    };
    let stored_user = {};
    let mounted = false;
    let show = false;

    BreadcrumbStore.set({
        path: [{ url: "/users", name: "Users" }],
    });

    $: {
        if (mounted) {
            show = !(JSON.stringify(user) === JSON.stringify(stored_user));
        }
    }

    onMount(async () => {
        await jspa("/User", "getUser", { user_id: params.user_id })
            .then((result) => {
                user = result.result;

                // if(user.shared == "1") user.shared = true;
                // if(user.shared == "0") user.shared = false;

                mounted = true;
            })
            .catch(() => {
                console.log("there was an error");
            })
            .finally(() => {
                // Make a copy of the object
                stored_user = Object.assign({}, user);
            });

        BreadcrumbStore.set({
            path: [
                { url: "/users", name: "Users" },
                { url: null, name: user.email },
            ],
        });
    });

    function update() {}

    function undo() {
        user = Object.assign({}, stored_user);
    }

    function save() {
        jspa("/User", "update", user).then((result) => {
            push("/users");
        });
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: false,
                show: !(JSON.stringify(user) === JSON.stringify(stored_user)),
                undo: () => undo(),
                save: () => save(),
            });
        }
    }
</script>

<!-- <Breadcrumbs path={breadcrumbs_path} target="User"  /> -->

<Role roles={["super"]} conditional={true}>
    <div slot="authorised">
        <UserForm bind:user />
    </div>
    <div slot="declined" class="text-gray-400">
        You do not have permission to view this page.
    </div>
</Role>
