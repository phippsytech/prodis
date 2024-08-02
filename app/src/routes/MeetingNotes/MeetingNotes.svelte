<script>
    import { push } from "svelte-spa-router";

    import Container from "@shared/Container.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";
    import { DocumentTextIcon } from "heroicons-svelte/24/outline";
    import { slide } from "svelte/transition";
    import { jspa } from "@shared/jspa.js";

    let meeting_notes = [];

    BreadcrumbStore.set({ path: [{ name: "Meeting Notes" }] });

    jspa("/MeetingNote", "listMeetingNotes", {}).then((result) => {
        meeting_notes = result.result;
        // sort the meeting notes reverse chronologically
        meeting_notes.sort(function (a, b) {
            let aDateTime = Date.parse(a.meeting_date + " " + a.start_time);
            let bDateTime = Date.parse(b.meeting_date + " " + b.start_time);
            return bDateTime - aDateTime;
        });
    });
</script>

<Container>
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <div
                class="text-2xl sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
                style="color:#220055;"
            >
                Meeting Notes
            </div>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            <button
                on:click={() => push("/meetingnotes/add")}
                type="button"
                class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                >Add Meeting Note</button
            >
        </div>
    </div>

    <ul class="bg-white rounded-lg border border-gray-200 w-full text-gray-900">
        {#each meeting_notes as meeting_note, index (meeting_note.id)}
            <li
                in:slide={{ duration: 200 }}
                out:slide={{ duration: 200 }}
                on:click={() => push("/meetingnotes/" + meeting_note.id)}
                class="px-4 py-2 hover:bg-blue-700 hover:text-white focus:outline-none focus:ring-0 focus:bg-gray-200 focus:text-gray-600 transition duration-500 cursor-pointer {meeting_notes.length -
                    1 ==
                index
                    ? 'rounded-b-lg'
                    : ''}border-b border-gray-200 w-full {meeting_note.archived ==
                1
                    ? 'text-gray-400 cursor-default'
                    : ''}"
            >
                <span
                    ><DocumentTextIcon
                        class="w-6 h-6 inline"
                    />{meeting_note.purpose}
                    {meeting_note.meeting_date}</span
                >
                <span class="text-sm"
                    >{meeting_note.start_time} - {meeting_note.end_time}</span
                >
                <div class="text-xs ml-7">
                    Supervisor: {meeting_note.supervisor}
                </div>
                <div class="text-xs ml-7">
                    Location: {meeting_note.location}
                </div>
            </li>
        {:else}
            <li
                class="px-6 py-2 border-b border-gray-200 w-full rounded-t-lg rounded-b-lg text-gray-400 cursor-default"
            >
                No Meeting Notes have been added
            </li>
        {/each}
    </ul>
</Container>
