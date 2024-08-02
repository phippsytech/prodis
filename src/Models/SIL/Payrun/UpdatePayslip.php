<?php

namespace NDISmate\Xero\Payroll;

use \RedBeanPHP\R as R;

/*
 * Genearate a payslip for a given employee
 */

class UpdatePayslip
{
    function __invoke($xero, $data)
    {
        $staffer = R::getRow('SELECT staffs.xero_employee_ref, housestaffs.level, housestaffs.house_lead FROM staffs join housestaffs on (staffs.id=housestaffs.staff_id) WHERE staffs.id=:staff_id', [':staff_id' => $data['staff_id']]);

        // Add reimbursement lines
        // Adding Kilometer Reimbursement
        $kms = (new \NDISmate\Models\SIL\House\Roster\GetKmsByStaffId)([
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'staff_id' => $data['staff_id']
        ]);

        $reimbursementLines = [];
        if ($kms > 0) {
            $reimbursementLine = new \XeroAPI\XeroPHP\Models\PayrollAu\ReimbursementLine();
            $reimbursementLine->setReimbursementTypeId('5bf20862-2bb8-4e23-a1f9-f0f6e437d054');  // km re-imbursement
            $reimbursementLine->setAmount($kms);
        }

        $payslipLine = new XeroAPI\XeroPHP\Models\PayrollAu\PayslipLines;
        $payslipLine->setReimbursementLines([$reimbursementLine]);
        $payslipLines[] = $payslipLine;

        try {
            $result = $xero->payrollAuApi->updatePayslip(
                xero_tenant_id: $xero->payroll_tenant_id,
                payslip_id: $payslipID,
                payslip_lines: $payslipLines,
                // idempotency_key: null,
            );
        } catch (Exception $e) {
            echo 'Exception when calling PayrollAuApi->updatePayslip: ', $e->getMessage(), PHP_EOL;
        }
    }
}
