<?php
namespace NDISmate\Models\Trip;

use \RedBeanPHP\R as R;

class ListTrips
{
    function __invoke($data)
    {
        $beans = R::getAll('SELECT trips.*, clients.name as client_name 
        FROM trips 
        LEFT JOIN clients ON clients.id = trips.client_id 
        WHERE trips.staff_id = :staff_id
        ORDER BY trips.id desc', [':staff_id' => $data['staff_id']]);

        // return $trips;
        if (!count($beans)) {
            throw new \Exception('Not Found', 404);
        } else {
            return $beans;
        }
    }
}
