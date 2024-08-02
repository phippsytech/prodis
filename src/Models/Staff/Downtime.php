<?php
namespace NDISmate\Models\Staff;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;
// use NDISmate\CORE\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class Downtime extends BaseModel {


    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("staffdowntime", [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'recurrence_type' => [v::stringType()], // Type of recurrence ("daily," "weekly," "fortnightly," etc.) or "one-time".
            'recurring_start_date' => [v::stringType()], // Start date of the recurring downtime (for recurring entries).
            'recurring_end_date' => [v::stringType()], // End date of the recurring downtime (for recurring entries).
            'recurring_day_of_week' => [v::stringType()], // Day of the week for weekly or fortnightly recurrence (for recurring entries).
            'start_time' => [v::stringType()], // Start time of the downtime.
            'end_time' => [v::stringType()], // End time of the downtime.
            'start_date' => [v::stringType()], // Start date of the downtime.
            'end_date' => [v::stringType()], // End date of the downtime.
            'reason' => [v::stringType()] // Reason for the downtime.
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addDowntime" , "create", true, [] ],
            ["getDowntime", "getOne", true, [] ],
            ["listDowntimes", "getAll", true, [] ],
            ["updateDowntime", "update", true, [] ],
            ["deleteDowntime", "delete", true, [] ],
            ["listDowntimeByStaffId", "listDowntimeByStaffId", false, [] ],
            ["getAvailableStaff", "getAvailableStaff", false, [] ],
        ];

        return $this->invoke($request, $response, $args, $this);
        
    }

    /**
     * $start_date_time, 
     * $end_date_time
     * $house_id
     */
    function getAvailableStaff($data) {
        // // Example usage:
        // $start_date_time = '2023-07-22 10:00:00';
        // $end_date_time = '2023-07-22 15:00:00';
        // $available_staff_ids = getAvailableStaffIDs($start_date_time, $end_date_time);

        $available_staff = (new \NDISmate\Models\Staff\Downtime\GetAvailableStaff)($data);
        return ["http_code"=>200, "result"=>$available_staff];

    }

}