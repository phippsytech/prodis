<?php
namespace NDISmate\Models\Payroll\Payrun;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class ListSalaryPackagingByStaffId
{
    function __invoke($data)
    {
        $salary_packaging_deductions = R::getAll('SELECT 
            account_type,
            total_amount
            FROM salarypackagingdeductions
            WHERE pid=:staff_id
            AND payrun_ref=:payrun_ref
            ', [
            ':staff_id' => $data['staff_id'],
            ':payrun_ref' => $data['payrun_id']
        ]);

        // if empty or null skip this staff member
        if (!$salary_packaging_deductions)
            return [];

        foreach ($salary_packaging_deductions as $deduction) {
            if ($deduction['account_type'] == 'Salary Packaging') {
                $deductionLine = [
                    'deductionTypeID' => 'bbe6095f-4d8d-4cea-af13-40beb33a4757',  // TODO: Fix hardcoded data
                    'amount' => $deduction['total_amount'],
                ];
            }

            if ($deduction['account_type'] == 'Meals/Holiday Expenses') {
                $deductionLine = [
                    'deductionTypeID' => '5de64f75-21a0-4301-8121-9712fda0661a',  // TODO: Fix hardcoded data
                    'amount' => $deduction['total_amount'],
                ];
            }

            $deductionLines[] = $deductionLine;
        }

        return $deductionLines;
    }
}
