<?php
namespace NDISmate\Services\TimeTrackingService;

use RedBeanPHP\R as R;

class GetMinutes
{
    public function __invoke($data)
    {
        $beans = R::getRow(
            'SELECT 
                id,
                sum(session_duration) as total_session_duration,
                client_id,
                service_id
            FROM timetrackings 
            WHERE client_id=:client_id 
                AND service_id=:service_id',
            [':client_id' => $data['client_id'], ':service_id' => $data['service_id']]
        );
        if (!count($beans)) {
            throw new \Exception('Not Found', 404);
        } else {
            return $beans;
        }
    }
}
