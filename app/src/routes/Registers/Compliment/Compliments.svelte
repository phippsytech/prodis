<script>
    import { push } from "svelte-spa-router";
    import { jspa } from "@shared/jspa.js";

    let compliments = {};

    jspa("/Register/Compliment", "listCompliment", {}).then((result) => {
        compliments = result.result
            .map((item) => ({
                label: `${item.name} - ${item.description}`,
                value: item.id,  
                staffId: item.staff_id,  
                staffName: item.staff_name,
                date: item.date,
                actionTaken: item.action_taken,
                acknowledgementDate: item.acknowledgement_date,
                status: item.status,
            }))
            .sort((a, b) => a.label.localeCompare(b.label));
    });

    console.log(compliments);
</script>

<!-- <div class="sm:flex sm:items-center mb-4">
    <div class="sm:flex-auto">
        <div
            class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
            style="color:#220055;"
        >
            Compliments Register
        </div>
    </div>
</div>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Acknowledged Compliments
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each compliments as compliment, index (compliment.id)}
        {#if compliment.status == "Acknowledged"}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {compliments.length - 1 == index ? 'rounded-b-lg' : ''} border-b border-gray-200 w-full"
            >
                <div class="justify-between flex">
                    <span class="text-xs">{compliment.date}</span>
                </div>
                <div>
                    <span class="font-bold">From:</span> {compliment.complimenter}<br />
                    <span class="font-bold">To:</span> {compliment.staff_name}<br />
                    <div class="mt-2">{compliment.description}</div>
                </div>
            </li>
        {/if}
    {/each}
</ul>

<h1 class="text-black text-1xl font-bold mt-0 mb-2 drop-shadow mb-2">
    Not Acknowledged Compliments
</h1>
<ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
    {#each compliments as compliment, index (compliment.id)}
        {#if compliment.status == "Not Acknowledged"}
            <li
                in:slide={{ duration: 200 }}
                out:slide|local={{ duration: 200 }}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {compliments.length - 1 == index ? 'rounded-b-lg' : ''} border-b border-gray-200 w-full"
            >
                <div class="justify-between flex">
                    <span class="text-xs">{compliment.date}</span>
                </div>
                <div>
                    <span class="font-bold">From:</span> {compliment.complimenter}<br />
                    <span class="font-bold">To:</span> {compliment.staff_name}<br />
                    <div class="mt-2">{compliment.description}</div>
                </div>
            </li>
        {/if}
    {/each}
</ul> -->
