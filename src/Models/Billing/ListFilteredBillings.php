<?php
namespace NDISmate\Models\Billing;

use \RedBeanPHP\R as R;

class ListFilteredBillings
{
    public function __invoke($data)
    {
        $bean = R::getAll(
            "SELECT 
                timetrackings.id,
                timetrackings.client_id,
                timetrackings.service_id,
                timetrackings.staff_id,
                timetrackings.session_date,
                timetrackings.end_time,
                timetrackings.start_time,
                timetrackings.actual_travel_time,
                timetrackings.session_duration,
                timetrackings.kilometers_travelled,
                timetrackings.claim_type,
                timetrackings.cancellation_reason,
                timetrackings.invoice_batch,
                timetrackings.unit_type,
                services.name AS service_name,
                services.code AS service_code,
                clients.name AS client_name
            FROM timetrackings 
            LEFT JOIN services ON (timetrackings.service_id = services.id)              
            LEFT JOIN clients ON (clients.id = timetrackings.client_id)
            WHERE timetrackings.staff_id=:staff_id AND timetrackings.session_date >= :start_date AND timetrackings.session_date <= :end_date
            AND (timetrackings.unit_type<>'km' OR timetrackings.unit_type is null)
            AND session_duration > 0
            ORDER BY timetrackings.session_date DESC
        ", [
                ':staff_id' => $data['staff_id'],
                ':start_date' => $data['start_date'],
                ':end_date' => $data['end_date']
            ]
        );

        if (!$bean) {
            return ['http_code' => 404, 'error_message' => 'Not Found'];
        } else {
            return ['http_code' => 200, 'result' => $bean];  // R::exportAll($beans, TRUE)
        }
    }
}
