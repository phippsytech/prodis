<script>
    import Container from "@shared/Container.svelte";
    import FloatingInput from "@shared/PhippsyTech/svelte-ui/forms/FloatingInput.svelte";
    import StaffSelector from "@app/routes/Billables/StaffSelector.svelte";
    // import PayItems from './Adjustments/PayItems.svelte';
    import EarningsItemSelector from "@app/routes/Payroll/EarningsItemSelector.svelte";
    import CurrentAdjustments from "./Adjustments/CurrentAdjustments.svelte";
    import { BreadcrumbStore } from "@shared/stores.js";

    import { jspa } from "@shared/jspa.js";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";

    let staff = [];
    let current_adjustments;
    let adjustment = {
        pay_item_type: "earnings",
    };
    let earnings_item = {
        Name: null,
        EarningsRateID: null,
        RatePerUnit: null,
    };
    let calc_hours = 0;
    let calc_mins = 0;
    let calc_dec = 0;
    let calc_days = 0;
    let calc_day_dec = 0;

    $: {
        calc_dec = +(+calc_hours + +calc_mins / 60).toFixed(2);
    }

    $: {
        calc_day_dec = +(+calc_days * 7.6).toFixed(2);
    }

    BreadcrumbStore.set({
        path: [
            { url: "/payroll", name: "Payroll" },
            { url: "/payroll/payruns", name: "Pay Runs" },
            { url: null, name: "Pay Run" },
        ],
    });

    jspa("/Staff", "listStaff", {}).then((result) => {
        staff = result.result;

        // Filter staff
        staff = staff.filter((staffMember) => {
            return staffMember.archived != 1;
        });

        staff = staff;

        staff.sort(function (a, b) {
            if (a.staff_name == null) return -1;
            if (b.staff_name == null) return 1;
            const nameA = a.staff_name.toUpperCase(); // ignore upper and lowercase
            const nameB = b.staff_name.toUpperCase(); // ignore upper and lowercase
            if (nameA < nameB) return -1;
            if (nameA > nameB) return 1;
            return 0; // names must be equal
        });
    });

    function addAdjustment(adjustment) {
        if (
            adjustment.staff_id == "Choose staffer" ||
            adjustment.staff_id == null
        ) {
            toastError("Please select a staffer");
            return;
        }

        if (!adjustment.quantity || isNaN(adjustment.quantity)) {
            toastError("Please enter a valid quantity");
            return;
        }

        jspa("/Payroll/Payrun/Adjustment", "addAdjustment", adjustment)
            .then((result) => {
                adjustment = result.result;
                toastSuccess("Payrun Adjustment added");
                current_adjustments.listAdjustments();
            })
            .catch((error) => {
                let error_message = error.error_message;
                toastError(error_message);
            });
    }

    function onEarningsItemSelected(e) {
        adjustment.pay_item_xero_ref = e.detail.EarningsRateID;
        adjustment.rate = e.detail.RatePerUnit;
    }

    // function isRateEditable(){
    //     if(!(adjustment.pay_item_type=="deductions")) return true
    //     return false
    // }
</script>

<h2
    class=" text-2xl leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight font-fredoka-one-regular mb-4"
    style="color:#220055;"
>
    Current Payrun Adjustments
</h2>
<!-- 
<Container>
    <div class="text-sm text-gray-500">Hour/Mins to Decimal Converter</div>
    <div class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 items-center">
        <div class="flex-grow">
            <FloatingInput label="Hours" type="number" bind:value={calc_hours}/>
        </div>
        <div class="flex-grow">
            <FloatingInput label="Minutes" type="number" bind:value={calc_mins}/>
        </div>    
        <div class="flex-grow">
            <FloatingInput label="Decimal" type="number" bind:value={calc_dec}/>
        </div>    
    </div>
</Container>

<Container>
    <div class="text-sm text-gray-500">Days to Hours Converter</div>
    <div class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 items-center">
        <div class="flex-grow">
            <FloatingInput label="Days" type="number" bind:value={calc_days}/>
        </div>
        <div class="flex-grow">
            <FloatingInput label="Decimal" type="number" bind:value={calc_day_dec}/>
        </div>    
    </div>
</Container> -->

<div
    class="flex flex-col sm:flex-row justify-between gap-0 sm:gap-2 items-center"
>
    <div class="flex-grow">
        <StaffSelector bind:staff_id={adjustment.staff_id} />
    </div>
    <div class="flex-grow">
        <!-- <PayItems bind:value={adjustment.pay_item_xero_ref} bind:pay_item_type={adjustment.pay_item_type} bind:staff_id={adjustment.staff_id}/> -->
        <EarningsItemSelector
            bind:value={earnings_item}
            on:change={onEarningsItemSelected}
        />
    </div>
    <div class="flex-grow">
        <FloatingInput
            label="Quantity"
            type="number"
            pattern="\d*\.?\d*"
            bind:value={adjustment.quantity}
        />
    </div>

    <div class="flex-grow">
        <FloatingInput
            label="Rate"
            type="number"
            step="any"
            pattern="\d*\.?\d*"
            bind:value={adjustment.rate}
            placeholder={earnings_item.RatePerUnit}
        />
    </div>

    <button
        on:click={() => addAdjustment(adjustment)}
        type="button"
        class="w-auto inline-flex justify-center rounded-md bg-indigo-600 px-3 py-4 text-sm font-semibold text-white hover:bg-indigo-500 mb-2"
        >Add Adjustment
    </button>
</div>

<CurrentAdjustments bind:this={current_adjustments} />
