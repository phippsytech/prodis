<?php
namespace NDISmate\Models\TimeLog;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;

class GetTimeLog{

    function __invoke($data){


        if(!isset($data['editable'])) $data['editable']=false;

        $start_date = date('Y-m-d', strtotime($data['info']['startStr'].' -1 day'));
        $end_date = date('Y-m-d', strtotime($data['info']['endStr'].' +1 day'));


        if(!empty($data['staff_id'])){
            
            $roster = (new \NDISmate\Models\TimeLog\GetTimes)([
                "staff_id"=>$data['staff_id'],
                'start_date'=>$start_date,
                // 'start_time'=>"00:00",
                'end_date'=>$end_date,
                // 'start_time'=>"00:00"
            ]);
        }


        // Loop through the shift beans and assign a color to each staff member
        
        foreach ($roster as &$bean) {
            $editable = $data['editable'];
            
            $bean['backgroundColor'] = "#4f46e533";
            $editable = true;
            
            if($editable){
                $bean['backgroundColor'] = "#4f46e5cc";
            }

            if (is_null($bean['title'])) {
                $bean['title'] = "Unassigned";
                $bean['backgroundColor'] = "#4f46e5cc";
            }

            $duration = (strtotime($bean['end']) - strtotime($bean['start'])) / 60/60;

            $bean['titleHTML']="";

            if(!empty($bean['description'])) $bean['titleHTML'] .= "<span style='font-weight:normal'>".$bean['description']."</span><br/>";

            // $bean['titleHTML'] = $this->convertNameFormat($bean['title']);
            $bean['titleHTML'] .= "<span style='font-weight:normal;font-size:0.85em'>";
            $bean['titleHTML'] .= $duration."hrs ";

            if(isset($bean['client_name'])) $bean['titleHTML'] .= $bean['client_name'];
            $bean['titleHTML'] .= "</span>";            
 
            $bean['editable'] = $editable;

        }

        return ["http_code"=>200, "result"=>$roster];

    }

    function convertNameFormat($name) {
        // Split the name into first name and last name
        $nameParts = explode(' ', $name);
        
        // Extract the first character of the first name and convert it to uppercase
        $first_name = $nameParts[0];
        
        // Get the last name intial
        $last_name_initital = strtoupper(substr($nameParts[1], 0, 1));
        
        
        // Concatenate the first name and last name initial with a space in between
        $convertedName = $first_name . ' ' . $last_name_initital;
        
        // Return the converted name
        return $convertedName;
    }
    
}
