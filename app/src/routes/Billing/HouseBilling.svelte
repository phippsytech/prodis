<script>
    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";
    import Footer from "@shared/Footer.svelte";
    import { jspa } from "@shared/jspa.js";
    import { formatDate } from "@shared/utilities";

    export let house_id;
    export let start_date;
    export let end_date;

    let billing = [];

    let services = [];

    let total = 0;

    onMount(async () => {
        await jspa("/Service", "listServices", {}).then((result) => {
            services = result.result;
        });
        await jspa("/SIL/Billing", "getBilling", {
            house_id: house_id,
            start_date: start_date,
            end_date: end_date,
        }).then((result) => {
            billing = result.result;

            billing.forEach((bill) => {
                const service = getService(bill.service_code);
                bill.billing_unit = service.billing_unit;
                bill.rate = service.rate;

                switch (bill.billing_unit) {
                    case "hour":
                        total += (bill.session_duration / 60) * bill.rate;
                        break;
                    case "kms":
                        total += bill.session_duration * bill.rate;
                        break;
                    default: // sleepover
                        total += bill.rate * 1; // The * 1 is needed.  It forces "rate" to be a number to avoid concatination.
                }
            });
        });
    });

    function getService(service_code) {
        const index = services.findIndex(
            (service) => service.code === service_code,
        );
        if (index !== -1) {
            return services[index];
        }
    }

    function formatCurrency(number) {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: "USD",
        }).format(number);
    }
</script>

<!-- <p>Total to be billed: {formatCurrency(total)}</p> -->
<!-- <div class="flow-root">
                  <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8"> -->
<table class="min-w-full divide-y divide-gray-300">
    <tbody class="divide-y divide-gray-200 bg-white">
        {#each billing as item}
            <tr class="">
                <td class="text-gray-900 pr-2 pt-1 pl-2">
                    <input
                        id="comments"
                        aria-describedby="comments-description"
                        name="comments"
                        type="checkbox"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                    />
                </td>
                <td
                    class="whitespace-nowrap py-2 text-sm font-medium text-gray-900 text-left pl-0"
                    >{formatDate(item.start_date, {
                        day: "numeric",
                        month: "short", // short, numeric
                        year: null,
                    })}</td
                >
                <td class="whitespace-nowrap px-3 py-2 text-sm text-gray-500"
                    >{item.service_code}
                </td>
                <td
                    class="whitespace-nowrap px-3 py-2 text-sm text-gray-500 text-center"
                >
                    {#if item.billing_unit == "hour"}
                        {item.session_duration / 60}
                    {:else if item.billing_unit == "kms"}
                        {item.session_duration}
                    {:else}
                        1
                    {/if}

                    <span class="text-xs"> x {formatCurrency(item.rate)}</span>
                </td>

                <td
                    class="whitespace-nowrap px-3 py-2 text-sm text-gray-500 text-right"
                >
                    {#if item.billing_unit == "hour"}
                        {formatCurrency(
                            (item.session_duration / 60) * item.rate,
                        )}
                    {:else if item.billing_unit == "kms"}
                        {formatCurrency(item.session_duration * item.rate)}
                    {:else}
                        {item.rate}
                    {/if}
                </td>
            </tr>
        {:else}
            loading...
        {/each}
    </tbody>
</table>
<!-- </div>
                  </div>
                </div>
               -->
