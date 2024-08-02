<?php
namespace NDISmate\Models\Payroll\Template\EarningsLine;

use \RedBeanPHP\R as R;

class ListEarningsLinesByStaffId
{
    function __invoke($data)
    {
        // get xero_employee_ref from staff_id
        $result = R::getAll(
            'SELECT *
            FROM payrolltemplateearningslines
            WHERE staff_id=:staff_id',
            [
                ':staff_id' => $data['staff_id']
            ]
        );

        return ['http_code' => 200, 'result' => $result];
    }
}
