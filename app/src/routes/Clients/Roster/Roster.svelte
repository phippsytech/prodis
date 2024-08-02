<script>
    import Calendar from "@event-calendar/core";
    import TimeGrid from "@event-calendar/time-grid";
    import DayGrid from "@event-calendar/day-grid";
    import Interaction from "@event-calendar/interaction";
    import ShiftForm from "./ShiftForm.svelte";
    import PasteWidget from "./PasteWidget.svelte";
    import DeleteWidget from "./DeleteWidget.svelte";

    import { jspa } from "@shared/jspa.js";
    import { getClient } from "@shared/api.js";
    import { onMount, onDestroy } from "svelte";
    import { BreadcrumbStore, RolesStore } from "@shared/stores.js";
    import {
        ModalStore,
        ContextMenuStore,
        SlideOverStore,
        TooltipStore,
    } from "@app/Overlays/stores.js";

    import { haveCommonElements } from "@shared/utilities.js";

    export let params;
    let closeTooltipTimeoutId;
    let roster_editable = false;

    let isSmallScreen = window.innerWidth < 768;
    let shifts = [];
    let staff = [];
    let ec;
    let plugins = [DayGrid, TimeGrid, Interaction];
    let week_start_date = null; // = new Date('2023-05-01T00:00:00');
    let shift_data = [];
    let options;
    let client_id = params.client_id;
    let client = {};

    $: rolesStore = $RolesStore;
    $: slideoverStore = $SlideOverStore;

    function handleResize() {
        isSmallScreen = window.innerWidth < 768;
    }

    function handleShiftUpdate() {
        ec.refetchEvents();
    }

    function getDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0"); // Months are zero-indexed, so add 1
        const day = String(date.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    }

    function get24HourTime(date) {
        const hours = String(date.getHours()).padStart(2, "0");
        const minutes = String(date.getMinutes()).padStart(2, "0");
        return `${hours}:${minutes}`;
    }

    function addShift(shift) {
        // shift.bill_kms=true;
        jspa("/SIL/House/Roster", "addShift", shift)
            .then((result) => {
                ec.unselect();
                editShift(result.result.result.id);
                ec.refetchEvents();
            })
            .catch((error) => {
                ec.unselect();
                alert(error.error_message);
            });
    }

    function editShift(shift_id) {
        jspa("/SIL/House/Roster", "getShift", { id: shift_id }).then(
            (result) => {
                let shift = result.result;
                shift.client_id = client_id;
                if (shift.do_not_bill == "1") shift.do_not_bill = true;
                if (shift.do_not_bill == "0") shift.do_not_bill = false;

                // if(shift.bill_kms == "1") shift.bill_kms = true;
                // if(shift.bill_kms == "0") shift.bill_kms = false;

                if (shift.passive == "1") shift.passive = true;
                if (shift.passive == "0") shift.passive = false;

                // ModalStore.set({
                //     label:"Edit Shift",
                //     show: true,
                //     props: shift,
                //     component:ShiftForm,
                //     action_label:"Update",
                //     action:()=>updateShift(shift),
                //     delete: ()=>deleteShift(shift)
                // });

                SlideOverStore.set({
                    label: "Edit Shift",
                    show: true,
                    props: shift,
                    component: ShiftForm,
                    action_label: "Update",
                    action: () => updateShift(shift),
                    delete: () => deleteShift(shift),
                });
            },
        );
    }

    function deleteShift(shift) {
        jspa("/SIL/House/Roster", "deleteShift", { id: shift.id }).then(
            (result) => {
                ec.refetchEvents();
            },
        );
    }

    // when the shift is moved, this updates the shift.
    function moveShift(event) {
        let shift = {};
        shift.id = event.id;
        shift.start_date = getDate(event.start);
        shift.start_time = get24HourTime(event.start);
        shift.end_date = getDate(event.end);
        shift.end_time = get24HourTime(event.end);
        updateShift(shift);
    }

    // when the shift is updated from the Modal, this saves it.
    function updateShift(shift) {
        jspa("/SIL/House/Roster", "updateShift", shift).then((result) => {
            ec.refetchEvents();
        });
    }

    onMount(async () => {
        client = await getClient(client_id);

        window.addEventListener("resize", handleResize);

        BreadcrumbStore.set({
            path: [
                { name: "Clients", url: "/clients" },
                { name: client.name, url: "/clients/" + client.id },
            ],
        });

        options = {
            allDaySlot: false,
            slotDuration: "00:15:00",
            slotHeight: 7,
            slotEventOverlap: false,
            eventStartEditable: false,
            eventDurationEditable: false,
            editable: roster_editable,
            firstDay: 1,
            nowIndicator: true,
            selectable: roster_editable,
            dateClick: function (info) {
                let myDiv = document.getElementById("calendar");
                const { top } = myDiv.getBoundingClientRect();

                ContextMenuStore.set({
                    show: true,
                    title: "Add Shift",
                    options: [
                        { name: "3 hours", value: 3 },
                        { name: "7.5 hour", value: 7.5 },
                        { name: "8 hour", value: 8 },
                        { name: "10 hours", value: 10 },
                    ],
                    select: (value) => {
                        let start_date = new Date(info.date);
                        let end_date = new Date(info.date);
                        end_date.setHours(end_date.getHours() + value);
                        addShift({
                            client_id: client.id,
                            start_date: getDate(start_date),
                            start_time: get24HourTime(start_date),
                            end_date: getDate(end_date),
                            end_time: get24HourTime(end_date),
                        });
                    },

                    x: info.jsEvent.clientX,
                    y: info.jsEvent.clientY,
                });
            },

            // eventMouseEnter: function(info) {
            //         clearTimeout(closeTooltipTimeoutId);
            //         console.log(info.event)
            //         let rect = info.el.getBoundingClientRect();
            //         TooltipStore.set({
            //             show: true,
            //             title: info.event.titleHTML,
            //             backgroundColor: info.event.backgroundColor,

            //             select: ()=>editShift(info.event.id),
            //             x: rect.left + window.scrollX,
            //             y: rect.top + window.scrollY,
            //             width: rect.width,
            //             height: rect.height
            //         })
            //     },

            eventClick: function (info) {
                if (info.event.editable) {
                    editShift(info.event.id);
                }
            },
            eventResize: function (info) {
                moveShift(info.event);
            },
            eventDrop: function (info) {
                moveShift(info.event);
            },

            select: function (info) {
                if (roster_editable) {
                    addShift({
                        client_id: client.id,
                        start_date: getDate(info.start),
                        start_time: get24HourTime(info.start),
                        end_date: getDate(info.end),
                        end_time: get24HourTime(info.end),
                    });
                }
            },
            datesSet: function (info) {
                const date = info.start;

                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, "0");
                const day = String(date.getDate()).padStart(2, "0");
                week_start_date = `${year}-${month}-${day}`;
            },
            eventSources: [
                {
                    events: function (info, successCallback, failureCallback) {
                        jspa("/SIL/House/Roster", "getRoster", {
                            client_id: client.id,
                            info: info,
                            editable: roster_editable,
                        })
                            .then((result) => {
                                let data = result.result;
                                shift_data = data;
                                successCallback(data);
                            })
                            .catch((error) => {
                                console.error("Error fetching events:", error);
                                failureCallback(error);
                            });
                    },
                },
            ],
        };

        fitToScreen(isSmallScreen);

        if (haveCommonElements(rolesStore, ["house.lead"])) {
            roster_editable = true;
            options.editable = roster_editable;
            options.selectable = roster_editable;

            ec.setOption("editable", roster_editable);
            ec.setOption("selectable", roster_editable);
        }
    });

    function fitToScreen(isSmallScreen) {
        if (ec) {
            if (isSmallScreen) {
                ec.setOption("view", "timeGridDay");
                ec.setOption("headerToolbar", {
                    start: "title",
                    center: "",
                    end: "prev,next,today",
                });
            } else {
                ec.setOption("view", "timeGridWeek");
                ec.setOption("headerToolbar", {
                    start: "title",
                    center: "",
                    end: "timeGridWeek,timeGridDay, prev,next,today",
                });
            }
        }
    }

    $: {
        fitToScreen(isSmallScreen);
    }

    function handlePaste() {
        ec.refetchEvents();
    }

    function handleDelete() {
        ec.refetchEvents();
    }
</script>

{#if haveCommonElements(rolesStore, ["house.lead"])}
    {#if !isSmallScreen}
        <PasteWidget
            bind:client_id
            bind:week_start_date
            bind:shifts={shift_data}
            on:paste={handlePaste}
        />
    {/if}
{/if}
<!-- <Container> -->

{#if haveCommonElements(rolesStore, ["roster"])}
    {#if !haveCommonElements(rolesStore, ["house.lead"])}
        <div class="rounded-md bg-blue-50 p-4 mb-2">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg
                        class="h-5 w-5 text-blue-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <div class="ml-3 flex-1 md:flex md:justify-between">
                    <p class="text-sm text-blue-700">
                        <b>Roster is in read-only mode.</b> You cannot edit the roster
                        using a shared login.
                    </p>
                </div>
            </div>
        </div>
    {/if}
{/if}

{#if haveCommonElements(rolesStore, ["roster"])}
    <div id="calendar">
        <Calendar bind:this={ec} {plugins} {options} />
    </div>
{/if}

{#if haveCommonElements(rolesStore, ["house.lead"])}
    {#if !isSmallScreen}
        <div class="py-8">
            <DeleteWidget
                bind:client_id
                bind:week_start_date
                on:delete={handleDelete}
            />
        </div>
    {/if}
{/if}
<!-- </Container> -->

<style>
    :global(.ec-time, .ec-line) {
        height: 7px; /* override this value */
    }
    :global(.ec-event) {
        border-radius: 4px;
        border: 1px solid #ffffffcc;
    }
    :global(.ec-events) {
        margin: 0 1px;
    }
    :global(.ec-day.ec-today) {
        background: #fff;
        /* border-left:2px solid rgb(235 79 39); */
    }
    :global(.ec-now-indicator) {
        border-top: 2px solid rgb(235 79 39);
    }
    :global(.ec-now-indicator::before) {
        background: rgb(235 79 39);
    }
    :global(.ec-event-time) {
        font-size: 0.7rem;
        color: #000;
    }
    :global(.ec-event-title) {
        font-size: 0.8rem;
        font-weight: bold;
        color: #000;
    }
    :global(.ec-time:nth-child(4n)) {
        visibility: hidden;
    }
    :global(.ec-line:nth-child(4n + 5):after) {
        border-bottom: 1px solid #aaa;
    }
</style>
