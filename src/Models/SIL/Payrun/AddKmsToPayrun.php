<?php
namespace NDISmate\Models\SIL\Payrun;

use \RedBeanPHP\R as R;

class AddKmsToPayrun
{
    function __invoke($xero, $data)
    {
        // km allowance (below ATO rate)
        $kilometer_earnings_below_tax_rate_id = '5bf20862-2bb8-4e23-a1f9-f0f6e437d054';

        // km allowance (above ATO rate)
        $kilometer_earnings_above_tax_rate_id = '15ecfd02-4eb8-460a-aecd-c54b84941a2d';

        // If I can't send the call without the rateperunit then I will need to look it up from the PayItems API
        // "Name": "km allowance (above ATO rate)",
        // "AccountCode": "41015",
        // "TypeOfUnits": "km",
        // "IsExemptFromTax": false,
        // "IsExemptFromSuper": true,
        // "IsReportableAsW1": true,
        // "AllowanceContributesToAnnualLeaveRate": false,
        // "AllowanceContributesToOvertimeRate": false,
        // "EarningsType": "ALLOWANCE",
        // "EarningsRateID": "15ecfd02-4eb8-460a-aecd-c54b84941a2d",
        // "RateType": "RATEPERUNIT",
        // "RatePerUnit": "0.14",
        // "UpdatedDateUTC": "/Date(1687312321000+0000)/",
        // "CurrentRecord": true,
        // "AllowanceType": "CAR"

        // some basic error checking
        $errors = null;
        if (!isset($data['payrun_id']))
            $errors[] = 'payrun_id is required';
        if (!isset($data['start_date']))
            $errors[] = 'start_date is required';
        if (!isset($data['end_date']))
            $errors[] = 'end_date is required';
        if ($errors)
            return ['http_code' => 400, 'error' => $errors];

        try {
            $apiResponse = $xero->payrollAuApi->getPayrun($xero->payroll_tenant_id, $data['payrun_id']);
            sleep(1);  // to avoid rate limit
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ['http_code' => 400, 'error' => $error, 'staff_id' => $staff_id];
        }

        foreach ($apiResponse->getPayRuns()[0]->getPayslips() as $payslip) {
            try {
                $payslipId = $payslip->getPayslipID();
                $employeeId = $payslip->getEmployeeId();

                if (!$payslipId)
                    continue;  // skip if there is no payslip id

                // load the actual payslip so we can get existing earnings lines
                $payslip_obj = $xero->payrollAuApi->getPayslip($xero->payroll_tenant_id, $payslipId);
                sleep(1);  // to avoid rate limit

                $earningsLines = $payslip_obj->getPayslip()->getEarningsLines();

                // If this code is run twice, and I've already added Kms, I don't want to add them again.
                foreach ($earningsLines as $earningsLine) {
                    if ($earningsLine->getEarningsRateID() == $kilometer_earnings_below_tax_rate_id ||
                            $earningsLine->getEarningsRateID() == $kilometer_earnings_above_tax_rate_id) {
                        continue 2;
                    }
                }

                if (!$employeeId)
                    continue;

                $staffer = R::getRow('SELECT id, xero_super_membership_ref from staffs where xero_employee_ref=:xero_employee_ref', [':xero_employee_ref' => $employeeId]);
                if (!$staffer)
                    continue;

                $staff_id = $staffer['id'];
                $xero_super_membership_ref = $staffer['xero_super_membership_ref'];

                // print_r([
                //     "start_date"=>$data['start_date'],
                //     "end_date"=>$data['end_date'],
                //     "staff_id"=>$staff_id
                // ]);

                // Adding Kilometer Reimbursement
                $kms = (new \NDISmate\Models\SIL\House\Roster\GetKmsByStaffId)([
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'staff_id' => $staff_id
                ]);

                if ($kms > 0) {
                    // km allowance (below ATO rate)
                    $earningsLine = new \XeroAPI\XeroPHP\Models\PayrollAu\EarningsLine;
                    $earningsLine->setEarningsRateID($kilometer_earnings_below_tax_rate_id);
                    $earningsLine->setRatePerUnit(0.88);
                    $earningsLine->setNumberOfUnits($kms);
                    $earningsLines[] = $earningsLine;

                    // km allowance (above ATO rate)
                    $earningsLine = new \XeroAPI\XeroPHP\Models\PayrollAu\EarningsLine;
                    $earningsLine->setEarningsRateID($kilometer_earnings_above_tax_rate_id);
                    $earningsLine->setRatePerUnit(0.11);
                    $earningsLine->setNumberOfUnits($kms);
                    $earningsLines[] = $earningsLine;

                    $payslipLine = new \XeroAPI\XeroPHP\Models\PayrollAu\PayslipLines;

                    $payslipLine->setEarningsLines($earningsLines);

                    // Adding Superannuation - I'm only doing this to bypass the error that I get when I try to add earnings lines without superannuation lines
                    $superannuationLines = $payslip_obj->getPayslip()->getSuperannuationLines();
                    $statutoryLines = array_filter($superannuationLines, function ($line) {
                        return $line->getCalculationType() === 'STATUTORY';
                    });
                    if (empty($statutoryLines)) {
                        if ($xero_super_membership_ref == null) {
                            try {
                                $apiResponse = $xero->payrollAuApi->getEmployee($xero->payroll_tenant_id, $employeeId);
                                $super_membership_id = $apiResponse[0]->getSuperMemberships()[0]->getSuperMembershipID();
                                sleep(1);  // to avoid rate limit
                                $superannuationLine = new \XeroAPI\XeroPHP\Models\PayrollAu\SuperannuationLine();
                                $superannuationLine->setSuperMembershipId($super_membership_id);
                                $superannuationLine->setContributionType('SGC');
                                $superannuationLine->setCalculationType('STATUTORY');
                                $superannuationLine->setExpenseAccountCode(41025);
                                $superannuationLine->setLiabilityAccountCode(826);
                                $superannuationLine->setPaymentDateForThisPeriod((new \DateTime('2024-01-28')));
                                $superannuationLines[] = $superannuationLine;
                                $payslipLine->setSuperannuationLines($superannuationLines);
                            } catch (\XeroAPI\XeroPHP\ApiException $e) {
                                $errors[] = ['message' => json_decode($e->getResponseBody(), true), 'staff_id' => $staff_id];
                            }
                        }
                    }

                    $result = $xero->payrollAuApi->updatePayslip(
                        xero_tenant_id: $xero->payroll_tenant_id,
                        payslip_id: $payslipId,
                        payslip_lines: [$payslipLine],
                        // idempotency_key: null,
                    );

                    sleep(1);  // to avoid rate limit
                }
            } catch (\XeroAPI\XeroPHP\ApiException $e) {
                $errors[] = ['message' => json_decode($e->getResponseBody(), true), 'staff_id' => $staff_id];
                // return ["http_code"=>400, "error"=>$errors];
            }
        }

        return ['http_code' => 200, 'error' => $errors];
    }
}
