<script>
    import IconMenu from "@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte";
    import BillingIcon from "@shared/PhippsyTech/svelte-ui/icons/Billing.svelte";
    import {
        CurrencyDollarIcon,
        PowerIcon,
        UsersIcon,
        TicketIcon,
        DocumentIcon,
        EyeIcon,
        ChatBubbleLeftRightIcon,
        ShieldCheckIcon,
    } from "heroicons-svelte/24/outline";
    import {
        UserStore,
        StafferStore,
        BreadcrumbStore,
        RolesStore,
    } from "@shared/stores.js";
    import { haveCommonElements } from "@shared/utilities.js";

    export let roles;
    export let conditional = false;

    let icons = [
        {
            url: "/credentials",
            name: "Credentials",
            icon: ShieldCheckIcon,
            role: ["manager", "admin"],
        },
        {
            url: "/documenttypes",
            name: "Document Types",
            icon: DocumentIcon,
            role: ["manager", "admin"],
        },
    ];

    BreadcrumbStore.set({
        path: [{ url: null, name: "Compliance" }],
    });
</script>

{#if haveCommonElements($RolesStore, ["sysadmin"])}
    <!-- <NavBar hideMenu={true} /> -->
    <div class="sm:mx-auto items-center sm:w-1/2"></div>

    <div class="py-2 px-4">
        <div
            class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular"
        >
            Compliance
        </div>
        <!-- <div class="text-2xl font-medium">Michael Phipps</div> -->
    </div>
    <!-- <div class="bg-indigo-600 rounded rounded-lg text-white p-4"><span class="text-3xl font-bold">MOtion</span>this is a thing</div> -->

    <IconMenu {icons} userRoles={$RolesStore} />
{:else}
    <div class="text-xs text-slate-800">This is for System Administration</div>
    <div
        class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-2"
        style="color:#220055;"
    >
        {$UserStore.name}
    </div>
{/if}
