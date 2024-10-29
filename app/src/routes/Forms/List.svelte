<script>
	import { getStaffer } from '@shared/api.js';
    import { onMount } from "svelte";
    import Container from "@shared/Container.svelte";
    import { jspa } from "@shared/jspa.js";
    import { getDaysUntilDate, formatDateTime, formatPrettyName } from "@shared/utilities.js";
    import { BreadcrumbStore, StafferStore } from "@shared/stores.js";
    import { push } from "svelte-spa-router";
    import { formatDate } from "@shared/utilities.js";

    document.title = "Forms";
    BreadcrumbStore.set({
        path: [{ url: "/forms", name: "Forms" }]
    });

    let showArchived = false;
    let formList = [];
    let search = "";

    // static forms data
    let forms = [
        {
            id: 1,
            title: "Form 1",
            creator_name: "John Doe",
            created_at: "2022-01-01T12:00:00Z",
            description: "Lorem Ipsum",
            archived: 0,
            schemas: [
                {
                    type: "html",
                    html: "some html here"
                },
                {
                    type: "text",      
                    key: "email",
                    label: "Email Address",
                    placeholder: "someone@emailaddress.com"
                },
                {
                    type: "textarea",
                    label: "Description",
                    placeholder: "Describe the thing"
                }
            ]
        },
        {
            id: 2,
            title: "Form 2",
            creator_name: "John Doe",
            created_at: "2022-01-01T12:00:00Z",
            description: "Lorem Ipsum",
            archived: 0,
            schemas: [
                {
                    type: "html",
                    html: "some html here"
                },
                {
                    type: "text",      
                    key: "email",
                    label: "Email Address",
                    placeholder: "someone@emailaddress.com"
                },
                {
                    type: "textarea",
                    label: "Description",
                    placeholder: "Describe the thing"
                },
                {
                    type: "textarea",
                    label: "Description",
                    placeholder: "Describe the thing"
                },
                {
                    type: "textarea",
                    label: "Description",
                    placeholder: "Describe the thing"
                }
            ]
        },
        {
            id: 3,
            title: "Form 3",
            creator_name: "John Doe",
            created_at: "2022-01-01T12:00:00Z",
            description: "Lorem Ipsum",
            archived: 1,
            schemas: [
                {
                    type: "html",
                    html: "some html here"
                },
                {
                    type: "text",      
                    key: "email",
                    label: "Email Address",
                    placeholder: "someone@emailaddress.com"
                },
                {
                    type: "textarea",
                    label: "Description",
                    placeholder: "Describe the thing"
                }
            ]
        },
    ];

    $: {
        formList = forms.filter(
            (form) =>
            form.title.toLowerCase().includes(search.toLowerCase()) == true,
        );

        if (!showArchived) {
            formList = formList?.filter(
                (form) => form.archived != 1,
            );
        }
    }


</script>
    
<Container>
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <h1
                class="text-2xl font-fredoka-one-regular"
                style="color:#220055;"
            >
                Forms
            </h1>
        </div>
    </div>
    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-2">
        <div class="relative flex flex-1">
            <label for="search-field" class="sr-only">Search</label>
            <svg
                class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
            >
                <path
                    fill-rule="evenodd"
                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                    clip-rule="evenodd"
                />
            </svg>
            <input
                bind:value={search}
                id="search-field"
                class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm outline-none"
                placeholder="search by form name..."
                type="search"
                name="search"
            />
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <button
                type="button"
                class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                on:click={() => push("/forms/add")}
                >Create Form</button
            >
        </div>
    </div>
    <label class="text-xs text-gray-400 flex justify-end mt-2">
        <input type="checkbox" bind:checked={showArchived} class="mr-2" />
        Include archived
    </label>
    <table class="w-full">
        <tr class="border-b-2">
            <th class="text-left">Title</th>
            <th class="text-left">Description</th>
            <th class="text-left">Schema Count</th>
            <th class="text-left">Created By</th>
            <th class="text-left">Created Date</th>
            <th class="text-left">Action</th>
        </tr>

        {#each formList as form, index (index)}
            <tr 
                class="border-b focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500"
                style="opacity: {form.archived == 1 ? 0.5 : 1}"
                >
                <td class="font-semibold p-2">{form.title}</td>
                <td>{form.description}</td>
                <td>{form.schemas.length}</td>
                <td>{form.creator_name}</td>
                <td>{form.created_at ? formatDate(form.created_at) : ''}</td>
                <td>
                    <div class="flex gap-2 items-center">
                        <div class="cursor-pointer" href="#">View</div>
                        <div class="cursor-pointer" on:click={() => push("/forms/" + form.id)}>Edit</div>
                    </div>
                </td>
            </tr>
        {/each}
    </table>
</Container>
