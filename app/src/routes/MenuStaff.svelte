<script>
  import Container from "@shared/Container.svelte";
  import IconMenu from "@shared/PhippsyTech/svelte-iconmenu/IconMenu.svelte";
  import PayrollIcon from "@shared/PhippsyTech/svelte-ui/icons/Payroll.svelte";
  import InvoicingIcon from "@shared/PhippsyTech/svelte-ui/icons/Invoicing.svelte";
  import CarIcon from "@shared/PhippsyTech/svelte-ui/icons/Car.svelte";
  import AddressBookIcon from "@shared/PhippsyTech/svelte-ui/icons/AddressBook.svelte";
  import FileInvoiceDollarIcon from "@shared/PhippsyTech/svelte-ui/icons/FileInvoiceDollar.svelte";
  import FileSignatureIcon from "@shared/PhippsyTech/svelte-ui/icons/FileSignature.svelte";
  import FileContractIcon from "@shared/PhippsyTech/svelte-ui/icons/FileContract.svelte";
  import ReferralIcon from "@shared/PhippsyTech/svelte-ui/icons/Referral.svelte";
  import ListCheckIcon from "@shared/PhippsyTech/svelte-ui/icons/ListCheck.svelte";
  import DueReports from "@app/routes/Profile/Reports/Due.svelte";

  import {
    UserIcon,
    UsersIcon,
    UserGroupIcon,
    ClockIcon,
    ClipboardDocumentListIcon,
    DocumentIcon,
    DocumentTextIcon,
    CogIcon,
    CubeTransparentIcon,
    BriefcaseIcon,
    PowerIcon,
    BellIcon,
    HomeIcon,
    BookOpenIcon,
    PuzzlePieceIcon,
    ShieldCheckIcon,
  } from "heroicons-svelte/24/outline";
  import {
    UserStore,
    StafferStore,
    RolesStore,
    BreadcrumbStore,
  } from "@shared/stores.js";
  import Role from "@shared/Role.svelte";

  BreadcrumbStore.set({
    path: [],
  });

  let icons = [
    {
      url: "/clients",
      name: "Clients",
      icon: UserGroupIcon,
      role: ["house.lead", "therapist.lead", "admin"],
    },

    {
      url: "/staff",
      name: "Staff",
      icon: UserIcon,
      role: ["sil.lead", "admin"],
    },

    {
      url: "/billables",
      name: "Billables",
      icon: FileInvoiceDollarIcon,
      role: ["therapist", "admin"],
    },
    {
      url: "/trips",
      name: "Trips",
      icon: CarIcon,
      role: ["house", "therapist", "admin"],
    },

    // {
    //   url: "/directory",
    //   name: "Directory",
    //   icon: AddressBookIcon,
    //   role: ["house", "therapist", "admin"],
    // },

    {
      url: "/services",
      name: "Services",
      icon: BriefcaseIcon,
      role: ["admin"],
    },

    // {
    //   url: "/referrals",
    //   name: "Referrals",
    //   icon: ReferralIcon,
    //   role: ["admin"],
    // },

    {
      url: "/serviceagreements",
      name: "Service Agreements",
      icon: FileContractIcon,
      role: ["admin"],
    },

    // {
    //   url: "/signaturerequests",
    //   name: "Signature Requests",
    //   icon: FileSignatureIcon,
    //   role: ["admin"],
    // },

    {
      url: "/tasks",
      name: "Tasks",
      icon: ListCheckIcon,
      role: ["sysadmin"],
    },

    // {
    //   url: "/meetingnotes",
    //   name: "Meetings",
    //   icon: ClipboardDocumentListIcon,
    //   role: ["admin"],
    // },

    {
      url: "/accounts",
      name: "Accounts",
      icon: InvoicingIcon,
      role: ["accounts"],
    },
    {
      url: "/payroll",
      name: "Payroll",
      icon: PayrollIcon,
      role: ["payroll"],
    },

    {
      url: "/credentials",
      name: "Credentials",
      icon: ShieldCheckIcon,
      role: ["manager", "admin"],
    },
    {
      url: "/documents",
      name: "Documents",
      icon: DocumentIcon,
      role: ["manager", "admin"],
    },

    {
      url: "/reports",
      name: "Reports",
      icon: DocumentTextIcon,
      role: ["manager", "admin"],
    },
    {
      url: "/registers",
      name: "Registers",
      icon: BookOpenIcon,
      role: ["admin"],
    },

    // {
    //   url: "/sil",
    //   name: "SIL",
    //   icon: HomeIcon,
    //   role: ["house.lead"],
    // },

    // {
    //   url: "/therapy",
    //   name: "Therapy",
    //   icon: PuzzlePieceIcon,
    //   role: ["therapist.lead"],
    // },

    // {
    //   url: "/timelog",
    //   name: "Time Log",
    //   icon: ClockIcon,
    //   role: ["sil.admin"],
    // },

    { url: "/settings", name: "Settings", icon: CogIcon, role: ["admin"] },
    // {
    //   url: "/sysadmin",
    //   name: "Sys Admin",
    //   icon: CubeTransparentIcon,
    //   role: ["sysadmin"],
    // },

    { url: "/users", name: "Users", icon: UsersIcon, role: ["super"] },

    { url: "/logout", name: "Logout", icon: PowerIcon },
  ];
</script>

<div class="my-2">
  <IconMenu {icons} userRoles={$RolesStore} />
</div>
