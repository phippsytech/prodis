<script>
    export let leave_type_id = "d7e69589-d4c4-4178-abc0-39fc961e6694";

    let leave = [
        {
            Name: "Annual Leave",
            LeaveTypeID: "d7e69589-d4c4-4178-abc0-39fc961e6694",
        },
        {
            Name: "Long Service Leave",
            LeaveTypeID: "7371762f-4700-4599-81a4-a12f7554332c",
        },
        {
            Name: "Personal/Carer's Leave",
            LeaveTypeID: "b2a23bc7-642e-4e83-8135-41b554e7f956",
        },
        {
            Name: "Compassionate Leave (paid)",
            LeaveTypeID: "6f7b4f53-9fc9-4be7-a883-36a8bfced83f",
        },
        {
            Name: "Community Service Leave",
            LeaveTypeID: "98b13673-f3c4-4f31-8943-559085b81152",
        },
        {
            Name: "Unpaid Leave",
            LeaveTypeID: "3db723b9-7ab1-4dcf-b1dd-c071016a20ba",
        },
        {
            Name: "Annual Leave (5 Weeks)",
            LeaveTypeID: "6d07ae1d-34fb-48e2-a47d-1cfbc2e49500",
        },
    ];

    let id;

    function makeUniqueId() {
        return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(
            /[xy]/g,
            function (c) {
                var r = (Math.random() * 16) | 0,
                    v = c == "x" ? r : (r & 0x3) | 0x8;
                return v.toString(16);
            },
        );
    }

    if (!id) {
        id = makeUniqueId();
    }

    function handleChange(e) {
        leave_type_id = e.target.value;
    }
</script>

<div
    class="rounded-md px-3 pb-1.5 pt-2.5 shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-indigo-600 bg-white mb-2"
>
    <label for={id} class="block text-xs text-gray-900/50">Pay Item</label>
    <select
        {id}
        on:change={handleChange}
        class="
            block
            w-full
            p-0
            -mx-1
            text-base
            font-normal
            text-gray-700
            bg-white bg-clip-padding
            block w-full border-0 p-0 text-gray-900 placeholder:text-gray-400 outline-none"
        required
    >
        {#each leave as item}
            <option value={item.LeaveTypeID}>{item.Name}</option>
        {/each}
    </select>
</div>
