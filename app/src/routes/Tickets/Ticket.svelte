<script>
    import { BreadcrumbStore } from "@shared/stores.js";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";

    import Assignment from "./Activity/Assignment.svelte";
    import Comment from "./Activity/Comment.svelte";
    import AddComment from "./AddComment.svelte";
    import Time from "./Activity/Time.svelte";

    import { jspa } from "@shared/jspa.js";

    export let params;

    let ticket_id = params.ticket_id;

    let ticket = {
        assignees: [],
        activity: [],
    };

    function getTicket(ticket_id) {
        jspa("/Tickets", "getTicket", { id: ticket_id }).then((result) => {
            ticket = result.result;
        });
    }

    getTicket(ticket_id);

    BreadcrumbStore.set({
        path: [{ url: null, name: "Tickets" }],
    });
</script>

<div class="flex min-h-full">
    <div class="flex w-0 flex-1 flex-col">
        <main class="flex-1">
            <div class="py-4">
                <div class="px-4">
                    <div class="">
                        <div>
                            <div>
                                <div
                                    class="md:flex md:items-center md:justify-between md:space-x-4 xl:border-b xl:pb-6"
                                >
                                    <div>
                                        <div
                                            class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
                                            style="color:#220055;"
                                        >
                                            {ticket.title}
                                        </div>

                                        <p class="mt-2 text-sm text-gray-500">
                                            #{ticket.id} opened by
                                            <a
                                                href="/#/"
                                                class="font-medium text-gray-900"
                                                >{ticket.user_name}</a
                                            >
                                            <!-- in
                                            <a
                                                href="/#/"
                                                class="font-medium text-gray-900"
                                                >Customer Portal</a
                                            > -->
                                        </p>
                                    </div>
                                </div>
                                <aside class="my-4">
                                    <h2 class="sr-only">Details</h2>
                                    <div
                                        class="flex flex-col space-y-1 sm:flex-row sm:justify-between"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <svg
                                                class="h-5 w-5 text-green-500"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M14.5 1A4.5 4.5 0 0010 5.5V9H3a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-1.5V5.5a3 3 0 116 0v2.75a.75.75 0 001.5 0V5.5A4.5 4.5 0 0014.5 1z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            <span
                                                class="text-sm font-medium text-green-700"
                                                >Open Issue</span
                                            >
                                        </div>
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <svg
                                                class="h-5 w-5 text-gray-400"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10 2c-2.236 0-4.43.18-6.57.524C1.993 2.755 1 4.014 1 5.426v5.148c0 1.413.993 2.67 2.43 2.902.848.137 1.705.248 2.57.331v3.443a.75.75 0 001.28.53l3.58-3.579a.78.78 0 01.527-.224 41.202 41.202 0 005.183-.5c1.437-.232 2.43-1.49 2.43-2.903V5.426c0-1.413-.993-2.67-2.43-2.902A41.289 41.289 0 0010 2zm0 7a1 1 0 100-2 1 1 0 000 2zM8 8a1 1 0 11-2 0 1 1 0 012 0zm5 1a1 1 0 100-2 1 1 0 000 2z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            <span
                                                class="text-sm font-medium text-gray-900"
                                                >4 comments</span
                                            >
                                        </div>
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <svg
                                                class="h-5 w-5 text-gray-400"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            <span
                                                class="text-sm font-medium text-gray-900"
                                                >Created on <time
                                                    datetime={ticket.created_at}
                                                    >{ticket.created_at}</time
                                                ></span
                                            >
                                        </div>
                                    </div>
                                    {#if ticket.assignees.length > 0}
                                        <div
                                            class="mt-4 border-b border-t border-gray-200 py-2"
                                        >
                                            <div>
                                                <h2
                                                    class="text-sm font-medium text-gray-500"
                                                >
                                                    Assignees
                                                </h2>
                                                <ul
                                                    role="list"
                                                    class="mt-1 space-y-1"
                                                >
                                                    {#each ticket.assignees as assignee}
                                                        <li
                                                            class="flex justify-start"
                                                        >
                                                            <a
                                                                href="/#/"
                                                                class="flex items-center space-x-3"
                                                            >
                                                                <div
                                                                    class="text-sm font-medium text-gray-900"
                                                                >
                                                                    {assignee.name}
                                                                </div>
                                                            </a>
                                                        </li>
                                                    {/each}
                                                </ul>
                                            </div>
                                        </div>
                                    {/if}
                                </aside>
                                <div class="py-1">
                                    <h2 class="sr-only">Description</h2>
                                    <div class="prose max-w-none">
                                        {@html ticket.content}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <section
                            aria-labelledby="activity-title"
                            class="mt-8 xl:mt-10"
                        >
                            <div>
                                <div class="divide-y divide-gray-200">
                                    <div class="pb-4">
                                        <h2
                                            id="activity-title"
                                            class="text-lg font-medium text-gray-900"
                                        >
                                            Activity
                                        </h2>
                                    </div>

                                    <div class="pt-6">
                                        <div class="flow-root">
                                            <ul role="list" class="-mb-8">
                                                <Comment />
                                                <Assignment />
                                                <Time />
                                            </ul>
                                        </div>

                                        <div class="mt-6">
                                            <AddComment bind:ticket_id />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
