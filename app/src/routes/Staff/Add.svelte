<script>
    import Role from "@shared/Role.svelte";
    import Container from "@shared/Container.svelte";

    import { jspa } from "@shared/jspa.js";
    import { push } from "svelte-spa-router";
    import { BreadcrumbStore } from "@shared/stores.js";
    import UserComponent from "@shared/UserComponent.svelte";

    let staffer = {};

    let userComponent;

    async function addStaffer() {
        try {
            const user_id = await userComponent.upsert();
            staffer.user_id = user_id;
            jspa("/Staff", "addStaffer", staffer).then((result) => {
                push("/staff/" + result.result.id);
            });
        } catch (error) {
            console.error(error);
        }
    }

    BreadcrumbStore.set({
        path: [
            { url: "/staff", name: "Staff" },
            { url: null, name: "Add" },
        ],
    });

    // function handleLoaded(event){
    //     staffer.user = event.detail;
    //     stored_staffer.user = Object.assign({}, event.detail);
    // }

    // function handleChanged(event){
    //     staffer.user = event.detail;
    // }
</script>

<Role roles={["admin"]} conditional={true}>
    <div slot="authorised">
        <div
            class="px-2 text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
            style="color:#220055;"
        >
            Add Staffer
        </div>

        <UserComponent
            bind:this={userComponent}
            bind:user_id={staffer.user_id}
        />

        <!-- on:loaded={(event)=>handleLoaded(event)} 
        on:changed={(event)=>handleChanged(event)}  -->

        <div class="flex justify-end px-4">
            <button
                on:click={() => addStaffer()}
                type="button"
                class="block rounded-md bg-indigo-600 px-4 py-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >Add Staff</button
            >
        </div>
    </div>
    <div slot="declined">
        <Container>You aren't authorised to add staff</Container>
    </div>
</Role>
