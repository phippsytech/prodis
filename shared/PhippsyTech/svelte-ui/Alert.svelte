<script>
    export let type="primary";

    let background="bg-blue-100";
    let color="text-blue-700";

    switch(type){
        case "primary":
            background="bg-blue-100";
            color="text-blue-700";
        break;
        case "secondary":
            background="bg-purple-100";
            color="text-purple-700";
        break;
        case "success":
        background="bg-green-100";
            color="text-green-700";
        break;
        case "danger":
        background="bg-red-100";
            color="text-red-700";
        break;
        case "warning":
        background="bg-yellow-100";
            color="text-yellow-700";
        break;
        case "indigo":
        background="bg-indigo-100";
            color="text-indigo-700";
        break;
        case "light":
        background="bg-gray-50";
            color="text-gray-500";
        break;
        case "dark":
        background="bg-gray-300";
            color="text-gray-800";
        break;
    }


</script>




<div class="alert {background} rounded-lg py-5 px-6 mb-3 text-base {color} inline-flex items-center w-full alert-dismissible fade show" role="alert">
    <slot />
    <!-- <button type="button" class="btn-close box-content w-4 h-4 p-1 ml-auto text-yellow-900 border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-yellow-900 hover:opacity-75 hover:no-underline" data-bs-dismiss="alert" aria-label="Close"></button> -->

  </div>
  