<?php
namespace NDISmate\Models\TimeTracking;

use RedBeanPHP\R as R;

class ListTimeTrackings
{
    public function __invoke($data)
    {
        $beans = R::getAll(
            'SELECT 
                timetrackings.id,
                timetrackings.actual_travel_time,
                timetrackings.session_date,
                timetrackings.session_duration,
                timetrackings.invoice_batch,
                timetrackings.client_id,
                timetrackings.service_id,
                timetrackings.staff_id,
                staffs.name as staff_name,
                services.name as service_name,
                services.code as service_code,
                clients.name as client_name
             FROM timetrackings 
            left join services on (timetrackings.service_id = services.id) 
            left join staffs on (timetrackings.staff_id = staffs.id) 
            left join clients on (timetrackings.client_id = clients.id) 
            Order by timetrackings.session_date desc, timetrackings.start_time desc
        '
        );
        if (!count($beans)) {
            throw new \Exception('Not Found', 404);
        } else {
            return $beans;
        }
    }
}
