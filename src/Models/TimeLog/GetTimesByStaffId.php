<?php
namespace NDISmate\Models\SIL\House\Roster;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;


class GetShiftsByStaffId{

    function __invoke($data){

        $sql = 'SELECT 
            timelogs.id, 
            timelogs.client_id, 
            timelogs.start_date, 
            timelogs.start_time, 
            timelogs.end_date, 
            timelogs.end_time, 
            timelogs.do_not_bill,
            timelogs.description,
            staffs.id AS staff_id, 
            staffs.name AS staff_name,
            clients.name as client_name    
        FROM timelogs
        LEFT JOIN staffs ON staffs.id = timelogs.staff_id
        left join clients on clients.id = timelogs.client_id
        WHERE timelogs.staff_id = :staff_id';

        $params = [':staff_id' => $data['staff_id']];

        $sql .=' ORDER BY timelogs.start_date, timelogs.start_time';

        $timelogs = R::getAll($sql, $params);

        return $timelogs;

    }

}