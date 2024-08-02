<?php
namespace NDISmate\Models\Payroll\Payrun\SalaryPackagingDeduction;

use \RedBeanPHP\R as R;

class UpdateCSV
{
    public function __invoke($deductions, $staff_id, $payrun_ref)
    {
        if (empty($deductions))
            return;

        foreach ($deductions as $deduction_item) {
            $account_type = '';

            if ($deduction_item->getDeductionTypeID() == 'bbe6095f-4d8d-4cea-af13-40beb33a4757') {
                $account_type = 'Salary Packaging';
            }

            if ($deduction_item->getDeductionTypeID() == '5de64f75-21a0-4301-8121-9712fda0661a') {
                $account_type = 'Meals/Holiday Expenses';
            }

            if (empty($account_type))
                continue;

            // Check if the calculation type is PERCENTAGE, if so append a percent sign to the amount
            $adjusted_amount = $deduction_item->getCalculationType() == 'PERCENTAGE' ? $deduction_item->getAmount() . '%' : $deduction_item->getAmount();

            // use redbeanphp to write a query to update salarypackagingdeductions with the new amounts
            $query =
                'UPDATE salarypackagingdeductions 
                SET adjusted_amount=:adjusted_amount 
                WHERE pid=:pid 
                AND account_type=:account_type 
                AND payrun_ref=:payrun_ref';
            $params = [
                ':adjusted_amount' => $adjusted_amount,
                ':pid' => $staff_id,
                ':account_type' => $account_type,
                ':payrun_ref' => $payrun_ref
            ];

            $result = R::exec($query, $params);
        }
    }
}
