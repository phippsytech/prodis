<script>
    import IconMenu from "@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte";
    import {
        CurrencyDollarIcon,
        UserIcon,
        UserGroupIcon,
        ClockIcon,
        ClipboardDocumentListIcon,
        DocumentTextIcon,
        AdjustmentsVerticalIcon,
        BriefcaseIcon,
        PowerIcon,
        ChatBubbleLeftEllipsisIcon,
        FireIcon,
        ExclamationCircleIcon,
        BellIcon,
        BanknotesIcon,
        HomeIcon,
        UsersIcon,
    } from "heroicons-svelte/24/outline";
    import { RolesStore } from "../stores.js";
    import Role from "@shared/Role.svelte";

    let icons = [
        { url: "/clients", name: "Clients", icon: UserGroupIcon },
        { url: "/notifications", name: "Notifications", icon: BellIcon },
        { url: "/staff", name: "Staff", icon: UserIcon },
        {
            url: "/services",
            name: "Services",
            icon: BriefcaseIcon,
            role: ["admin"],
        },
        { url: "/billables", name: "Time Tracking", icon: ClockIcon },
        { url: "/planmanagers", name: "Plan Managers", icon: BanknotesIcon },
        {
            url: "/meetingnotes",
            name: "Meeting Notes",
            icon: ClipboardDocumentListIcon,
        },
        {
            url: "/invoicing",
            name: "Invoicing",
            icon: CurrencyDollarIcon,
            role: ["accounts", "admin"],
        },
        {
            url: "/reports",
            name: "Reports",
            icon: DocumentTextIcon,
            role: ["manager", "admin"],
        },
        {
            url: "/registers/feedbacks",
            name: "Feedback Register",
            icon: ChatBubbleLeftEllipsisIcon,
        },
        { url: "/registers/risks", name: "Risk Register", icon: FireIcon },
        {
            url: "/registers/conflictofinterests",
            name: "Conflict Of Interest Register",
            icon: ExclamationCircleIcon,
        },
        {
            url: "/settings",
            name: "Settings",
            icon: AdjustmentsVerticalIcon,
            role: ["admin"],
        },
        { url: "/house", name: "House", icon: HomeIcon, role: ["house"] },
        {
            url: "/sil",
            name: "Supported Independant Living",
            icon: HomeIcon,
            role: ["admin"],
        },
        { url: "/users", name: "Users", icon: UsersIcon, role: ["super"] },
        { url: "/logout", name: "Logout", icon: PowerIcon },
    ];
</script>

<IconMenu {icons} userRoles={$RolesStore} />

<!-- <div class="mt-10 relative inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300  ">
        Notifications
        <div class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-2 -right-2 ">8</div>
    </div> -->
