<script>
    
    import AdminNavList from "./NavList/AdminNavList.svelte";

    const pathname = window.location.hash;
    const segments = pathname.split('/');
    const client_id = segments[2];
    
</script>


    
    <div
        class="flex grow flex-col gap-y-2 overflow-y-auto border-r border-gray-200 bg-white"
    >

     
            <ul role="list" class="divide-y divide-gray-100 bg-white">
                <li
                    class="relative flex justify-between py-2 text-gray-700"
                >
                    <div class="min-w-0 flex-auto">

                        <div class="flex justify-between items-center px-5">
                            <h2 class="mt-2 text-md font-bold leading-7 text-gray-900 truncate tracking-tight mt-0 mb-2 "
                            >Admin</h2>
                            
                        </div>


                        <AdminNavList />
                    </div>
                </li>
            </ul>
        
</div>
