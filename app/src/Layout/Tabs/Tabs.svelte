<script>
    // import { fly } from "svelte/transition";
    import { onMount } from "svelte"; 
    import { RolesStore } from "@shared/stores.js";
    import { haveCommonElements } from "@shared/utilities.js";
    import { getClient } from "@shared/api.js";

    let pathname = window.location.hash.slice(1);

    const updateHashValue = () => {
        // hashValue = window.location.hash.substring(1);
        // pathname = window.location.hash.substring(1);
        pathname = window.location.hash.slice(1);
    };

    onMount(() => {
        updateHashValue();
        window.addEventListener("hashchange", updateHashValue);

        return () => {
            window.removeEventListener("hashchange", updateHashValue);
        };
    });

    $: rolesStore = $RolesStore;

    let client = {};
    let tabs = [];

    let routes = [
        {
            "/trips": (params) => {
                tabs = [
                    {
                        name: "Add Trip",
                        url: "/trips/add",
                        active: true,
                        roles: ["house", "therapist", "admin", "trip"],
                    },
                    {
                        name: "Trip History",
                        url: "/trips/history",
                        active: true,
                        roles: ["house", "therapist", "admin", "trip"],
                    },
                ];
            },
        },

        {
            "/clients/:client_id": async (params) => {
                client = await getClient(params.client_id);
                let sil_enabled = client.sil_enabled;

                tabs = [
                    {
                        name: "Details",
                        url: "/clients/" + params.client_id + "/details",
                        active: true,
                        roles: ["stakeholder", "house", "therapist", "admin"],
                    },
                    {
                        name: "Case Notes",
                        url: "/clients/" + params.client_id + "/casenotes",
                        active: true,
                        roles: ["therapist", "admin"],
                    },
                    {
                        name: "Timeline",
                        url: "/clients/" + params.client_id + "/timeline",
                        active: sil_enabled,
                        roles: ["timeline"],
                    },
                    {
                        name: "Forms",
                        url: "/clients/" + params.client_id + "/forms",
                        active: sil_enabled,
                        roles: ["forms"],
                    },
                    {
                        name: "Documents",
                        url: "/clients/" + params.client_id + "/documents",
                        active: true,
                        roles: ["house", "therapist", "admin"],
                    },
                    {
                        name: "Roster",
                        url: "/clients/" + params.client_id + "/roster",
                        active: sil_enabled,
                        roles: ["roster"],
                    },
                    {
                        name: "Staff",
                        url: "/clients/" + params.client_id + "/staff",
                        active: true,
                        roles: ["house.lead", "admin"],
                    },
                    {
                        name: "Billables",
                        url: "/clients/" + params.client_id + "/billables",
                        active: true,
                        roles: ["therapist", "accounts", "admin"],
                    },
                    {
                        name: "Invoices",
                        url: "/clients/" + params.client_id + "/invoices",
                        active: true,
                        roles: ["accounts", "admin"],
                    },
                    {
                        name: "Activity History",
                        url: "/clients/" + params.client_id + "/activities",
                        active: true,
                        roles: ["accounts", "admin"],
                    },
                    {
                        name: "Settings",
                        url: "/clients/" + params.client_id + "/settings",
                        active: true,
                        roles: ["admin"],
                    },
                ];
            },
        },

        {
            "/clients": (params) => (tabs = []),
        },

        {
            "/staff/add": (params) => (tabs = []),
        },

        {
            "/staff/:staff_id/payroll": (params) => {
                tabs = [
                    {
                        name: "Settings",
                        url: "/staff/" + params.staff_id + "/payroll/settings",
                        active: true,
                        roles: ["payroll"],
                    },
                    {
                        name: "Pay Template",
                        url:
                            "/staff/" +
                            params.staff_id +
                            "/payroll/paytemplate",
                        active: true,
                        roles: ["payroll"],
                    },
                ];
            },
        },

        {
            "/staff/:staff_id": (params) => {
                tabs = [
                    {
                        name: "Details",
                        url: "/staff/" + params.staff_id + "/details",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Payroll",
                        url: "/staff/" + params.staff_id + "/payroll",
                        active: true,
                        roles: ["sysadmin"],
                    },
                    {
                        name: "Credentials",
                        url: "/staff/" + params.staff_id + "/credentials",
                        active: true,
                        roles: ["admin", "sil.admin"],
                    },
                    {
                        name: "Documents",
                        url: "/staff/" + params.staff_id + "/documents",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Availabilitiy",
                        url: "/staff/" + params.staff_id + "/availability",
                        active: true,
                        roles: ["house.lead", "admin"],
                    },
                    {
                        name: "Clients",
                        url: "/staff/" + params.staff_id + "/clients",
                        active: true,
                        roles: ["house.lead", "admin"],
                    },
                    {
                        name: "Shifts",
                        url: "/staff/" + params.staff_id + "/shifts",
                        active: true,
                        roles: ["house.lead", "admin"],
                    },
                    {
                        name: "Schedule",
                        url: "/staff/" + params.staff_id + "/schedule",
                        active: true,
                        roles: ["house.lead", "admin"],
                    },
                    {
                        name: "Billables",
                        url: "/staff/" + params.staff_id + "/billables",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Invoiced",
                        url: "/staff/" + params.staff_id + "/invoiced",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Team",
                        url: "/staff/" + params.staff_id + "/team",
                        active: true,
                        roles: ["admin"],
                    },
                ];
            },
        },

        {
            "/billing": (params) => {
                tabs = [
                    {
                        name: "Billables",
                        url: "/billing/unbilled",
                        active: true,
                        roles: ["therapist", "accounts", "admin"],
                    },
                    {
                        name: "Invoiced",
                        url: "/billing/billed",
                        active: true,
                        roles: ["accounts", "admin"],
                    },
                    {
                        name: "SIL",
                        url: "/billing/sil",
                        active: true,
                        roles: ["super"],
                    },
                ];
            },
        },

        { "/accounts/invoice/:invoice_number": (params) => (tabs = []) },

        {
            "/accounts/invoiced/ndia/:anything": (params) =>
                (tabs = [
                    {
                        name: "Payment Request Status",
                        url: "/accounts/invoiced/ndia/paymentrequeststatus",
                        active: true,
                        roles: ["accounts", "admin"],
                    },
                    {
                        name: "Remittance",
                        url: "/accounts/invoiced/ndia/remittance",
                        active: true,
                        roles: ["accounts", "admin"],
                    },
                ]),
        },
        { "/accounts/invoiced/:invoice_batch": (params) => (tabs = []) },
        {
            "/accounts/invoiced": (params) =>
                (tabs = [
                    {
                        name: "Invoice Batches",
                        url: "/accounts/invoiced",
                        active: true,
                        roles: ["accounts", "admin"],
                    },
                    {
                        name: "NDIA",
                        url: "/accounts/invoiced/ndia",
                        active: true,
                        roles: ["accounts", "admin"],
                    },
                ]),
        },
        { "/accounts/planmanagers/:planmanager_id": (params) => (tabs = []) },

        {
            "/accounts": (params) => {
                tabs = [
                    {
                        name: "Billables",
                        url: "/accounts/billables",
                        active: true,
                        roles: ["accounts", "admin"],
                    },
                    {
                        name: "Errors",
                        url: "/accounts/errors",
                        active: true,
                        roles: ["accounts", "admin"],
                    },
                    {
                        name: "Invoiced",
                        url: "/accounts/invoiced",
                        active: true,
                        roles: ["accounts", "admin"],
                    },
                    {
                        name: "Plan Managers",
                        url: "/accounts/planmanagers",
                        active: true,
                        roles: ["accounts", "admin"],
                    },
                ];
            },
        },

        {
            "/registers/feedbacks": (params) => {
                tabs = [
                    {
                        name: "Feedback",
                        url: "/registers/feedbacks",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Risk",
                        url: "/registers/risks",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Conflict Of Interest",
                        url: "/registers/conflictofinterests",
                        active: true,
                        roles: ["admin"],
                    },
                ];
            },
        },

        {
            "/registers/risks": (params) => {
                tabs = [
                    {
                        name: "Feedback",
                        url: "/registers/feedbacks",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Risk",
                        url: "/registers/risks",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Conflict Of Interest",
                        url: "/registers/conflictofinterests",
                        active: true,
                        roles: ["admin"],
                    },
                ];
            },
        },

        {
            "/registers/conflictofinterests": (params) => {
                tabs = [
                    {
                        name: "Feedback",
                        url: "/registers/feedbacks",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Risk",
                        url: "/registers/risks",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Conflict Of Interest",
                        url: "/registers/conflictofinterests",
                        active: true,
                        roles: ["admin"],
                    },
                ];
            },
        },

        {
            "/payroll/leave": (params) => {
                tabs = [
                    // {name: "Payrun", url: "/payroll/payrun", active: true, roles: ['super'] },
                    {
                        name: "Requests",
                        url: "/payroll/leave/requests",
                        active: true,
                        roles: ["payroll"],
                    },
                    {
                        name: "Approved",
                        url: "/payroll/leave/approved",
                        active: true,
                        roles: ["payroll"],
                    },
                    {
                        name: "Processed",
                        url: "/payroll/leave/processed",
                        active: true,
                        roles: ["payroll"],
                    },
                ];
            },
        },

        {
            "/payroll/payruns/:payrun_id": (params) => {
                tabs = [
                    // {name: "Payrun", url: "/payroll/payrun", active: true, roles: ['super'] },
                    {
                        name: "Payrun",
                        url: "/payroll/payruns/" + params.payrun_id + "/payrun",
                        active: true,
                        roles: ["payroll"],
                    },
                    {
                        name: "Adjustments",
                        url:
                            "/payroll/payruns/" +
                            params.payrun_id +
                            "/adjustments",
                        active: true,
                        roles: ["payroll"],
                    },
                    // {
                    //     name: "Salary Packaging",
                    //     url:
                    //         "/payroll/payruns/" +
                    //         params.payrun_id +
                    //         "/salarypackaging",
                    //     active: true,
                    //     roles: ["payroll"],
                    // },
                ];
            },
        },

        {
            "/payroll": (params) => {
                tabs = [
                    // {name: "Payrun", url: "/payroll/payrun", active: true, roles: ['super'] },
                    {
                        name: "Payruns",
                        url: "/payroll/payruns",
                        active: true,
                        roles: ["payroll"],
                    },
                    {
                        name: "Pay Grades",
                        url: "/payroll/paygrades",
                        active: true,
                        roles: ["payroll"],
                    },
                    {
                        name: "Staff Pay Rates",
                        url: "/payroll/staff",
                        active: true,
                        roles: ["payroll"],
                    },
                    // {name: "Payrun Adjustments", url: "/payroll/payrun/adjustments", active: true, roles: ['payroll'] },
                    {
                        name: "Leave",
                        url: "/payroll/leave",
                        active: true,
                        roles: ["payroll"],
                    },
                    {
                        name: "Breakdown",
                        url: "/payroll/breakdown",
                        active: true,
                        roles: ["accounts"],
                    },
                    // {name: "Salary Packaging", url: "/payroll/salarypackaging", active: true, roles: ['payroll'] },
                ];
            },
        },

        {
            "/billables": (params) => {
                tabs = [
                    {
                        name: "Add Billable",
                        url: "/billables/add",
                        active: true,
                        roles: ["therapist", "admin"],
                    },
                    {
                        name: "Billables",
                        url: "/billables/unbilled",
                        active: true,
                        roles: ["therapist", "accounts", "admin"],
                    },
                    {
                        name: "Invoiced",
                        url: "/billables/billed",
                        active: true,
                        roles: ["therapist", "accounts", "admin"],
                    },
                ];
            },
        },

        {
            "/credentials": (params) => {
                tabs = [
                    {
                        name: "Credentials",
                        url: "/credentials/list",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Rules",
                        url: "/credentials/rules",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Verify",
                        url: "/credentials/verify",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Missing",
                        url: "/credentials/missing",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Expired",
                        url: "/credentials/expired",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Due",
                        url: "/credentials/due",
                        active: true,
                        roles: ["admin"],
                    },
                    {
                        name: "Report",
                        url: "/credentials/report",
                        active: true,
                        roles: ["admin"],
                    },
                ];
            },
        },

        // {
        //     "/documenttypes": (params) => {
        //         tabs = [
        //             {
        //                 name: "Types",
        //                 url: "/documenttypes/list",
        //                 active: true,
        //                 roles: ["admin"],
        //             },
        //             {
        //                 name: "Missing",
        //                 url: "/documenttypes/missing",
        //                 active: true,
        //                 roles: ["admin"],
        //             },
        //         ];
        //     },
        // },

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
