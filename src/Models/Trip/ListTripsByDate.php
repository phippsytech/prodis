<?php
namespace NDISmate\Models\Trip;

use \RedBeanPHP\R as R;

class ListTripsByDate
{
    function __invoke($data)
    {
        $beans = R::getAll('SELECT trips.*, clients.name as client_name, staffs.name as staff_name 
            FROM trips 
            LEFT JOIN clients ON clients.id = trips.client_id
            INNER JOIN staffs ON staffs.id = trips.staff_id
            WHERE trips.trip_date BETWEEN :start_date AND :end_date
            AND (:client_id IS NULL OR trips.client_id = :client_id)
            AND (:staff_id IS NULL OR trips.staff_id = :staff_id)
            ORDER BY trips.trip_date DESC, trips.client_id, trips.staff_id', [
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date'],
                ':client_id' => $data['client_id'] ?? null,
                ':staff_id' => $data['staff_id'] ?? null
            ]
        );

        if (!count($beans)) {
            throw new \Exception('Not Found', 404);
        } else {
            return $beans;
        }

    }
}
