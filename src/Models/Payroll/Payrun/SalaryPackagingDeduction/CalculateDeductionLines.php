<?php
namespace NDISmate\Models\Payroll\Payrun\SalaryPackagingDeduction;

use \RedBeanPHP\R as R;

class CalculateDeductionLines
{
    function __invoke($salary_packaging, $gross_total)
    {
        $salary_packaging_amount = 0;
        $meals_holiday_expenses_amount = 0;

        foreach ($salary_packaging as &$salary_packaging_deduction_item) {
            $amount = $salary_packaging_deduction_item['amount'];
            $isPercentage = strpos($amount, '%') !== false;

            if ($isPercentage) {
                $percentage = str_replace('%', '', $amount);
                $salary_packaging_deduction_item['isPercentage'] = TRUE;
                $salary_packaging_deduction_item['percentage'] = $percentage;
                $salary_packaging_deduction_item['amount'] = $gross_total * $percentage / 100;
                continue;
            }

            switch ($salary_packaging_deduction_item['deductionTypeID']) {
                // Salary Packaging
                case 'bbe6095f-4d8d-4cea-af13-40beb33a4757':
                    $salary_packaging_amount = $amount;
                    break;

                // Meals/Holiday Expenses
                case '5de64f75-21a0-4301-8121-9712fda0661a':
                    $meals_holiday_expenses_amount = $amount;
                    break;
            }
        }

        $total_deductions = $salary_packaging_amount + $meals_holiday_expenses_amount;

        if ($gross_total < $total_deductions) {
            $excess = $total_deductions - $gross_total;

            if ($meals_holiday_expenses_amount >= $excess) {
                $meals_holiday_expenses_amount -= $excess;
            } else {
                $excess -= $meals_holiday_expenses_amount;
                $meals_holiday_expenses_amount = 0;
                $salary_packaging_amount = max(0, $salary_packaging_amount - $excess);
            }
        }

        // Update the salary_packaging array with the new amounts
        foreach ($salary_packaging as &$salary_packaging_deduction_item) {
            if ($salary_packaging_deduction_item['deductionTypeID'] == 'bbe6095f-4d8d-4cea-af13-40beb33a4757') {
                $salary_packaging_deduction_item['amount'] = $salary_packaging_amount;
            } elseif ($salary_packaging_deduction_item['deductionTypeID'] == '5de64f75-21a0-4301-8121-9712fda0661a') {
                $salary_packaging_deduction_item['amount'] = $meals_holiday_expenses_amount;
            }
        }

        // Create deduction lines
        $deductionLines = [];
        foreach ($salary_packaging as $deduction_item) {
            $deductionLines[] = $this->addDeductionsLine($deduction_item);
        }

        return $deductionLines;
    }

    function addDeductionsLine($deduction_item)
    {
        $deductionLine = new \XeroAPI\XeroPHP\Models\PayrollAu\DeductionLine;
        $deductionLine->setDeductionTypeID($deduction_item['deductionTypeID']);
        $deductionLine->setAmount($deduction_item['amount']);

        if ($deduction_item['isPercentage']) {
            $deductionLine->setCalculationType('PRETAX');
            $deductionLine->setPercentage($deduction_item['percentage']);
        } else {
            $deductionLine->setCalculationType('FIXEDAMOUNT');
        }

        return $deductionLine;
    }
}
