<script>
    import Calendar from "@event-calendar/core";
    import TimeGrid from "@event-calendar/time-grid";
    import DayGrid from "@event-calendar/day-grid";
    import Interaction from "@event-calendar/interaction";

    import { jspa } from "@shared/jspa.js";
    import { getStaffer } from "@shared/api.js";
    import { onMount, onDestroy } from "svelte";
    import { BreadcrumbStore, RolesStore } from "@shared/stores.js";
    import {
        ModalStore,
        ContextMenuStore,
        TooltipStore,
        SlideOverStore,
    } from "@app/Overlays/stores.js";

    import { haveCommonElements } from "@shared/utilities.js";

    export let params;
    let closeTooltipTimeoutId;
    let roster_editable = false;

    let isSmallScreen = window.innerWidth < 768;
    let shifts = [];

    let ec;
    let plugins = [DayGrid, TimeGrid, Interaction];
    let week_start_date = null; // = new Date('2023-05-01T00:00:00');
    let shift_data = [];
    let options;
    let staff_id = params.staff_id;

    let staff = {};

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

    onMount(async () => {
        staff = await getStaffer(staff_id);

        window.addEventListener("resize", handleResize);

        BreadcrumbStore.set({
            path: [
                { name: "Staff", url: "/staff" },
                { name: staff.name, url: "/staff/" + staff.id },
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
            selectable: false,

            datesSet: function (info) {
                const date = info.start;
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, "0");
                const day = String(date.getDate()).padStart(2, "0");
                week_start_date = `${year}-${month}-${day}`;
            },

            // eventMouseEnter: function(info) {
            //     clearTimeout(closeTooltipTimeoutId);
            //     console.log(info.event)
            //     let rect = info.el.getBoundingClientRect();
            //     console.log(rect)
            //     TooltipStore.set({
            //         show: true,
            //         title: info.event.titleHTML,
            //         x: rect.left + window.scrollX,
            //         y: rect.top + window.scrollY,
            //         width: rect.width,
            //         height: rect.height
            //     })
            // },

            // eventMouseLeave: function(info) {

            // /*
            //     // Start a delay before setting the tooltip to hide to prevent flickering
            //     closeTooltipTimeoutId = setTimeout(() => {
            //         // Only close the tooltip if not hovering over it
            //         TooltipStore.update(currentTooltip => {
            //             if (!currentTooltip.isHovering) {
            //                 return { show: false };
            //             }
            //             return currentTooltip; // Keep the current state if hovering
            //         });
            //     }, 100);
            // */

            // },

            eventSources: [
                {
                    events: function (info, successCallback, failureCallback) {
                        jspa("/Staff/Schedule", "getScheduleByStaffId", {
                            staff_id: staff_id,
                            info: info,
                        })
                            .then((result) => {
                                let data = result.result;
                                console.log(data);
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
</script>

{#if !haveCommonElements(rolesStore, ["house.lead", "admin"])}
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

<div id="calendar">
    <Calendar bind:this={ec} {plugins} {options} />
</div>

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
