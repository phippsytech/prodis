<script>
    export let value = false;
    export let on_label;
    export let off_label;
</script>

<div class="form-check form-switch">
    <input on:click class="form-check-input appearance-none w-9 -ml-10 rounded-full float-left h-5 align-top bg-white bg-no-repeat bg-contain bg-gray-300 focus:outline-none cursor-pointer shadow-sm" type="checkbox" role="switch" id="flexSwitchCheckDefault" bind:checked={value}>
    <label class="form-check-label inline-block text-gray-800 {(value)?'font-bold':''}" for="flexSwitchCheckDefault">{#if value}{on_label}{:else}{off_label}{/if}</label>
</div>

