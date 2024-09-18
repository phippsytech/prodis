<?php
namespace NDISmate\Models\TimeTracking;

use \RedBeanPHP\R as R;

class GetTimeTracking
{
    public function __invoke($data)
    {
        $bean = R::getRow(
            'SELECT 
                timetrackings.id,
                timetrackings.client_id,
                timetrackings.planmanager_id,
                timetrackings.service_id,
                timetrackings.service_booking_id,
                timetrackings.staff_id,
                timetrackings.session_date,
                timetrackings.end_time,
                timetrackings.start_time,
                timetrackings.actual_travel_time,
                timetrackings.session_duration,
                timetrackings.kilometers_travelled,
                timetrackings.claim_type,
                timetrackings.cancellation_reason,
                clientcasenotes.notes,
                clientcasenotes.internal,
                timetrackings.invoice_batch,
                staffs.name as staff_name,
                services.name as service_name,
                services.code as service_code,
                clients.name as client_name,
                planmanagers.name as planmanager_name
             FROM timetrackings 
             left join services on (timetrackings.service_id = services.id) 
             left JOIN clientcasenotes on (clientcasenotes.timetracking_id = timetrackings.id)
             left JOIN staffs on (staffs.id = timetrackings.staff_id)
             join clients on (clients.id = timetrackings.client_id)
             left join planmanagers on (planmanagers.id = timetrackings.planmanager_id)
            WHERE timetrackings.id=:timetrackings_id
            ORDER BY session_date desc
        ', [':timetrackings_id' => $data['id']]
        );
        if (!$bean) {
            throw new \Exception('Not Found', 404);
        } else {
            // Fetch all errors for the current timetracking_id
            $error = R::getRow(
                'SELECT 
                    billablendiaerrors.payment_request_id,
                    ndiapaymentrequeststatuss.error_message
                FROM billablendiaerrors 
                join ndiapaymentrequeststatuss on (ndiapaymentrequeststatuss.id = billablendiaerrors.payment_request_id)
                WHERE timetracking_id=:timetracking_id',
                [':timetracking_id' => $data['id']]
            );

            if ($error) {
                $payment_request_id = $error['payment_request_id'];

                // Fetch all timetracking_ids for the fetched payment_request_id
                $timetracking_ids = R::getAll(
                    'SELECT 
                        timetracking_id
                    FROM billablendiaerrors 
                    join timetrackings on (timetrackings.id = billablendiaerrors.timetracking_id)
                    WHERE payment_request_id=:payment_request_id
                    AND timetrackings.id != :timetracking_id
                    ',
                    [
                        ':payment_request_id' => $payment_request_id,
                        ':timetracking_id' => $data['id']
                    ]
                );

                if ($timetracking_ids) {
                    // $ids = [];
                    // foreach ($timetracking_ids as $timetracking_id) {
                    //     $ids[] = $timetracking_id['timetracking_id'];
                    // }
                    $error['related_billables'] = $timetracking_ids;
                }

                $bean['error'] = $error;
            }
            return $bean;
        }
    }
}
