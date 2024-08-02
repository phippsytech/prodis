<?php
namespace NDISmate\Models\SIL\RosterOfCare\Shift;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;


class GetShifts{

    function __invoke($data){


        $start_date = date('Y-m-d', strtotime($data['info']['startStr'].' -1 day'));
        $end_date = date('Y-m-d', strtotime($data['info']['endStr'].' +1 day'));


        $sql = 'SELECT 
            rosterofcareshifts.id, 
            rosterofcareshifts.rosterofcare_id, 
            rosterofcareshifts.start_date, 
            rosterofcareshifts.start_time, 
            rosterofcareshifts.end_date, 
            rosterofcareshifts.end_time,
            CONCAT(rosterofcareshifts.start_date, " ", rosterofcareshifts.start_time) AS start, 
            CONCAT(rosterofcareshifts.end_date, " ", rosterofcareshifts.end_time) AS end
        FROM rosterofcareshifts
        WHERE rosterofcareshifts.start_date >= :start_date 
        AND rosterofcareshifts.start_date <= :end_date 
        AND rosterofcareshifts.rosterofcare_id=:rosterofcare_id
        ';

        $params = [':start_date' => $start_date, ':end_date' => $end_date, ':rosterofcare_id' => $data['id']];



        $sql .=' ORDER BY rosterofcareshifts.start_date, rosterofcareshifts.start_time';

        // print $sql;

        $roster = R::getAll($sql, $params);
        


        $colors = array(
            // "#cccccc",
            "#ccccff",
            // "#cccc99",
            "#ccffcc",
            // "#ccffff",
            "#cc99ff",
            // "#cc99cc",
            // "#cc99ff",
            "#cc9999",
            "#ffcccc",
            // "#ffccff",
            "#ffcc99",
            // "#ffffcc",
            // "#ffffff",
            "#ffff99",
            // "#ff99cc",
            "#ff99ff",
            // "#ff9999",
            "#99cccc",
            // "#99ccff",
            "#99cc99",
            // "#99ffcc",
            "#99ffff",
            // "#99ff99",
            "#9999cc",
            // "#9999ff",
            // "#999999",
            );    
    
    
            
            foreach ($roster as &$bean) {
    
                $bean['backgroundColor'] = "#4F46E5";
                
                
                
                if( $bean['start_date'] >= $billing_period_start_date && $editable){
                    $editable = true;
                }else{
                    $editable = false;
                }
    
    
    
    
                $duration = (strtotime($bean['end']) - strtotime($bean['start'])) / 60/60;
    
                    $bean['titleHTML'] = $this->convertNameFormat($bean['title']);
                    if($bean['passive']){
                        $bean['titleHTML'] .= "<span style='font-weight:normal'>Passive</span>";
                    }else{
                        $bean['titleHTML'] .= "<span style='font-weight:normal'>".$duration."hrs</span>";
                    }
    
    
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
