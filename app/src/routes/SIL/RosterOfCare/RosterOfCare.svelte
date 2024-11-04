<script>
  import Calendar from "@event-calendar/core";
  import TimeGrid from "@event-calendar/time-grid";
  import DayGrid from "@event-calendar/day-grid";
  import Interaction from "@event-calendar/interaction";
  import ShiftForm from "./RosterOfCareForm.svelte";
  import { jspa } from "@shared/jspa.js";
  import { BreadcrumbStore } from "@shared/stores.js";
  import { onMount } from "svelte";
  import { RolesStore } from "@shared/stores.js";
  import { ModalStore, ContextMenuStore } from "@app/Overlays/stores.js";
  import { haveCommonElements } from "@shared/utilities.js";

  export let params;

  let shift_editable = false;

  let isSmallScreen = window.innerWidth < 768;
  let shifts = [];
  let staff = [];
  let ec;
  let plugins = [DayGrid, TimeGrid, Interaction];
  let week_start_date = null; // = new Date('2023-05-01T00:00:00');
  let shift_data = [];
  let options;
  let rosterofcare_id = params.rosterofcare_id;
  let rosterofcare = {};

  $: rolesStore = $RolesStore;

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
    jspa("/SIL/RosterOfCare/Shift", "addShift", shift)
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
    jspa("/SIL/RosterOfCare/Shift", "getShift", { id: shift_id }).then(
      (result) => {
        let shift = result.result;
        shift.rosterofcare_id = rosterofcare_id;

        if (shift.passive == "1") shift.passive = true;
        if (shift.passive == "0") shift.passive = false;

        ModalStore.set({
          label: "Edit Shift",
          show: true,
          props: shift,
          component: ShiftForm,
          action_label: "Update",
          action: () => updateShift(shift),
          delete: () => deleteShift(shift),
        });
      }
    );
  }

  function deleteShift(shift) {
    jspa("/SIL/RosterOfCare/Shift", "deleteShift", { id: shift.id }).then(
      (result) => {
        ec.refetchEvents();
      }
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
    jspa("/SIL/RosterOfCare/Shift", "updateShift", shift).then((result) => {
      ec.refetchEvents();
    });
  }

  async function getRosterOfCare(rosterofcare_id) {
    let rosterofcare = await jspa("/SIL/RosterOfCare", "getRosterOfCare", {
      id: rosterofcare_id,
    });
    return rosterofcare.result;
  }

  onMount(async () => {
    rosterofcare = await getRosterOfCare(rosterofcare_id);

    window.addEventListener("resize", handleResize);

    BreadcrumbStore.set({
      path: [
        { name: "SIL", url: "/sil" },
        { name: "Roster Of Care", url: "/sil/rosterofcare" },
      ],
    });

    options = {
      allDaySlot: false,
      slotDuration: "00:30:00",
      slotHeight: 12,
      slotEventOverlap: false,
      eventStartEditable: false,
      eventDurationEditable: false,
      editable: shift_editable,
      firstDay: 1,
      nowIndicator: true,
      selectable: shift_editable,
      dateClick: function (info) {
        let myDiv = document.getElementById("calendar");
        const { top } = myDiv.getBoundingClientRect();

        ContextMenuStore.set({
          show: true,
          title: "Add shift",
          options: [
            { name: "3.5 hours", value: 3.5 },
            { name: "7.5 hour", value: 7.5 },
            { name: "8 hour", value: 8 },
          ],
          select: (value) => {
            let start_date = new Date(info.date);
            let end_date = new Date(info.date);
            end_date.setHours(end_date.getHours() + value);
            addShift({
              rosterofcare_id: rosterofcare.id,
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
        if (shift_editable) {
          addShift({
            rosterofcare_id: rosterofcare.id,
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
            jspa("/SIL/RosterOfCare/Shift", "getShifts", {
              id: rosterofcare.id,
              info: info,
              editable: shift_editable,
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

    if (haveCommonElements(rolesStore, ["house.lead", "admin"])) {
      shift_editable = true;
      options.editable = shift_editable;
      options.selectable = shift_editable;

      ec.setOption("editable", shift_editable);
      ec.setOption("selectable", shift_editable);
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
</script>

<div class="flex items-center mb-2 px-4">
  <div class="flex-auto">
    <div
      class="text-2xl text-indigo-700 tracking-tight font-fredoka-one-regular"
    >
      Roster Of Care for {rosterofcare.participant_name}
    </div>
    <p>
      Use this tool as an aid to calculating Supported Independent Living (SIL)
      claims.
    </p>
  </div>
</div>

<div id="calendar">
  <Calendar bind:this={ec} {plugins} {options} />
</div>

<style>
  :global(.ec-time, .ec-line) {
    height: 12px; /* override this value */
  }
  :global(.ec-event) {
    font-size: 0.7rem;
    border: 1px solid white;
    border-radius: 6px;
    color: #fff;
  }
  :global(.ec-event-time) {
    font-size: 0.7rem;
    color: #fff;
  }
  :global(.ec-event-title) {
    font-size: 0.8rem;
    font-weight: bold;
    color: #fff;
  }
  /* :global(.ec-time:nth-child(8n) ) {
        visibility:hidden;
    } */
  :global(.ec-line:nth-child(4n + 5):after) {
    border-bottom: 1px solid #aaa;
  }
</style>
