<?php
namespace NDISmate\Models\Payroll\Payrun;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetSILEarnings
{
    function __invoke($data)
    {
        $sql = 'SELECT 
            houserosters.id, 
            houserosters.start_date, 
            houserosters.start_time, 
            houserosters.end_date, 
            houserosters.end_time, 
            houserosters.passive,
            staffs.id AS staff_id, 
            staffs.name AS staff_name
            FROM houserosters
            LEFT JOIN staffs ON staffs.id = houserosters.staff_id
            WHERE houserosters.staff_id = :staff_id
            AND houserosters.start_date >= :start_date 
            AND houserosters.start_date <= :end_date
        ';

        $params = [
            ':staff_id' => $data['staff_id'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date']
        ];

        $sql .= ' ORDER BY houserosters.start_date, houserosters.start_time';

        $shifts = R::getAll($sql, $params);

        return $shifts;
    }
}
