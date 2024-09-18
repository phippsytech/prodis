<?php
namespace NDISmate\Services\TripService\Utilities;

use RedBeanPHP\R as R;

class GetAdditionalData
{
    function __invoke($data)
    {
        if ($data['service_booking_id']) {
            $client_plan_service = R::getRow(
                'SELECT
                service_id,
                plan_manager_id
            FROM servicebookings
            WHERE id = :service_booking_id',
                [':service_booking_id' => $data['service_booking_id']]
            );

            $data['service_id'] = $client_plan_service['service_id'];
            $data['planmanager_id'] = $client_plan_service['plan_manager_id'];
        }
        return $data;
    }
}
