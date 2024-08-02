<script>
    import Container from "@shared/Container.svelte";
    import IconMenu from "@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte";
    import { UserGroupIcon } from "heroicons-svelte/24/outline";
    import { BreadcrumbStore, RolesStore } from "@shared/stores.js";

    let icons = [
        {
            url: `/settings/supportcatalogue`,
            name: "NDIS Support Catalogue",
            icon: UserGroupIcon,
            role: ["admin"],
        },
        {
            url: `/settings/accounting`,
            name: "Accounting",
            icon: UserGroupIcon,
            role: ["admin"],
        },
        {
            url: `/settings/documents`,
            name: "Documents",
            icon: UserGroupIcon,
            role: ["admin"],
        },
        {
            url: `/settings/staff`,
            name: "Staff",
            icon: UserGroupIcon,
            role: ["admin"],
        },
        {
            url: `/settings/reports`,
            name: "Reports",
            icon: UserGroupIcon,
            role: ["admin"],
        },
    ];

    BreadcrumbStore.set({
        path: [{ url: null, name: "Settings" }],
    });
</script>

<div class="flex justify-between items-center mb-4">
    <div
        class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular"
    >
        Settings
    </div>
</div>
<Container>
    <IconMenu {icons} userRoles={$RolesStore} />
</Container>
