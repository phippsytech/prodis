<?php
namespace NDISmate\Models\Payroll;

use \RedBeanPHP\R as R;

class GetEmployeeByStaffId
{
    function __invoke($xero, $data)
    {
        // get xero_employee_ref from staff_id
        $xero_employee_ref = R::getCell(
            'SELECT xero_employee_ref 
            FROM staffs
            WHERE id=:staff_id',
            [
                ':staff_id' => $data['staff_id']
            ]
        );

        if ($xero_employee_ref == null) {
            return ['http_code' => 400, 'error' => 'No employee found with staff_id: ' . $data['staff_id']];
        }

        return (new \NDISmate\Models\Payroll\GetEmployee)($xero, ['employee_id' => $xero_employee_ref]);
    }
}
