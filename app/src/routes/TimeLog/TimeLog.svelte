<script>
    import Calendar from "@event-calendar/core";
    import TimeGrid from "@event-calendar/time-grid";
    import DayGrid from "@event-calendar/day-grid";
    import Interaction from "@event-calendar/interaction";
    import TimeForm from "./TimeForm.svelte";

    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { jspa } from "@shared/jspa.js";
    import { onMount } from "svelte";
    import {
        BreadcrumbStore,
        RolesStore,
        StafferStore,
    } from "@shared/stores.js";
    import { ModalStore } from "@app/Overlays/stores.js";
    import { haveCommonElements } from "@shared/utilities.js";

    $: rolesStore = $RolesStore;

    let timelog_editable = false;
    let isSmallScreen = window.innerWidth < 768;
    let ec;
    let plugins = [DayGrid, TimeGrid, Interaction];
    let week_start_date = null;
    let time_data = [];
    let options;
    let staff_id = parseInt($StafferStore.id);

    // let times=[];
    // let client = {}

    function handleResize() {
        isSmallScreen = window.innerWidth < 768;
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

    function addTime(time) {
        jspa("/TimeLog", "addTime", time)
            .then((result) => {
                ec.unselect();
                editTime(result.result.result.id);
                ec.refetchEvents();
            })
            .catch((error) => {
                ec.unselect();
                alert(error.error_message);
            });
    }

    function editTime(time_id) {
        jspa("/TimeLog", "getTime", { id: time_id }).then((result) => {
            let time = result.result;
            time.staff_id = staff_id;
            if (time.do_not_bill == "1") time.do_not_bill = true;
            if (time.do_not_bill == "0") time.do_not_bill = false;

            ModalStore.set({
                label: "Edit Time",
                show: true,
                props: time,
                component: TimeForm,
                action_label: "Update",
                action: () => updateTime(time),
                delete: () => deleteTime(time),
            });
        });
    }

    function deleteTime(time) {
        jspa("/TimeLog", "deleteTime", { id: time.id }).then((result) => {
            ec.refetchEvents();
        });
    }

    // when the time is moved, this updates the time.
    function moveTime(event) {
        let time = {};
        time.id = event.id;
        time.start_date = getDate(event.start);
        time.start_time = get24HourTime(event.start);
        time.end_date = getDate(event.end);
        time.end_time = get24HourTime(event.end);
        updateTime(time);
    }

    // when the time is updated from the Modal, this saves it.
    function updateTime(time) {
        console.log(time);
        jspa("/TimeLog", "updateTime", time).then((result) => {
            ec.refetchEvents();
        });
    }

    onMount(async () => {
        window.addEventListener("resize", handleResize);

        BreadcrumbStore.set({
            path: [{ name: "Time Log for " + $StafferStore.name, url: null }],
        });

        options = {
            allDaySlot: false,
            slotDuration: "00:15:00",
            slotHeight: 28,
            slotEventOverlap: false,
            eventStartEditable: false,
            eventDurationEditable: false,
            editable: timelog_editable,
            firstDay: 1,
            nowIndicator: true,
            selectable: timelog_editable,

            eventClick: function (info) {
                if (info.event.editable) {
                    editTime(info.event.id);
                }
            },
            eventResize: function (info) {
                let events = ec.getEvents();

                // Check for overlaps with other events
                for (let existingEvent of events) {
                    if (
                        info.event.id !== existingEvent.id &&
                        isOverlapping(info.event, existingEvent)
                    ) {
                        // Overlap detected, revert the event to its original position
                        info.revert();
                        toastError("Error: Time overlaps with another time.");
                        return;
                    }
                }
                moveTime(info.event);
            },
            eventDrop: function (info) {
                let events = ec.getEvents();

                // Check for overlaps with other events
                for (let existingEvent of events) {
                    if (
                        info.event.id !== existingEvent.id &&
                        isOverlapping(info.event, existingEvent)
                    ) {
                        // Overlap detected, revert the event to its original position
                        info.revert();
                        toastError("Error: Time overlaps with another time.");
                        return;
                    }
                }
                moveTime(info.event);
            },
            select: function (info) {
                let events = ec.getEvents();
                for (let existingEvent of events) {
                    if (isOverlapping(info, existingEvent)) {
                        // Overlap detected, revert the event to its original position

                        toastError(
                            "Error: Selection overlaps with another time.",
                        );
                        return;
                    }
                }
                if (timelog_editable) {
                    addTime({
                        staff_id: staff_id,
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
                        console.log(info);
                        jspa("/TimeLog", "getTimeLog", {
                            staff_id: staff_id,
                            info: info,
                            editable: timelog_editable,
                        })
                            .then((result) => {
                                let data = result.result;
                                time_data = data;
                                console.log(result);
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

        function isOverlapping(event1, event2) {
            return event1.start < event2.end && event1.end > event2.start;
        }

        if (
            haveCommonElements(rolesStore, [
                "therapist.lead",
                "house.lead",
                "sil.admin",
                "admin",
            ])
        ) {
            timelog_editable = true;
            options.editable = timelog_editable;
            options.selectable = timelog_editable;

            ec.setOption("editable", timelog_editable);
            ec.setOption("selectable", timelog_editable);
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
                    end: "timeGridWeek,timeGridDay,prev,next,today",
                });
            }
        }
    }

    $: {
        fitToScreen(isSmallScreen);
    }
</script>

<div id="calendar">
    <Calendar bind:this={ec} {plugins} {options} />
</div>

<style>
    :global(.ec-time, .ec-line) {
        height: 28px; /* override this value */
    }
    :global(.ec-event-time) {
        display: none;
        font-size: 0.7rem;
        color: #fff;
    }
    :global(.ec-event-title) {
        font-size: 0.8rem;
        font-weight: bold;
        color: #fff;
    }
    :global(.ec-time:nth-child(4n)) {
        visibility: hidden;
    }
    :global(.ec-line:nth-child(4n + 5):after) {
        border-bottom: 1px solid #aaa;
    }
</style>
