<script>
import Badge from "@shared/PhippsyTech/Tailwind/App/Elements/Badge.svelte";
import { push } from "svelte-spa-router";

export let staffer = {};

function getFirstLetters(str) {
    return str
    .split(" ")
    .map((word) => word.charAt(0))
    .join("")
    .toUpperCase();
}

function handle(staff_id) {
    staff_id = staff_id;
    push(`/staff/${staff_id}`);
}
</script>

<a 
    class="relative flex items-center space-x-3 rounded-lg border {staffer.archived ==
    '1'
    ? 'border-red-600 bg-red-50'
    : 'border-indigo-100 bg-white'} pl-4 pr-2 py-3 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2
    hover:text-white
    hover:bg-indigo-600
    transition ease-in-out duration-150 hover:scale-105
    group
    text-gray-800
    cursor-pointer
    "
    on:click={() => handle(staffer.id)}>
    <div class="flex-shrink-0">

        <div
        class="flex h-10 w-10 rounded-full bg-indigo-600 text-white items-center justify-center text-xl font-bold"
        >
        {getFirstLetters(staffer.staff_name)}
        </div>
    </div>
    <div class="min-w-0 flex-1">
        <div class="text-sm font-medium">
            {staffer.staff_name}
        </div>
        {#if staffer.groups}
            <div class="flex flex-wrap gap-1 badge-container">
                {#each staffer.groups as group}<Badge label={group} />{/each}
            </div>
        {/if}
    </div>
</a>

<style>
.badge-container :global(.badge) {
    margin-left: 0;
}
</style>