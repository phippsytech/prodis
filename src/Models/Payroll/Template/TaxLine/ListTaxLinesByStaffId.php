<?php
namespace NDISmate\Models\Payroll\Template\TaxLine;

use \RedBeanPHP\R as R;

class ListTaxLinesByStaffId
{
    function __invoke($data)
    {
        // get xero_employee_ref from staff_id
        $result = R::getAll(
            'SELECT *
            FROM payrolltemplatetaxlines
            WHERE staff_id=:staff_id',
            [
                ':staff_id' => $data['staff_id']
            ]
        );

        return ['http_code' => 200, 'result' => $result];
    }
}
