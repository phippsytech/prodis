<?php
namespace NDISmate\Services\TimeTrackingService;

use NDISmate\Models\Client\CaseNote;
use NDISmate\Models\TimeTracking;
use RedBeanPHP\R as R;

class UpdateTimeTracking
{
    public function __invoke($data)
    {
        if ($data['participant_service_id']) {
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
        }

        // Update Time Tracking
        $timetracking = new TimeTracking();
        $timetracking->update($data);

        // Update related case note
        if (isset($data['notes'])) {
            $casenote = R::findOne('clientcasenotes', ' timetracking_id=:timetracking_id ', [':timetracking_id' => $data['id']]);
            if ($casenote) {
                (new CaseNote)->update([
                    'id' => $casenote->id,
                    'staff_id' => $data['staff_id'],
                    'client_id' => $data['client_id'],
                    'notes' => $data['notes'],
                    'internal' => $data['internal'],
                    'timetracking_id' => $data['id']
                ]);
            } else {
                (new CaseNote)->create([
                    'staff_id' => $data['staff_id'],
                    'client_id' => $data['client_id'],
                    'notes' => $data['notes'],
                    'internal' => $data['internal'],
                    'timetracking_id' => $data['id']
                ]);
            }
        }

        return;
    }
}
