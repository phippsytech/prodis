<?php
namespace NDISmate\Models\TimeTracking;

use RedBeanPHP\R as R;

class ListTimeTrackingByStaffId
{
    public function __invoke($data)
    {
        $isNotNull = isset($data['billed']) && $data['billed'] == 'true' ? true : false;

        $beans = R::getAll(
            'SELECT 
            timetrackings.id,
            timetrackings.session_date,
            timetrackings.claim_type,
            timetrackings.cancellation_reason,
            timetrackings.actual_travel_time,
            timetrackings.kilometers_travelled,
            timetrackings.session_duration,
            timetrackings.invoice_batch,
            timetrackings.client_id,
            timetrackings.service_id,
            timetrackings.staff_id,
            timetrackings.trip_id,
            timetrackings.unit_type,
            staffs.name as staff_name,
            services.name as service_name,
            services.code as service_code,
            clients.name as client_name
         FROM timetrackings 
         left join services on (timetrackings.service_id = services.id) 
         left JOIN staffs on (staffs.id = timetrackings.staff_id)
         join clients on (clients.id = timetrackings.client_id)
        WHERE timetrackings.staff_id=:staff_id
        AND timetrackings.invoice_batch ' . ($isNotNull ? 'IS NOT NULL' : 'IS NULL') . '
        ORDER BY session_date desc
    ', [':staff_id' => $data['staff_id']]
        );
        if (!count($beans)) {
            throw new \Exception('Not Found', 404);
        } else {
            return $beans;
        }
    }
}
