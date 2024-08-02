<?php
namespace NDISmate\Services\TripService;

use RedBeanPHP\R as R;

class GetKMs
{
    function __invoke($data)
    {
        $sql = 'SELECT 
        id,
        trip_date as start_date,
        null as start_time,
        null as end_date,
        null as end_time,
        kms as duration,            
        staff_id,
        "SIL TRAVEL" as service_code
        FROM trips
        WHERE trip_date >= :start_date 
        AND trip_date <= :end_date 
        ';

        $params = [':start_date' => $data['start_date'], ':end_date' => $data['end_date']];

        if (isset($data['client_id'])) {
            $sql .= ' AND client_id = :client_id';
            $params[':client_id'] = $data['client_id'];
        }

        $sql .= ' ORDER BY trip_date';

        // print $sql;

        $kms = R::getAll($sql, $params);

        return $kms;
    }
}
