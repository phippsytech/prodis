<script>

    export let staffer={}

    function getFirstLetters(str) {
        return str.split(' ').map(word => word.charAt(0)).join('').toUpperCase();
    }

</script>

<div class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-3 py-2 shadow-sm ">
    <div class="flex-shrink-0">
      <!-- <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt=""> -->

      <div class="flex h-10 w-10 rounded-full bg-indigo-600 text-white items-center justify-center text-xl font-bold group-hover:bg-white group-hover:text-indigo-600">{getFirstLetters(staffer.name)}</div>

    </div>
    <div class="min-w-0 flex-1">
        <span class="absolute inset-0" aria-hidden="true"></span>
        <p class="text-sm font-medium text-gray-900 flex items-center">
            {staffer.name} 
            <!-- <span class="inline-flex rounded-full bg-indigo-100 px-2 ml-1 text-xs font-normal text-indigo-700">Behavioural</span> -->
        </p>
        <!-- <p class="truncate text-xs text-gray-500 font-light">2 Days ago</p> -->
    </div>
  </div>  