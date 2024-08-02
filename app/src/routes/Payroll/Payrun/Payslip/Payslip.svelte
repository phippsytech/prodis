<script>
    import { onMount } from "svelte";
    import { jspa } from "@shared/jspa.js";
    import { BreadcrumbStore } from "@shared/stores.js";
    import PayItemLines from "./PayItemLines.svelte";
    import TaxTypeLines from "./TaxTypeLines.svelte";
    import { toastSuccess, toastError } from "@shared/toastHelper.js";
    import { convertMicrosoftDate } from "@shared/utilities.js";
    import { SpinnerStore } from "@app/Overlays/stores.js";
    import { push } from "svelte-spa-router";

    export let params;

    let payrun_id = params.payrun_id;
    let payslip_id = params.payslip_id;
    let payslip = {};
    let payrun = {};

    let staff_id = null;

    // write this component that takes in a payslip_id via params and displays the payslip

    onMount(() => {
        jspa("/Payroll/Payrun", "getPayslip", { payslip_id: payslip_id }).then(
            (result) => {
                payslip = result.result;

                BreadcrumbStore.set({
                    path: [
                        { url: "/payroll", name: "Payroll" },
                        {
                            url: "/payroll/payruns/" + payrun_id,
                            name: "Payrun",
                        },
                        {
                            url: null,
                            name: payslip.FirstName + " " + payslip.LastName,
                        },
                    ],
                });

                jspa("/Payroll/Payrun", "getStaffIdByXeroEmployeeRef", {
                    xero_employee_ref: payslip.EmployeeID,
                }).then((result) => {
                    staff_id = result.staff_id;
                });
            },
        );
    });

    async function generateEmployeePayslip() {
        try {
            SpinnerStore.set({
                show: true,
                message: `Generating payslip for ${payslip.FirstName} ${payslip.LastName}`,
            });

            const payrunResult = await jspa("/Payroll/Payrun", "getPayrun", {
                payrun_id: payrun_id,
            });
            payrun = payrunResult.result;

            await jspa("/Payroll/Payrun", "doPayrun", {
                payrun_id: payrun_id,
                staff_id: staff_id,
                start_date: convertMicrosoftDate(payrun.PayRunPeriodStartDate),
                end_date: convertMicrosoftDate(payrun.PayRunPeriodEndDate),
            });
        } catch (error) {
            console.error("Error generating employee payslip:", error);
        } finally {
            push(`/payroll/payruns/${payrun_id}/payrun`);
            SpinnerStore.set({ show: false });
        }
    }

    /*
{
    "EmployeeID": "2cba6c25-5c40-42cc-86a2-7692c2889eeb",
    "PayslipID": "726b917f-662c-43b1-80b7-81cc717e8d7f",
    "FirstName": "Stuart Raymond",
    "LastName": "Kasule",
    "Tax": 0,
    "NetPay": 0,
    "EarningsLines": [],
    "LeaveEarningsLines": [
        {
            "EarningsRateID": "1c08b7a9-839a-4df5-b135-ae4d47139d34",
            "RatePerUnit": 0,
            "NumberOfUnits": 15.2,
            "PayOutType": "DEFAULT"
        },
        {
            "EarningsRateID": "1c08b7a9-839a-4df5-b135-ae4d47139d34",
            "RatePerUnit": 0,
            "NumberOfUnits": 7.6,
            "PayOutType": "DEFAULT"
        }
    ],
    "TimesheetEarningsLines": [],
    "DeductionLines": [],
    "LeaveAccrualLines": [],
    "ReimbursementLines": [],
    "SuperannuationLines": [
        {
            "SuperMembershipID": "cd311eb0-24b3-4613-af33-6d8a300f1c6a",
            "ContributionType": "SGC",
            "CalculationType": "STATUTORY",
            "MinimumMonthlyEarnings": 450,
            "ExpenseAccountCode": "458",
            "LiabilityAccountCode": "826",
            "PaymentDateForThisPeriod": "/Date(1706400000000+0000)/",
            "Percentage": 0,
            "Amount": 0
        }
    ],
    "TaxLines": [
        {
            "PayslipTaxLineID": "8dee1fa2-90de-47c8-baec-0ab6db3244f6",
            "Amount": 0,
            "TaxTypeName": "PAYG",
            "Description": "With tax-free threshold and leave loading"
        }
    ],
    "UpdatedDateUTC": "/Date(1702258269000+0000)/"
}
*/
</script>

<div class="flex justify-between items-center">
    <h2
        class=" text-2xl leading-7 text-indigo-900 sm:truncate tracking-tight font-fredoka-one-regular mb-4"
    >
        {payslip.FirstName}
        {payslip.LastName}
    </h2>
    <button
        on:click={() => generateEmployeePayslip()}
        type="button"
        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >Generate Payslip</button
    >
</div>

<PayItemLines payitems={payslip.EarningsLines} type="earnings" />

<PayItemLines payitems={payslip.LeaveEarningsLines} type="leave" />

<PayItemLines payitems={payslip.DeductionLines} type="deductions" />

<TaxTypeLines tax_lines={payslip.TaxLines} />

<PayItemLines payitems={payslip.LeaveAccrualLines} type="leave_accrual" />

<PayItemLines payitems={payslip.ReimbursementLines} type="reimbursements" />
