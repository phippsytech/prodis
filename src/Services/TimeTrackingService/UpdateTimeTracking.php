<?php

namespace NDISmate\Services\TimeTrackingService;

use NDISmate\Models\Client\CaseNote;
use NDISmate\Models\TimeTracking;
use RedBeanPHP\R as R;

class UpdateTimeTracking
{
    public function __invoke($data)
    {
        if ($data['service_booking_id']) {

            $client_plan_service = R::getRow(
                'SELECT 
                service_id
            FROM servicebookings
            WHERE id = :service_booking_id',
                [':service_booking_id' => $data['service_booking_id']]
            );

            $data['service_id'] = $client_plan_service['service_id'];


            $data['rate'] = R::getCell(
                'SELECT 
                servicebookings.rate
            FROM servicebookings
            JOIN services ON services.id = servicebookings.service_id
            WHERE servicebookings.id = :service_booking_id',
                [':service_booking_id' => $data['service_booking_id']]
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
