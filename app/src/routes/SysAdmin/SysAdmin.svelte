<script>
    import IconMenu from "@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte";
    import BillingIcon from "@shared/PhippsyTech/svelte-ui/icons/Billing.svelte";
    import {
        CameraIcon,
        CurrencyDollarIcon,
        PowerIcon,
        UsersIcon,
        TicketIcon,
        EyeIcon,
        ChatBubbleLeftRightIcon,
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
            url: "/billing",
            name: "Billing",
            icon: BillingIcon,
            role: ["super"],
        },
        {
            url: "/chat",
            name: "Team Chat",
            icon: ChatBubbleLeftRightIcon,
            role: ["super"],
        },
        {
            url: "/tickets",
            name: "Ticket System",
            icon: TicketIcon,
            role: ["super"],
        },

        {
            url: "/expenses",
            name: "Expenses",
            icon: CurrencyDollarIcon,
            role: ["super"],
        },

        {
            url: "/sysadmin/camera",
            name: "Camera",
            icon: CameraIcon,
            role: ["super"],
        },

        { url: "/users", name: "Users", icon: UsersIcon, role: ["super"] },
        { url: "/pulse", name: "Pulse", icon: EyeIcon, role: ["super"] },

        { url: "/logout", name: "Logout", icon: PowerIcon },
    ];

    BreadcrumbStore.set({
        path: [{ url: null, name: "Crystel Care" }],
    });
</script>

{#if haveCommonElements($RolesStore, ["sysadmin"])}
    <!-- <NavBar hideMenu={true} /> -->
    <div class="sm:mx-auto items-center sm:w-1/2"></div>

    <div class="py-2 px-4">
        <div class="text-xs text-slate-800">System Administration</div>
        <div
            class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular"
        >
            {$UserStore.name}
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
