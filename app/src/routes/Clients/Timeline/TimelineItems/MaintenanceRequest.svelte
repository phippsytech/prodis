<script>
        export let activity={};
</script>

<li class="flex items-start space-x-3">
    <a rel="noopener noreferrer" href="/#/" class="flex items-center h-10 text-sm hover:underline  opacity-60">{activity.time}</a>
    <div class="flex-1 ">
        <div class="flex items-center justify-between space-x-4 ">
            <a rel="noopener noreferrer" href="/#/form/{activity.id}" class="inline-flex items-center px-3 py-1 my-1 space-x-2 text-sm border rounded-full group">
                <span class="group-hover:underline ">{activity.report_type} : {activity.temperment}</span> 
            </a>
            <span class="text-xs whitespace-nowrap">{activity.staff_name}</span>
        </div>
        <div class="text-sm pl-4 mb-2">
            {activity.additional_details}
        </div>
    </div>
</li>