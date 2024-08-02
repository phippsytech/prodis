<script>
    import FloatingSelect from "@shared/PhippsyTech/svelte-ui/forms/FloatingSelect.svelte";
    import FloatingTextArea from "@shared/PhippsyTech/svelte-ui/forms/FloatingTextArea.svelte";

    import { jspa } from "@shared/jspa.js";
    import { StafferStore } from "@shared/stores.js";
    import { formatDate } from "@shared/utilities.js";

    export let props;

    let staff_id = parseInt($StafferStore.id);
    let clients = [];
    let clientList = [];
    let billable = !props.do_not_bill; // this needs to change to bill_time

    $: {
        props.do_not_bill = !billable;
        if (props.client_id == "Select Client") props.client_id = null;
    }

    function getHouseStaff() {
        jspa("/Client/Staff", "listStaffClientsByStaffId", {
            staff_id: staff_id,
        }).then((result) => {
            clientList = [];
            clients = result.result;
            clients.push({ client_id: null, name: "[Unassigned]", id: null });
            clients = clients.sort((a, b) =>
                a.client_name.localeCompare(b.client_name),
            );
            clients.forEach((client) => {
                let options = {
                    option: client.client_name,
                    value: client.id,
                    selected: false,
                };
                if (
                    client.client_id &&
                    props &&
                    client.client_id == props.client_id
                ) {
                    options.selected = true;
                }
                if (client.archived != 1) {
                    clientList.push(options);
                }
            });
            clientList = clientList;
            console.log(clientList);
        });
    }

    getHouseStaff();
</script>

<h3 class="text-base leading-6 text-gray-900" id="modal-title">
    {formatDate(props.start_date, {
        day: "numeric",
        month: "short",
        year: null,
    })}
    {props.start_time} - {#if props.start_date != props.end_date}{formatDate(
            props.end_date,
            { day: "numeric", month: "short", year: null },
        )}{/if}
    {props.end_time}
</h3>

<FloatingSelect
    bind:value={props.client_id}
    label="Assign to Client"
    instruction="Select Client"
    options={clientList}
    hideValidation={true}
/>

<FloatingTextArea
    bind:value={props.description}
    label="Notes"
    placeholder="eg: Extra Notes."
    style="height:150px"
/>
