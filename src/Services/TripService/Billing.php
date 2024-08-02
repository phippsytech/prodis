<?php
namespace NDISmate\Services\TripService;

use NDISmate\Models\TimeTracking;
use RedBeanPHP\R as R;

class Billing
{
    function addDistance($data)
    {
        $data['claim_type'] = 'TRAN';
        $data['unit_type'] = 'km';

        if (isset($data['service_id']))
            $data['rate'] = $this->getRate($data['service_id']);

        (new TimeTracking)->create($data);
    }

    function addDuration($data)
    {
        $data['claim_type'] = 'TRAN';
        if (isset($data['service_id']))
            $data['rate'] = $this->getRate($data['service_id']);

        (new TimeTracking)->create($data);
    }

    function updateDistance($data)
    {
        $timetracking = R::findOne('timetrackings', "trip_id=:trip_id AND unit_type='km'", [':trip_id' => $data['trip_id']]);

        if (!$timetracking) {
            $this->addDistance($data);
            return;
        }

        $timetracking->staff_id = $data['staff_id'];
        $timetracking->client_id = $data['client_id'];
        $timetracking->service_id = $data['service_id'];
        $timetracking->participant_service_id = $data['participant_service_id'];
        $timetracking->planmanager_id = $data['planmanager_id'];
        $timetracking->session_date = $data['session_date'];
        $timetracking->session_duration = $data['session_duration'];

        if (isset($data['service_id']))
            $timetracking->rate = $this->getRate($data['service_id']);

        R::store($timetracking);
    }

    function updateDuration($data)
    {
        $timetracking = R::findOne('timetrackings', "trip_id=:trip_id AND (unit_type != 'km' OR unit_type IS NULL)", [':trip_id' => $data['trip_id']]);

        if (!$timetracking) {
            $this->addDuration($data);
            return;
        }

        $timetracking->staff_id = $data['staff_id'];
        $timetracking->client_id = $data['client_id'];
        $timetracking->service_id = $data['service_id'];
        $timetracking->participant_service_id = $data['participant_service_id'];
        $timetracking->planmanager_id = $data['planmanager_id'];
        $timetracking->session_date = $data['session_date'];
        $timetracking->session_duration = $data['session_duration'];

        if (isset($data['service_id']))
            $timetracking->rate = $this->getRate($data['service_id']);

        R::store($timetracking);
    }

    function delete($data)
    {
        $trip_id = $data['id'];
        R::hunt('timetrackings', 'trip_id=:trip_id', [':trip_id' => $trip_id]);
    }

    function getRate($service_id)
    {
        $rate = R::getCell(
            'SELECT 
                rate
            FROM services
            WHERE id = :service_id',
            [':service_id' => $service_id]
        );

        return $rate;
    }
}
