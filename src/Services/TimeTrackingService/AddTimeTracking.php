<?php
namespace NDISmate\Services\TimeTrackingService;

use NDISmate\Models\Client\CaseNote;
use NDISmate\Models\TimeTracking;
use RedBeanPHP\R as R;

class AddTimeTracking
{
    public function __invoke($data)
    {
        if (!isset($data['service_booking_id'])) {
            throw new \Exception('Participant Service Id is required.', 400);
        }

        $client_plan_service = R::getRow(
            'SELECT 
                plan_manager_id,
                service_id
            FROM servicebookings
            WHERE id = :service_booking_id',
            [':service_booking_id' => $data['service_booking_id']]
        );

        $data['planmanager_id'] = $client_plan_service['plan_manager_id'];
        $data['service_id'] = $client_plan_service['service_id'];

        $data['rate'] = R::getCell(
            'SELECT 
                services.rate
            FROM servicebookings
            JOIN services ON services.id = servicebookings.service_id
            WHERE servicebookings.id = :service_booking_id',
            [':service_booking_id' => $data['service_booking_id']]
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
