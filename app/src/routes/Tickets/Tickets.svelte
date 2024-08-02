<script>
    import { BreadcrumbStore } from "@shared/stores.js";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import RTE from "@shared/RTE/RTE.svelte";

    import { jspa } from "@shared/jspa.js";

    let tickets = [];

    function listTickets() {
        jspa("/Tickets", "listTickets", {}).then((result) => {
            tickets = result.result;
        });
    }

    listTickets();

    BreadcrumbStore.set({
        path: [{ url: null, name: "Tickets" }],
    });
</script>

<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1
                class="text-2xl font-fredoka-one-regular"
                style="color:#220055;"
            >
                Ticket System
            </h1>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <button
                type="button"
                class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >Open Ticket</button
            >
        </div>
    </div>
    <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead>
                        <tr>
                            <th
                                scope="col"
                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 lg:pl-8"
                                >Subject</th
                            >
                            <th
                                scope="col"
                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                >Last Reply</th
                            >
                            <th
                                scope="col"
                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                >Status</th
                            >
                            <th
                                scope="col"
                                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                                >Due</th
                            >
                            <th
                                scope="col"
                                class="relative py-3.5 pl-3 pr-4 sm:pr-6 lg:pr-8"
                            >
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        {#each tickets as ticket}
                            <tr>
                                <td
                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8"
                                    >{ticket.title}</td
                                >
                                <td
                                    class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                                    >Phippsy</td
                                >
                                <td
                                    class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                                    >Open</td
                                >
                                <td
                                    class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"
                                    >{ticket.created_at}</td
                                >
                                <td
                                    class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 lg:pr-8"
                                >
                                    <a
                                        href="/#/tickets/{ticket.id}"
                                        class="text-indigo-600 hover:text-indigo-900"
                                        >View</a
                                    >
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
