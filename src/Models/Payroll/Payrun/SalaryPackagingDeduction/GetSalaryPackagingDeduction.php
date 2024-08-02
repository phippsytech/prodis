<?php
namespace NDISmate\Models\Payroll\Payrun\SalaryPackagingDeduction;

use \RedBeanPHP\R as R;

class GetSalaryPackagingDeduction
{
    function __invoke($data)
    {
        $payrun_ref = $data['payrun_id'];

        $salary_packaging_deductions = R::getAll('SELECT * FROM salarypackagingdeductions WHERE payrun_ref=:payrun_ref', [':payrun_ref' => $payrun_ref]);

        return ['http_code' => 200, 'result' => $salary_packaging_deductions];
    }
}
