<?php
namespace NDISmate\Services\TimeTrackingService;

use NDISmate\Models\Client\CaseNote;
use NDISmate\Models\TimeTracking;
use RedBeanPHP\R as R;

class AddTimeTracking
{
    public function __invoke($data)
    {
        if (!isset($data['participant_service_id'])) {
            throw new \Exception('Participant Service Id is required.', 400);
        }

        $client_plan_service = R::getRow(
            'SELECT 
                plan_manager_id,
                service_id
            FROM clientplanservices
            WHERE id = :participant_service_id',
            [':participant_service_id' => $data['participant_service_id']]
        );

        $data['planmanager_id'] = $client_plan_service['plan_manager_id'];
        $data['service_id'] = $client_plan_service['service_id'];

        $data['rate'] = R::getCell(
            'SELECT 
                services.rate
            FROM clientplanservices
            JOIN services ON services.id = clientplanservices.service_id
            WHERE clientplanservices.id = :participant_service_id',
            [':participant_service_id' => $data['participant_service_id']]
        );

        // Add Time Tracking
        $timetracking = new TimeTracking();
        $timetracking->create($data);

        // get time tracking id
        $timetracking_id = $timetracking->id;

        // add case note
        $data['timetracking_id'] = $timetracking_id;

        $casenote = new CaseNote();
        $casenote->create($data);

        return ['id' => $timetracking_id];
    }
}
