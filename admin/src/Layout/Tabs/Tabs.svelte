<script>
    import { onMount, beforeUpdate } from "svelte";
    import { RolesStore } from "@shared/stores.js";
    import { haveCommonElements } from "@shared/utilities.js";
    import { getClient } from "@shared/api.js";

    let pathname = window.location.hash.slice(1);

    const updateHashValue = () => {
        pathname = window.location.hash.slice(1);
    };

    onMount(() => {
        updateHashValue();
        window.addEventListener("hashchange", updateHashValue);

        return () => {
            window.removeEventListener("hashchange", updateHashValue);
        };
    });

    beforeUpdate(() => {
        updateHashValue();
    });

    $: rolesStore = $RolesStore;

 
    let tabs = [];

    let routes = [
        {
            "/profile": (params) => {
                tabs = [
                    {
                        name: "Details",
                        url: "/profile/details",
                        active: true,
                        roles: ["house", "therapist", "admin"],
                    },
                    // {name: "Payroll", url: "/profile/payroll", active: true, roles: ['house','therapist','admin'] },
                    {
                        name: "Leave",
                        url: "/profile/leave",
                        active: true,
                        roles: ["house", "therapist", "admin"],
                    },
                    // {name: "Payslip", url: "/profile/payslip", active: true, roles: ['house','therapist','admin'] },
                ];
            },
        },

        {
            "/settings": (params) => {
                tabs = [
                    {
                        name: "Accounting",
                        url: "/settings/accounting",
                        active: true,
                        roles: ["house", "therapist", "admin"],
                    },
                    {
                        name: "Documents",
                        url: "/settings/documents",
                        active: true,
                        roles: ["house", "therapist", "admin"],
                    },

                    {
                        name: "NDIS Support Catalogue",
                        url: "/settings/supportcatalogue",
                        active: true,
                        roles: ["house", "therapist", "admin"],
                    },

                    {
                        name: "Staff",
                        url: "/settings/staff",
                        active: true,
                        roles: ["house", "therapist", "admin"],
                    },

                    {
                        name: "Reports",
                        url: "/settings/reports",
                        active: true,
                        roles: ["house", "therapist", "admin"],
                    },
                ];
            },
        },
    ];

    function checkUrlStartsWith(url, pathname) {
        if (url == pathname) return true;
        const url_segments = url.split("/");
        const pathname_segments = pathname.split("/");

        const diff = pathname_segments.length - url_segments.length;
        if (diff == 1) {
            if (pathname.startsWith(url)) {
                return true;
            }
        }
        return false;
    }

    $: handleRoute(pathname);

    function handleRoute(hash) {
        tabs = [];

        for (const route of routes) {
            const routePath = Object.keys(route)[0]; // Get the first (and only) key in the route object
            const pattern = new RegExp(
                `^${routePath.replace(/:\w+/g, "([^/]+)")}`,
            );
            const match = hash.match(pattern);
            if (match) {
                const params = {};
                const paramNames = routePath.match(/:(\w+)/g) || [];
                paramNames.forEach((param, index) => {
                    params[param.slice(1)] = match[index + 1];
                });
                return route[routePath](params); // Invoke the associated function
            }
        }

        return [];
    }
</script>

{#if tabs.length > 0}
    <div class="print:hidden h-12"></div>

    <!-- in:fly|global={{ y: -100, duration: 300 }} -->
    <div
        class="print:hidden fixed top-12 h-11 w-full z-10 bg-white border-b border-indigo-100"
        style="
        box-shadow: 0 10px 4px -4px #f8fafc;
      "
    >
        <div class="custom-scrollbar mx-auto max-w-7xl overflow-x-auto">
            <div class="block px-4 flex whitespace-nowrap">
                <div class="">
                    <nav class=" flex space-x-0" aria-label="Tabs">
                        {#each tabs as tab}
                            {#if tab.active && haveCommonElements(rolesStore, tab.roles)}
                                <a
                                    href="/#{tab.url}"
                                    class="whitespace-nowrap py-2 px-4 mt-2 text-sm {checkUrlStartsWith(
                                        tab.url,
                                        pathname,
                                    ) == true
                                        ? ' text-indigo-600 font-medium bg-slate-50 border border-indigo-100 border-b-0 rounded-t-md '
                                        : ' text-slate-500 hover:text-indigo-500 hover:border-indigo-200 hover:bg-radial-gradient '} "
                                    style="text-shadow: 1px 1px  #ffffff;"
                                >
                                    {tab.name}
                                    {#if tab.count}<span
                                            class="bg-slate-50 text-slate-900 ml-3 hidden rounded-full py-0.5 px-2.5 text-xs font-medium md:inline-block"
                                            >{tab.count}</span
                                        >{/if}
                                </a>
                            {/if}
                        {/each}
                    </nav>
                </div>
            </div>
        </div>
    </div>
{/if}

<style>
    .custom-scrollbar::-webkit-scrollbar {
        display: none; /* For Chrome, Safari, and Opera */
    }

    .custom-scrollbar {
        -ms-overflow-style: none; /* For Internet Explorer and Edge */
        scrollbar-width: none; /* For Firefox */
    }
</style>
