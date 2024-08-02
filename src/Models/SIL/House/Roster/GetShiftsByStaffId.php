<?php
namespace NDISmate\Models\SIL\House\Roster;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;


class GetShiftsByStaffId{

    function __invoke($data){

        $sql = 'SELECT 
            houserosters.id, 
            houserosters.house_id, 
            houserosters.start_date, 
            houserosters.start_time, 
            houserosters.end_date, 
            houserosters.end_time, 
            CONCAT(houserosters.start_date, " ", houserosters.start_time) AS start, 
            CONCAT(houserosters.end_date, " ", houserosters.end_time) AS end, 
            houserosters.passive,
            houserosters.kms,
            houserosters.do_not_bill,
            houserosters.bill_kms,
            houserosters.shift_type,
            houserosters.vehicle_type,
            staffs.id AS staff_id, 
            staffs.name AS staff_name,
            clients.name as client_name,
            clients.name as title    
        FROM houserosters
        LEFT JOIN staffs ON staffs.id = houserosters.staff_id
        left join houses on houses.id = houserosters.house_id
        left join clients on clients.id = houses.client_id
        WHERE houserosters.staff_id = :staff_id';


        $params = [':staff_id' => $data['staff_id']];
        
        if (isset($data['start_date'])) {
            $sql .=' AND houserosters.start_date >= :start_date ';
            $params[':start_date'] = $data['start_date'];
        }

        if (isset($data['end_date'])) {
            $sql .=' AND houserosters.start_date <= :end_date';
            $params[':end_date'] = $data['end_date'];
        }

        $sql .=' ORDER BY houserosters.start_date, houserosters.start_time';

        

        $shifts = R::getAll($sql, $params);

        return $shifts;

    }

}