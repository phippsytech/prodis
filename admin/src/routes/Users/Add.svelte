<script>
    import { push } from "svelte-spa-router";
    import Role from "@shared/Role.svelte";

    import UserForm from "./UserForm.svelte";
    import { jspa } from "@shared/jspa.js";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

    let user = {};
    let stored_user = {};

    let breadcrumbs_path = [{ url: "/users", name: "Users" }];

    function undo() {
        user = Object.assign({}, stored_user);
    }

    function save() {
        jspa("/User", "create", user).then((result) => {
            push("/users");
        });
    }

    $: {
        ActionBarStore.set({
            can_delete: false,
            show: !(JSON.stringify(user) === JSON.stringify(stored_user)),
            undo: () => undo(),
            save: () => save(),
        });
    }
</script>

<Role roles={["super"]} conditional={true}>
    <div slot="authorised">
        <UserForm bind:user />
    </div>
    <div slot="declined" class="text-gray-400">
        You do not have permission to view this page.
    </div>
</Role>
