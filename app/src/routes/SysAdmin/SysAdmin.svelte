<script>
    import IconMenu from "@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte";
    import BillingIcon from "@shared/PhippsyTech/svelte-ui/icons/Billing.svelte";
    import {
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

    //import StaffCapacityGraph.svelte
    import StaffCapacityGraph from "./StaffCapacityGraph.svelte";

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

        { url: "/users", name: "Users", icon: UsersIcon, role: ["super"] },
        { url: "/pulse", name: "Pulse", icon: EyeIcon, role: ["super"] },

        { url: "/logout", name: "Logout", icon: PowerIcon },
    ];

    BreadcrumbStore.set({
        path: [{ url: null, name: "Crystel Care" }],
    });

    //test data, inquire where to get the actual max capacity data from.
    let labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];

    let datasets = [
    {
      label: 'Hours Worked',
      data: [60, 50, 40, 30, 20], // Your custom data for Hours Worked
      fill: true,
      borderColor: 'rgba(75, 192, 192, 1)',
      backgroundColor: 'rgba(75, 192, 192, 0.2)',
      tension: 0.1, // Makes the line curved
      order: 2 // Draw this line below the max capacity line
    },
    {
      label: 'Max Capacity',
      data: [30, 30, 30, 30, 30], // Your custom data for Max Capacity
      borderColor: 'rgba(255, 99, 132, 1)', // Red color for the max capacity line
      borderWidth: 2,
      pointRadius: 0, // No points at the data points
      fill: false, // Do not fill the area under this line
      tension: 0, // Straight line
      order: 1 // Draw this line above the actual data line
    }
  ];
</script>

<StaffCapacityGraph {labels} {datasets}/>

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
