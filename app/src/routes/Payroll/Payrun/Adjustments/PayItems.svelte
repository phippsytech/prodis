<script>
    import { jspa } from "@shared/jspa.js";

    export let staff_id = null;
    export let value = "1c08b7a9-839a-4df5-b135-ae4d47139d34";
    export let pay_item_type = "earnings";

    let earnings = [
        {
            EarningsRateID: "1c08b7a9-839a-4df5-b135-ae4d47139d34",
            Name: "Ordinary Hours",
        },
    ];

    let bonuses = [
        {
            EarningsRateID: "b64dc6b2-dd92-466b-9b8b-290e7d42ec83",
            Name: "Bonus",
            Rate: 60,
        },
        {
            EarningsRateID: "f1213de7-a2b5-452e-9979-b0447d4f7e23",
            Name: "Bonus Referral",
            Rate: 100,
        },
        {
            EarningsRateID: "391b9cbd-0bad-4445-9b57-9181d50aa2b7",
            Name: "Signing Bonus",
        },
    ];

    let deductions = [
        {
            Name: "Other Pre-Tax Deductions",
            DeductionTypeID: "ee8b7f65-e6fd-4f8f-a56d-31b15bb814f0",
        },
        {
            Name: "Other Post-Tax Deductions",
            DeductionTypeID: "935f5734-5c1a-431e-8ef7-93c200f340c8",
        },
        {
            Name: "Union Fees/Subscriptions",
            DeductionTypeID: "676bcbcd-7a1d-43ff-9137-c12591beb5cf",
        },
        {
            Name: "Lease Payments",
            DeductionTypeID: "36072f56-9759-4337-8840-809dd7a04676",
        },
        {
            Name: "FBT",
            DeductionTypeID: "54f487a9-9ab6-43db-9724-60728317f072",
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
        value = e.target.value;
        pay_item_type = e.target.options[e.target.selectedIndex].dataset.type;
    }

    function getPayGrades(staff_id) {
        earnings = [];

        jspa("/Payroll/PayGrade", "listPayGradesByStaffId", {
            staff_id: staff_id,
        })
            .then((result) => {
                earnings = result.result;
            })
            .catch((error) => {
                earnings = [
                    {
                        Name: "Ordinary Hours",
                        EarningsRateID: "1c08b7a9-839a-4df5-b135-ae4d47139d34",
                    },
                ];
            });
    }

    $: {
        if (staff_id != null) {
            getPayGrades(staff_id);
        } else {
        }
    }
</script>

<div
    class="rounded-md px-3 pb-1.5 pt-2.5 shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-indigo-600 bg-white mb-2"
>
    <label for={id} class="block text-xs text-gray-900/50">Pay Item</label>
    <select
        {id}
        on:change={handleChange}
        bind:value
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
        <option disabled>Earnings</option>
        {#each earnings as item}
            <option value={item.EarningsRateID} data-type="earnings"
                >{item.Name}</option
            >
        {/each}

        <option disabled>Bonus</option>
        {#each bonuses as item}
            <option value={item.EarningsRateID} data-type="earnings"
                >{item.Name}</option
            >
        {/each}

        <option disabled>Deductions</option>
        {#each deductions as item}
            <option value={item.DeductionTypeID} data-type="deductions"
                >{item.Name}</option
            >
        {/each}
    </select>
</div>
