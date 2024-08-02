<?php
namespace NDISmate\Models\SIL\House\Roster;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetShifts
{
    function __invoke($data)
    {
        $sql = "SELECT 
            houserosters.id, 
            houserosters.house_id, 
            houserosters.start_date, 
            houserosters.start_time, 
            houserosters.end_date, 
            houserosters.end_time, 
            houserosters.kms,
            houserosters.do_not_bill,
            houserosters.bill_kms,
            houserosters.vehicle_type,
            houserosters.passive,
            houserosters.shift_type,
            houserosters.group_ids,
            CONCAT(houserosters.start_date, ' ', houserosters.start_time) AS start, 
            CONCAT(houserosters.end_date, ' ', houserosters.end_time) AS end, 
            staffs.id AS staff_id, 
            staffs.name AS title,
            houses.id as house_id
        FROM houserosters
        LEFT JOIN staffs ON staffs.id = houserosters.staff_id
        JOIN houses on houses.id = houserosters.house_id
        WHERE houserosters.start_date >= :start_date 
        AND houserosters.start_date <= :end_date 
        ";

        $params = [':start_date' => $data['start_date'], ':end_date' => $data['end_date']];

        /** TODO:  I need to work out how to clean up shifts that are not passive that start before the end_date but finish after the end_date. */

        /**
         * IF YOU ARE THINKING ABOUT ADDING TIME BACK IN TO THE QUERY, DON'T!!
         * USE MIDNIGHT AS THE START END TIME.
         * PAYROLL SYSTEMS LIKE XERO DON'T HANDLE A PAY PERIOD THAT STARTS HALFWAY THROUGH A DAY.
         */

        // WHERE
        //         houserosters.start_date >= :start_date
        //         AND (
        //             houserosters.start_date > :start_date
        //             OR houserosters.start_time >= :start_time
        //         )
        //         AND houserosters.end_date <= :end_date
        //         AND (
        //             houserosters.end_date < :end_date
        //             OR houserosters.end_time <= :end_time

        // $params = [':start_date' => $data['start_date'], ':end_date' => $data['end_date'], ':start_time' => $data['start_time'], ':end_time' => $data['end_time']];

        if (isset($data['house_id'])) {
            $sql .= ' AND houses.id = :house_id';
            $params[':house_id'] = $data['house_id'];
        }

        if (isset($data['client_id'])) {
            $sql .= ' AND houses.client_id = :client_id';
            $params[':client_id'] = $data['client_id'];
        }

        $sql .= ' ORDER BY houserosters.start_date, houserosters.start_time';

        // print $sql;

        $shifts = R::getAll($sql, $params);

        return $shifts;
    }
}
