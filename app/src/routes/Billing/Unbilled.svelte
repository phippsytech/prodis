<script>
    import FloatingDateSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingDateSelect.svelte";

    import Container from "@shared/Container.svelte";
    import Footer from "@shared/Footer.svelte";
    import ClientItems from "./ClientItems.svelte";
    import {BreadcrumbStore} from "@shared/stores.js"

    const date = new Date();

    const start_year = new Date().getFullYear() - 1;
    const end_year = new Date().getFullYear() + 1;

    let period = {
        start_date: "2023-06-19",
        end_date: "2023-06-25",
    };


    BreadcrumbStore.set({path:[
        {name:"Billing",url:null}
    ]})


</script>

    <!-- I think overview should be dashboard throughout -->

<Container>
    <div class="text-sm mb-1 font-bold opacity-50">Period</div>
    <div class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2">
        <div class="flex-grow">
            <FloatingDateSelect
                label="Start Date"
                bind:value={period.start_date}
                {start_year}
                {end_year}
            />
        </div>
        <div class="flex-grow">
            <FloatingDateSelect
                label="End Date"
                bind:value={period.end_date}
                {start_year}
                {end_year}
            />
        </div>
    </div>
</Container>

<Container>
    <div class="text-sm mb-1 font-bold opacity-50">Selected Total</div>
    <p class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:tracking-tight mb-2">$54,639.27</p>    
</Container>

<!-- <Container>
    <div class="text-sm mb-1 font-bold opacity-50">Filter</div>
    </Container> -->

    <div class="flex justify-between mx-4 text-xs">
        <div>Select All</div>
        <div>Expand All</div>
    </div>


    <ClientItems />

    <ClientItems />

    <ClientItems />

    <ClientItems />

