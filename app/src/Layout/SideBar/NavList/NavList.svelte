<script>
  import MenuItem from "../../MenuItem.svelte";
  import HouseNavList from "./HouseNavList.svelte";
  import TherapyNavList from "./TherapyNavList.svelte";
  import AdminNavList from "./AdminNavList.svelte";
  import AccountsNavList from "./AccountsNavList.svelte";

  const pathname = window.location.hash;
  const segments = pathname.split("/");
  const area = segments[1];

  let selected = area;
</script>

<li>
  <MenuItem label="Clients" href="/#/clients">
    <svg
      class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-indigo-600"
      fill="none"
      stroke="currentColor"
      stroke-width="1.5"
      viewBox="0 0 24 24"
      xmlns="http://www.w3.org/2000/svg"
      aria-hidden="true"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"
      ></path>
    </svg>
  </MenuItem>
</li>

<li>
  <MenuItem label="Notifications" href="/#/">
    <svg
      class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-indigo-600"
      fill="none"
      stroke="currentColor"
      stroke-width="1.5"
      viewBox="0 0 24 24"
      xmlns="http://www.w3.org/2000/svg"
      aria-hidden="true"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"
      ></path>
    </svg>
  </MenuItem>
</li>

<li>
  <HouseNavList bind:selected />
</li>
<li>
  <TherapyNavList bind:selected />
</li>
<li>
  <AccountsNavList bind:selected />
</li>
<li>
  <AdminNavList bind:selected />
</li>
