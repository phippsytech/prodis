<?php
namespace NDISmate\Models\Payroll\Payrun;

use \RedBeanPHP\R as R;

class GetStaffIdByXeroEmployeeRef
{
    function __invoke($data)
    {
        // get xero_employee_ref from staff_id
        $staff_id = R::getCell(
            'SELECT id
            FROM staffs
            WHERE xero_employee_ref=:xero_employee_ref',
            [
                ':xero_employee_ref' => $data['xero_employee_ref']
            ]
        );

        if ($staff_id == null) {
            return ['http_code' => 400, 'error' => 'No employee found with xero_employee_ref: ' . $data['xero_employee_ref']];
        }

        return ['http_code' => 200, 'staff_id' => $staff_id];
    }
}
