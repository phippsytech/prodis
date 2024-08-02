<script>
    import ClientSideBar from './SideBar/Client.svelte'
    import StaffSideBar from './SideBar/Staff.svelte'
    import AdminSideBar from './SideBar/Admin.svelte'

    const pathname = window.location.hash;
const segments = pathname.split('/');
const area = segments[1];


</script>
{#if area =="clients"}
    <ClientSideBar />
{/if}
{#if area =="staff"}
    <StaffSideBar />
{/if}

{#if area =="admin"}
    <AdminSideBar />
{/if}