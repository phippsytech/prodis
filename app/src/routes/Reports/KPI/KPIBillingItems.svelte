<script>
    import { jspa } from "@shared/jspa.js";
    import { slide } from "svelte/transition";
    import { quintOut } from "svelte/easing";
    import KPIViewDate from "./KPIViewDate.svelte";
    import KPIViewParticipant from "./KPIViewParticipant.svelte";

    import { formatDate } from "@shared/utilities.js";

    export let staff_id = [];
    export let start_date = [];
    export let end_date = [];

    let line_items = [];

    let view = "participant";

    let selected_client_id = null;

    jspa("/Billing", "listFilteredBillings", {
        staff_id: staff_id,
        start_date: start_date,
        end_date: end_date,
    }).then((result) => {
        line_items = result.result;
    });

    let claim_types = [
        { option: "Direct Service", value: "" },
        { option: "Cancellation", value: "CANC" },
        { option: "NDIA Required Report", value: "REPW" },
        { option: "Provider Travel", value: "TRAN" },
        {
            option: "Non-Face-to-Face Services",
            value: "NF2F",
        },
    ];

    function getOption(value) {
        if (value === null) value = "";
        let option = "";
        claim_types.forEach((item) => {
            if (item.value == value) {
                option = item.option;
            }
        });
        return option;
    }

    // function getItem(id){
    //     return line_items.find((item) => item.SessionId === id);
    // }

    let filtered_items = [];
    function selectClient(client_id) {
        selected_client_id = client_id;
        filtered_items = line_items.filter(
            (item) => item.client_id === client_id,
        );
    }

    let casenote = {};

    function getCasenote(casenote_id) {
        if (casenote.id == casenote_id) {
            casenote = {}; // this is like toggling the case note.
            return;
        }
        casenote = line_items.filter((item) => item.id === casenote_id)[0];
    }
</script>

<div
    transition:slide|global={{ duration: 250, easing: quintOut }}
    class="flex justify-end text-sm text-gray-500 gap-2 my-2 items-center"
>
    <span>View by</span>

    <div
        on:click={() => (view = "participant")}
        class="{view == 'participant'
            ? 'bg-indigo-600 text-white'
            : 'bg-gray-200 hover:bg-indigo-200 hover:text-indigo-600 cursor-pointer'} rounded p-1 flex items-center gap-1 px-2"
    >
        <svg
            class="h-5 w-5 flex-none shrink-0 inline cursor-pointer"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"
            ></path>
        </svg> Participant
    </div>

    <div
        on:click={() => (view = "date")}
        class="{view == 'date'
            ? 'bg-indigo-600 text-white'
            : 'bg-gray-200 hover:bg-indigo-200 hover:text-indigo-600 cursor-pointer'} rounded p-1 flex items-center gap-1 px-2"
    >
        <svg
            class="h-5 w-5 flex-none shrink-0 inline"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"
            ></path>
        </svg> Date
    </div>
</div>

{#if view == "date"}
    <KPIViewDate {line_items} />
{/if}

{#if view == "participant"}
    <KPIViewParticipant bind:line_items />
{/if}
