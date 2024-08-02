<?php
namespace NDISmate\Services\TimeTrackingService;

use RedBeanPHP\R as R;

class GetSummary
{
    public function __invoke($data)
    {
        $isNotNull = isset($data['billed']) && $data['billed'] == 'true' ? true : false;

        $beans = R::getAll(
            'SELECT 
                timetrackings.id,
                timetrackings.actual_travel_time,
                timetrackings.kilometers_travelled,
                timetrackings.session_date,
                timetrackings.claim_type,
                timetrackings.cancellation_reason,
                timetrackings.session_duration,
                timetrackings.invoice_batch,
                timetrackings.client_id,
                timetrackings.service_id,
                timetrackings.staff_id,
                timetrackings.trip_id,
                staffs.name AS staff_name,
                services.name AS service_name,
                services.code AS service_code,
                clients.name AS client_name
            FROM timetrackings 
            LEFT JOIN services ON (timetrackings.service_id = services.id) 
            LEFT JOIN staffs ON (timetrackings.staff_id = staffs.id) 
            LEFT JOIN clients ON (timetrackings.client_id = clients.id) 
            WHERE timetrackings.staff_id=:staff_id
                AND (clients.archived IS NULL OR clients.archived !=1)
                AND timetrackings.invoice_batch ' . ($isNotNull ? 'IS NOT NULL' : 'IS NULL') . '
            ORDER by timetrackings.session_date desc, timetrackings.client_id, timetrackings.start_time desc
        ', [':staff_id' => $data['staff_id']]
        );
        if (!count($beans)) {
            throw new \Exception('Not Found', 404);
        } else {
            return $beans;
        }
    }
}
