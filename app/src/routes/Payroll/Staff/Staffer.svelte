<script>
    import { ModalStore } from "@app/Overlays/stores.js";
    import BonusModal from "./BonusModal.svelte";
    import LeaveModal from "./LeaveModal.svelte";
    import { jspa } from "@shared/jspa.js";
    import Badge from "@shared/PhippsyTech/Tailwind/App/Elements/Badge.svelte";

    import EmploymentBasis from "./EmploymentBasis.svelte";
    import SILPayGrade from "./SILPayGrade.svelte";

    let staff = [];

    let hasFocus = false;

    // let staff_list=[]

    jspa("/Staff", "listStaff", {}).then((result) => {
        staff = result.result;

        // Filter staff
        staff = staff.filter((staffMember) => {
            // if(staffMember.groups && staffMember.groups.length >0 && staffMember.archived != 1  )
            // return staffMember.groups.includes("therapist");

            return staffMember.archived != 1;
            // return staffMember.groups.includes("therapist");
        });
        staff = staff;

        staff.sort(function (a, b) {
            if (a.name == null) return -1;
            if (b.name == null) return 1;

            const nameA = a.name.toUpperCase(); // ignore upper and lowercase
            const nameB = b.name.toUpperCase(); // ignore upper and lowercase
            if (nameA < nameB) return -1;
            if (nameA > nameB) return 1;
            return 0; // names must be equal
        });
    });

    let visible = false;

    let pay_items = [
        {
            type: "earnings",
            name: "Ordinary Hours",
            quantity: 76,
        },
        // {
        //     type:"earnings",
        //     name:"Public Holiday (Not Worked)",
        //     quantity:7.6
        // },
        // {
        //     type:"earnings",
        //     name:"Bonus ($60)",
        //     quantity:4
        // },
        // {
        //     type:"earnings",
        //     name:"Referal Bonus ($100)",
        //     quantity:1
        // },
        // {
        //     type:"earnings",
        //     name:"Kilometres",
        //     quantity:24
        // }
    ];

    let bonus = {
        type: "bonus",
        quantity: 0,
    };

    let leave = {
        type: "annual",
        quantity: 0,
    };

    let leave_types = [
        { option: "Annual Leave", value: "annual" },
        { option: "Carer's Leave (unpaid)", value: "carer" },
        { option: "Community Service Leave", value: "community" },
        { option: "Compassionate Leave (paid)", value: "paid_compassionate" },
        {
            option: "Compassionate Leave (unpaid)",
            value: "unpaid_compassionate",
        },
        { option: "Long Service Leave", value: "long_service" },
        { option: "Other Unpaid Leave", value: "other_unpaid" },
        { option: "Parental Leave (unpaid)", value: "parental" },
        { option: "Personal (Sick/Carer's) Leave", value: "personal" },
    ];

    function addLeave(leave) {
        pay_items.push({
            type: "earnings",
            name: leave.type,
            quantity: leave.quantity,
        });
        pay_items = pay_items;

        visible = false;
    }

    function showLeaveModal() {
        ModalStore.set({
            label: "Add Leave",
            show: true,
            props: leave,
            component: LeaveModal,
            action_label: "Add",
            action: () => addLeave(leave),
        });
        visible = false;
    }

    function addOrdinaryHours() {
        pay_items.push({
            type: "earnings",
            name: "Ordinary Hours",
            quantity: 0,
        });
        pay_items = pay_items;

        visible = false;
    }

    function addPublicHoliday() {
        pay_items.push({
            type: "earnings",
            name: "Public Holiday (Not Worked)",
            quantity: 7.6,
        });
        pay_items = pay_items;

        visible = false;
    }

    function addBonus(bonus) {
        pay_items.push({
            type: "earnings",
            name: bonus.type,
            quantity: bonus.quantity,
        });
        pay_items = pay_items;

        visible = false;
    }

    function showBonusModal() {
        ModalStore.set({
            label: "Add Bonus",
            show: true,
            props: bonus,
            component: BonusModal,
            action_label: "Add",
            action: () => addBonus(bonus),
        });
        visible = false;
    }

    function deletePayItem(index) {
        pay_items.splice(index, 1);
        pay_items = pay_items;
    }
</script>

<h2
    class=" text-2xl leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular"
    style="color:#220055;"
>
    Default Staff Pay Rates
</h2>

<table class="table">
    <tr class="border-b-2 border-gray-300">
        <th class="p-2 text-left">Name</th>
        <th class="p-2 text-left">Type</th>
        <th class="p-2 text-left">Hourly Rate</th>
        <th class="p-2 text-left">Hours/week</th>
        <th class="p-2 text-left">SIL Pay Grade</th>
    </tr>
    {#each staff as staffer}
        <tr
            class="border-b border-gray-200"
            class:bg-indigo-600={hasFocus == staffer.id}
            on:focusin={() => (hasFocus = staffer.id)}
            on:focusout={() => (hasFocus = false)}
        >
            <td class="p-2" class:text-white={hasFocus == staffer.id}>
                {staffer.name}
                {#if staffer.groups}
                    {#each staffer.groups as group}<Badge
                            label={group}
                        />{/each}
                {/if}
            </td>
            <td class="p-2"
                ><EmploymentBasis bind:value={staffer.employment_basis} /></td
            >
            <td class="p-2"
                >{#if (staffer.groups && staffer.groups.includes("therapist")) || (staffer.groups && staffer.groups.includes("sil") && staffer.employment_basis != "CASUAL")}<input
                        class="p-1"
                        type="number"
                        bind:value={staffer.ordinary_hours_rate}
                        step="0.1"
                        placeholder="hourly rate"
                    />{/if}</td
            >
            <td class="p-2"
                >{#if staffer.employment_basis != "CASUAL"}<input
                        class="p-1"
                        type="number"
                        bind:value={staffer.hours_per_week}
                        step="0.1"
                        placeholder="hours per week"
                    />{/if}</td
            >
            <td class="p-2"
                >{#if staffer.groups && staffer.groups.includes("sil")}<SILPayGrade
                        bind:value={staffer.paygrade_id}
                    />{/if}</td
            >
        </tr>
    {/each}
</table>

<div class="px-4 flex justify-start gap-x-4 mb-4">
    <div class="relative inline-block text-left">
        <div>
            <button
                on:click={() => (visible = !visible)}
                type="button"
                class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                id="menu-button"
                aria-expanded="true"
                aria-haspopup="true"
            >
                Add Pay Item
                <svg
                    class="-mr-1 h-5 w-5 text-gray-400"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>
        </div>

        {#if visible}
            <div
                class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                role="menu"
                aria-orientation="vertical"
                aria-labelledby="menu-button"
                tabindex="-1"
            >
                <div class="py-1" role="none">
                    <span
                        on:click={() => addOrdinaryHours()}
                        class="text-gray-700 block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer"
                        role="menuitem"
                        tabindex="-1"
                        id="menu-item-0">Ordinary Hours</span
                    >
                    <span
                        on:click={() => showLeaveModal()}
                        class="text-gray-700 block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer"
                        role="menuitem"
                        tabindex="-1"
                        id="menu-item-0">Leave</span
                    >
                    <span
                        on:click={() => addPublicHoliday()}
                        class="text-gray-700 block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer"
                        role="menuitem"
                        tabindex="-1"
                        id="menu-item-1">Public Holiday</span
                    >
                    <span
                        on:click={() => showBonusModal()}
                        class="text-gray-700 block px-4 py-2 text-sm hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer"
                        role="menuitem"
                        tabindex="-1"
                        id="menu-item-2">Bonus</span
                    >
                </div>
            </div>
        {/if}
    </div>
</div>

<table class="table">
    <tr class="text-xs">
        <td class="px-4"></td>
        <td class="px-4">Quantity</td>
    </tr>

    {#each pay_items as item, index}
        <tr>
            <td class="px-4">{item.name}</td>
            <td class="px-4"></td>
            <td>
                <button
                    on:click={() => deletePayItem(index)}
                    type="button"
                    class="inline-flex justify-center rounded-md 600 px-1 py-1 text-xs text-gray-600 bg-gray-100 hover:text-white hover:bg-indigo-600 items-center"
                >
                    X</button
                >
            </td>
        </tr>
    {/each}
</table>
