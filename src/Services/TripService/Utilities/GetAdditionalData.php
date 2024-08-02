<?php
namespace NDISmate\Services\TripService\Utilities;

use RedBeanPHP\R as R;

class GetAdditionalData
{
    function __invoke($data)
    {
        if ($data['participant_service_id']) {
            $client_plan_service = R::getRow(
                'SELECT
                service_id,
                plan_manager_id
            FROM clientplanservices
            WHERE id = :participant_service_id',
                [':participant_service_id' => $data['participant_service_id']]
            );

            $data['service_id'] = $client_plan_service['service_id'];
            $data['planmanager_id'] = $client_plan_service['plan_manager_id'];
        }
        return $data;
    }
}
