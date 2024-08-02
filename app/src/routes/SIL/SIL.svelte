<script>
    import Container from "@shared/Container.svelte";
    import IconMenu from "@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte";
    import CubeIcon from "@shared/PhippsyTech/svelte-ui/icons/Cube.svelte";
    import ClipboardDocumentCheckIcon from "@shared/PhippsyTech/svelte-ui/icons/ClipboardDocumentCheckIcon.svelte";
    import { BreadcrumbStore, RolesStore } from "@shared/stores.js";

    let icons = [
        // {url:`/sil/rosterofcare`, name:"Roster Of Care", icon: CubeIcon, role:["super"]},

        {
            url: "/sil/formsummary",
            name: "Form Summary",
            icon: ClipboardDocumentCheckIcon,
            role: ["house.lead", "admin"],
        },
        {
            url: "/sil/filteredformsummary",
            name: "Filtered Form Summary",
            icon: ClipboardDocumentCheckIcon,
            role: ["house.lead", "admin"],
        },
    ];

    BreadcrumbStore.set({
        path: [{ url: null, name: "SIL" }],
    });
</script>

<Container>
    <IconMenu {icons} userRoles={$RolesStore} />
</Container>
