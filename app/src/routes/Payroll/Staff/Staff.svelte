<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import Badge from "@shared/PhippsyTech/Tailwind/App/Elements/Badge.svelte";
    import EmploymentBasis from "./EmploymentBasis.svelte";
    import SILPayGrade from "./SILPayGrade.svelte";
    import { ActionBarStore } from "@app/Layout/BottomNav/stores.js";

    let staff = [];
    let stored_staff = {};
    let mounted = false;
    let hasFocus = false;

    onMount(async () => {
        jspa("/Staff", "listStaff", {}).then((result) => {
            staff = result.result;

            // Filter staff
            staff = staff.filter((staffMember) => {
                return staffMember.archived != 1;
            });

            // // This is just here while I am cleaning stuff up.
            // staff = staff.map(staffer => ({
            //     ...staffer,
            //     employment_basis: 'CASUAL', // default value
            //     ordinary_hours_rate: null, // default value
            //     hours_per_week: null, // default value
            // }));
            // staff = staff

            staff.sort(function (a, b) {
                if (a.staff_name == null) return -1;
                if (b.staff_name == null) return 1;
                const nameA = a.staff_name.toUpperCase(); // ignore upper and lowercase
                const nameB = b.staff_name.toUpperCase(); // ignore upper and lowercase
                if (nameA < nameB) return -1;
                if (nameA > nameB) return 1;
                return 0; // names must be equal
            });

            stored_staff = JSON.parse(JSON.stringify(staff));

            mounted = true;
        });
    });

    function undo() {
        staff = JSON.parse(JSON.stringify(stored_staff));
    }

    async function save() {
        try {
            jspa("/Staff", "bulkUpdateStaff", staff).then((result) => {
                stored_staff = JSON.parse(JSON.stringify(staff));
            });
        } catch (error) {
            console.error(error);
        }
    }

    $: {
        if (mounted) {
            ActionBarStore.set({
                can_delete: false,
                show: !(JSON.stringify(staff) === JSON.stringify(stored_staff)),
                undo: () => undo(),
                save: () => save(),
            });
        }
    }
</script>

<h2 class="text-2xl text-indigo-900 tracking-tight font-fredoka-one-regular">
    Default Staff Pay Rates
</h2>

<table class="table">
    <thead class="sticky top-28 bg-white">
        <tr class="border-b-2 border-gray-300">
            <th class="p-2 text-left">Name</th>
            <th class="p-2 text-left">Type</th>
            <th class="p-2 text-left">Base Hourly Rate</th>
            <th class="p-2 text-left">Hours/week</th>
            <th class="p-2 text-left">SIL Pay Grade</th>
        </tr>
    </thead>
    {#each staff as staffer, index}
        <tr
            class="border-b border-gray-200"
            class:bg-indigo-600={hasFocus == staffer.id}
            on:focusin={() => (hasFocus = staffer.id)}
            on:focusout={() => (hasFocus = false)}
        >
            <td class="p-2" class:text-white={hasFocus == staffer.id}>
                {staffer.staff_name}
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
                ><input
                    class="p-1"
                    type="number"
                    bind:value={staffer.ordinary_hours_rate}
                    step="0.1"
                    placeholder="hourly rate"
                /></td
            >
            <!-- <td class="p-2">{#if 
                 (staffer.groups && staffer.groups.includes("therapist") )
                 || (staffer.groups && staffer.groups.includes("admin") )
                || (staffer.groups && staffer.groups.includes("sil") && staffer.employment_basis!="CASUAL")
            }<input class="p-1" type="number" bind:value={staffer.ordinary_hours_rate} step="0.1"  placeholder="hourly rate"  />{/if}</td> -->
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
            <!-- <td class="p-2">{#if staffer.groups && staffer.groups.includes("sil")}<SILPayGrade bind:value={staffer.paygrade_id} />{/if}</td> -->
        </tr>
    {/each}
</table>
