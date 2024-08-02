<script>
    import Container from "@shared/Container.svelte";
    import IconMenu from "@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte";
    import { UserGroupIcon } from "heroicons-svelte/24/outline";
    import { RolesStore } from "@shared/stores.js";
    import { BreadcrumbStore } from "@shared/stores.js";

    export let client_id;

    BreadcrumbStore.set({
        path: [],
    });

    let icons = [
        {
            url: `/clients/${client_id}/details`,
            name: "Client Details",
            icon: UserGroupIcon,
            role: ["house", "house.lead"],
        },
        {
            url: `/clients/${client_id}/timeline`,
            name: "Timeline",
            icon: UserGroupIcon,
            role: ["house", "house.lead"],
        },
        {
            url: `/clients/${client_id}/forms`,
            name: "Forms",
            icon: UserGroupIcon,
            role: ["house", "house.lead"],
        },
        {
            url: `/clients/${client_id}/documents`,
            name: "Documents",
            icon: UserGroupIcon,
            role: ["house", "house.lead"],
        },
        {
            url: `/clients/${client_id}/roster`,
            name: "Roster",
            icon: UserGroupIcon,
            role: ["house", "house.lead"],
        },
        {
            url: `/clients/${client_id}/staff`,
            name: "Staff",
            icon: UserGroupIcon,
            role: ["house.lead"],
        },
        {
            url: `/clients/${client_id}/settings`,
            name: "Settings",
            icon: UserGroupIcon,
            role: ["house.lead"],
        },
    ];
</script>

<Container>
    <IconMenu {icons} userRoles={$RolesStore} />
</Container>
