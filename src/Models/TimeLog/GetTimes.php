<?php
namespace NDISmate\Models\TimeLog;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;


class GetTimes{

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
            CONCAT(timelogs.start_date, " ", timelogs.start_time) AS start, 
            CONCAT(timelogs.end_date, " ", timelogs.end_time) AS end, 
            timelogs.staff_id AS staff_id, 
            staffs.name AS title,
            clients.name as client_name
        FROM timelogs
        LEFT JOIN staffs ON staffs.id = timelogs.staff_id
        LEFT JOIN clients on clients.id = timelogs.client_id
        WHERE timelogs.start_date >= :start_date 
        AND timelogs.start_date <= :end_date 
        ';

        $params = [':start_date' => $data['start_date'], ':end_date' => $data['end_date']];

        if (isset($data['staff_id'])) {
            $sql .=' AND timelogs.staff_id = :staff_id';
            $params[':staff_id'] = $data['staff_id'];
        }

        $sql .=' ORDER BY timelogs.start_date, timelogs.start_time';

        $timelogs = R::getAll($sql, $params);

        return $timelogs;

    }

}