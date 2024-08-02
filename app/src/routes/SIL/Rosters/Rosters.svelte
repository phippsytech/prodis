<script>
    // I am thinking of replacing SvelteGantt with https://bryntum.com/ if I am unable to build something usable myself.

    import {
        SvelteGantt,
        SvelteGanttDependencies,
        SvelteGanttExternal,
        SvelteGanttTable,
    } from "svelte-gantt";
    import { onMount, getContext } from "svelte";
    import { time, createDateTime } from "./utils";
    import { jspa } from "@shared/jspa";

    let currentStart = time("0:00");
    let currentEnd = time("24:00");

    currentStart = createDateTime("2024-01-29", "00:00");
    currentEnd = createDateTime("2024-02-04", "00:00");

    const colors = ["blue", "green", "orange"];

    const timeRanges = [
        // {
        //     id: 0,
        //     from: time('6:00'),
        //     to: time('20:00'),
        //     classes: "testthing",
        //     label: 'Day',
        //     resizable: false,
        // },
        // {
        //     id: 1,
        //     from: time('20:00'),
        //     to: time('24:00'),
        //     classes: null,
        //     label: 'Evening',
        //     resizable: false,
        // },
        // {
        //     id: 3,
        //     from: time('22:00'),
        //     to: time('06:00'),
        //     classes: null,
        //     label: 'Passive',
        //     resizable: false,
        // }
    ];

    export const data = {
        rows: [
            {
                id: 1,
                label: "Logan Walker",
            },
            {
                id: 2,
                label: "Cristian Salisbury",
            },
            {
                id: 3,
                label: "Rhys Tallowin",
            },
            {
                id: 4,
                label: "Alex Haggon",
            },
            {
                id: 5,
                label: "Mia Stoegger",
            },
            {
                id: 6,
                label: "Jamie Stoegger",
            },
            {
                id: 7,
                label: "Kaylah Dyett",
            },
        ],
        // tasks: [{
        //     "id": 1,
        //     "resourceId": 1,
        //     "label": "Marc T",
        //     "from": createDateTime("2023-10-22", "00:00"),
        //     "to": createDateTime("2023-10-22", "06:00"),
        //     "classes": "last-in-a-group",
        //     "height":50,
        // }, {
        //     "id": 2,
        //     "resourceId": 3,
        //     "label": "Dean R",
        //     "from": time("10:00"),
        //     "to": time("24:30"),
        //     "classes": "last-in-a-group"
        // }]
    };

    let options = {
        // dateAdapter: new MomentSvelteGanttDateAdapter(moment),
        layout: "pack",
        rows: data.rows,
        tasks: data.tasks,
        timeRanges,
        columnUnit: "minute",
        columnOffset: 15,
        columnStrokeColor: "#fafafa",
        columnStrokeWidth: 1,
        useCanvasColumns: false,
        magnetOffset: 15,
        rowHeight: 80,
        rowPadding: 2,
        headers: [
            { unit: "day", format: "DD.MM.YYYY", sticky: true },
            { unit: "hour", format: "H:mm", sticky: true },
        ],
        fitWidth: true,
        // minWidth: 800,
        minWidth: 7000,
        from: currentStart,
        to: currentEnd,
        tableHeaders: [
            {
                title: "Participants",
                property: "label",
                width: 140,
                type: "tree",
            },
        ],
        tableWidth: 140,
        ganttTableModules: [SvelteGanttTable],
        highlightedDurations: {
            unit: "h",
            fractions: [6, 8, 10, 12, 14, 16, 18, 20, 22],
        },
        highlightColor: "#f0f0f0",
    };

    let gantt;

    function createRosterTime(dateString, timeString) {
        return new Date(`${dateString}T${timeString}`);
    }

    function reformatResponse(resource_id, response) {
        return response.map((item, index) => ({
            id: item.id,
            resourceId: resource_id,
            label: item.title,
            from: createRosterTime(item.start_date, item.start_time),
            to: createRosterTime(item.end_date, item.end_time),
            classes: getColourClass(item.backgroundColor),
        }));
    }

    function getColourClass(color) {
        if (color === "#ff0000") {
            return "red";
        }
        return "indigo";
    }

    onMount(async () => {
        gantt = new SvelteGantt({
            target: document.getElementById("example-gantt"),
            props: options,
        });

        getParticipantRoster(332, 1);
        getParticipantRoster(580, 2);
        getParticipantRoster(424, 3);
        getParticipantRoster(84, 4);
        getParticipantRoster(671, 5);
        getParticipantRoster(670, 6);
        getParticipantRoster(672, 7);
    });

    function getParticipantRoster(participant_id, resource_id) {
        jspa("/SIL/House/Roster", "getRoster", {
            client_id: participant_id,
            info: {
                start: "2024-01-29T13:00:00.000Z",
                end: "2024-02-04T13:00:00.000Z",
                startStr: "2024-01-29T00:00:00",
                endStr: "2024-02-04T00:00:00",
            },
            editable: true,
        })
            .then((result) => {
                gantt.updateTasks(reformatResponse(resource_id, result.result));
            })
            .catch((error) => {
                console.error("Error fetching events:", error);
            });
    }

    function onChangeOptions(event) {
        const opts = event.detail;
        Object.assign(options, opts);
        gantt.$set(options);
    }
</script>

<div class="container">
    <div id="example-gantt"></div>
</div>

<style>
    #example-gantt {
        flex-grow: 1;
        overflow: auto;
    }

    :global(.testthing) {
        background: #00ff0011;
        background-image: none !important;
    }

    :global(.sg-row) {
        border-bottom: 1px solid #e0e0e0;
    }

    :global(.red) {
        background: red;
    }
    :global(.indigo) {
        background: #4f46e5ff;
        border: 2px solid white;
    }

    /* :global(.sg-task) {
        background: #4f46e5ff;
    }

    :global(.sg-task:hover) {
        background: #4f46e5cc;
    } */
</style>
