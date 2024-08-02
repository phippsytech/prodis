<?php
namespace NDISmate\Models\Payroll\Payrun\Payslip;

use \RedBeanPHP\R as R;

class MakeEmployeePayslip
{
    function __invoke($xero, $employee_payslip_data, $payrun_id)
    {
        try {
            if (
                empty($employee_payslip_data['shifts']) &&
                empty($employee_payslip_data['ppt']) &&
                empty($employee_payslip_data['adjustments']) &&
                empty($employee_payslip_data['kms'])
            )
                return ['http_code' => 204];;

            // If appropriate, mark this as the final payslip (this needs to be done first)
            // TODO

            $payslip_id = $employee_payslip_data['payslip_id'];
            $employee_id = $employee_payslip_data['xero_employee_id'];

            $apiEmployeeResponse = $xero->payrollAuApi->getEmployee($xero->payroll_tenant_id, $employee_id);
            sleep(1);  // to avoid rate limit

            // Create LeaveApplications from LeaveRequests
            // TODO
            // $leave_units = 0;

            // Get leave hours
            // foreach($employee_payslip_data["leave"] as $leave_request){
            //     $leave_hours += $leave_request['hours'];
            // }

            $gross_total = 0;

            // Create Pay Items from Shifts
            foreach ($employee_payslip_data['shifts'] as $shift_earnings_item) {
                $gross_total += $shift_earnings_item['numberOfUnits'] * $shift_earnings_item['ratePerUnit'];
                $earningsLines[] = $this->addEarningsLine($shift_earnings_item);
            }

            // Create Pay Items for PPT
            foreach ($employee_payslip_data['ppt'] as $ppt_earnings_item) {
                $gross_total += $ppt_earnings_item['numberOfUnits'] * $ppt_earnings_item['ratePerUnit'];
                $earningsLines[] = $this->addEarningsLine($ppt_earnings_item);
            }

            // Create Pay Items from Payroll Adjustments data
            foreach ($employee_payslip_data['adjustments'] as $adjustment_earnings_item) {
                $gross_total += $adjustment_earnings_item['numberOfUnits'] * $adjustment_earnings_item['ratePerUnit'];
                $earningsLines[] = $this->addEarningsLine($adjustment_earnings_item);
            }

            // Create KM Lines
            foreach ($employee_payslip_data['kms'] as $km_earnings_item) {
                $gross_total += $km_earnings_item['numberOfUnits'] * $km_earnings_item['ratePerUnit'];
                $earningsLines[] = $this->addEarningsLine($km_earnings_item);
            }

            // Salary Sacrifice Disabled
            // // Create Deduction Lines from Salary Sacrifice
            // $deductionLines = (new \NDISmate\Models\Payroll\Payrun\SalaryPackagingDeduction\CalculateDeductionLines)($employee_payslip_data['salary_packaging'], $gross_total);

            // // Update CCB CSV Data
            // (new \NDISmate\Models\Payroll\Payrun\SalaryPackagingDeduction\UpdateCSV)($deductionLines, $employee_payslip_data['staff_id'], $payrun_id);

            // return ["http_code"=>200,"result"=>$deductionLines];

            // ### SUPERANNUATION HAS BEEN DISABLED HERE.  IT SHOULD BE SET UP IN THE PAYSLIP TEMPLATE ###
            // Superannuation Lines
            // $super_membership_id = $apiEmployeeResponse[0]->getSuperMemberships()[0]->getSuperMembershipID();
            // $superannuationLine = new \XeroAPI\XeroPHP\Models\PayrollAu\SuperannuationLine();
            // $superannuationLine->setSuperMembershipId($super_membership_id);
            // $superannuationLine->setContributionType('SGC');
            // $superannuationLine->setCalculationType('STATUTORY');
            // $superannuationLine->setExpenseAccountCode(458);
            // $superannuationLine->setLiabilityAccountCode(826);
            // $superannuationLine->setPaymentDateForThisPeriod((new \DateTime('2024-01-28')));
            // $superannuationLines[] = $superannuationLine;

            // Payslip
            $payslipLine = new \XeroAPI\XeroPHP\Models\PayrollAu\PayslipLines;
            $payslipLine->setEarningsLines($earningsLines);
            // $payslipLine->setDeductionLines($deductionLines);
            // $payslipLine->setSuperannuationLines($superannuationLines);

            // Update Payslip
            $result = $xero->payrollAuApi->updatePayslip(
                xero_tenant_id: $xero->payroll_tenant_id,
                payslip_id: $payslip_id,
                payslip_lines: [$payslipLine],
                // idempotency_key: null,
            );
            sleep(1);  // to avoid rate limit

            return ['http_code' => 200, 'result' => $result];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $errors[] = ['message' => json_decode($e->getResponseBody(), true), 'staff_id' => $employee_payslip_data['staff_id']];
            // print_r($errors);
            return ['http_code' => 400, 'error_message' => $errors];
        }
    }

    function addEarningsLine($earnings_item)
    {
        $earningsLine = new \XeroAPI\XeroPHP\Models\PayrollAu\EarningsLine;
        $earningsLine->setEarningsRateID($earnings_item['earningsRateID']);
        $earningsLine->setNumberOfUnits($earnings_item['numberOfUnits']);

        if ($earnings_item['earningsRateID'] == '391b9cbd-0bad-4445-9b57-9181d50aa2b7') {
            // Signing Bonus, which needs to be a fixed amount.
            $earningsLine->setFixedAmount($earnings_item['ratePerUnit']);
        } else {
            $earningsLine->setRatePerUnit($earnings_item['ratePerUnit']);
        }

        return $earningsLine;
    }
}
