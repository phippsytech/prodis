<script>
import Container from "@shared/Container.svelte";
</script>

<Container>
    <div class="text-xs opacity-50 mb-2">Reimbursements</div>
    <div>
        <button
            on:click={() => push("/clients/add")}
            type="button"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
            >Add Reimbursement Line</button
        >
    </div>
</Container>