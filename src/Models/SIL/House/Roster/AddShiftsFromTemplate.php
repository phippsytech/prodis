<?php
namespace NDISmate\Models\SIL\House\Roster;

// use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class AddShiftsFromTemplate{

    function __invoke($obj, $data){

        // $data contains $week_start_date and $house id

        // set up day offsets for calculating dates
        $day_offset=[
            "Monday"=>0,
            "Tuesday"=>1,
            "Wednesday"=>2,
            "Thursday"=>3,
            "Friday"=>4,
            "Saturday"=>5,
            "Sunday"=>6
        ];

        // Get the template
        $template = (new \NDISmate\Models\SIL\House\Roster\Template)->getTemplate(["house_id"=>$data['house_id']]);
        $template = $template["result"]["template"];

        // Loop through the template 
        foreach($template as $day){
            foreach($day["shifts"] as $shift){

                // Calculate the start and end dates for the shift
                $start_date = date('Y-m-d', strtotime($data['week_start_date'] . ' + ' . $day_offset[$day["day"]] . ' days'));
                if ($shift["end"] < $shift["start"]) {
                    $end_date = date('Y-m-d', strtotime($start_date . ' + 1 days'));
                } else {
                    $end_date = $start_date;
                }
                
                // Check if a shift already exists for shift we are creating (match day and start time and staff id)
                $shift_bean = R::findOne( 
                    'houserosters', 
                    ' house_id=:house_id
                    AND start_date=:start_date
                    AND start_time=:start_time
                    AND end_date=:end_date
                    AND end_time=:end_time
                    AND staff_id=:staff_id ',
                    [
                        ':house_id'=>$data['house_id'],
                        ':start_date'=>$start_date,
                        ':start_time'=>$shift['start'],
                        ':end_date'=>$end_date,
                        ':end_time'=>$shift['end'],
                        ':staff_id'=>$shift['staff_id'],
                    ]
                );

                // If the shift exists, we skip
                if($shift_bean) continue;

                $result = $obj->CRUD->create([
                    'house_id'=>$data['house_id'],
                    'start_date'=>$start_date,
                    'start_time'=>$shift['start'],
                    'end_date'=>$end_date,
                    'end_time'=>$shift['end'],
                    'staff_id'=>$shift['staff_id'],
                ]);
            }
        }

        return ["http_code"=>201];

    }
    
}
