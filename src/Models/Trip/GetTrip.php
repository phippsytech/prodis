<?php
namespace NDISmate\Models\Trip;

use RedBeanPHP\R as R;

class GetTrip
{
    public function __invoke($data)
    {
        $trip = R::load('trips', $data['id']);

        if (!$trip) {
            throw new \Exception('Trip Not Found', 404);
        } else {
            return $trip;
        }
    }
}
