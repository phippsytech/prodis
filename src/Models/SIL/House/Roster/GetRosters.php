<?php
namespace NDISmate\Models\SIL\House\Roster;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;

class GetRosters{

    function __invoke($data){

        $billing_period_start_date = (string) KeyValue::get('sil_billing_period_start_date');
        if(!$billing_period_start_date){
            return ["http_code"=>400, "error_message"=>"Billing period start date is not set.  Cannot retrieve roster."];
        }

        if(!isset($data['editable'])) $data['editable']=false;

        $start_date = date('Y-m-d', strtotime($data['info']['startStr'].' -1 day'));
        $end_date = date('Y-m-d', strtotime($data['info']['endStr']));




            
            $roster = (new \NDISmate\Models\SIL\House\Roster\GetShifts)([
                // "client_id"=>$data['client_id'],
                'start_date'=>$start_date,
                'end_date'=>$end_date,
            ]);

// if(!$roster){
//     return ["http_code"=>400, "error_message"=>"client_id or house_id is required" , "data"=>$data["client_id"]];
// }

        // Define the colors array
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


        // src of colours: https://sashamaps.net/docs/resources/20-colors/
// and this search result got me good hits.  https://search.brave.com/search?q=I+need+a+list+of+as+many+visually+different+colours&source=desktop

        $colors = array(  '#e6194B', '#3cb44b', '#ffe119', '#4363d8', '#f58231', '#911eb4', '#42d4f4', '#f032e6', '#bfef45', '#fabed4', '#469990', '#dcbeff', '#9A6324', '#fffac8', '#800000', '#aaffc3', '#808000', '#ffd8b1', '#000075', '#a9a9a9');

        // shuffle($colors);

        $bw_colors = array(
            "#666666",
            "#777777",
            "#888888",
            "#999999",
            "#aaaaaa",
            "#bbbbbb",
            "#cccccc",
            "#dddddd",
            "#eeeeee",
            "#ffffff",
        );

        // Create a mapping between staff members and colors
        $color_map = array();
        foreach ($roster as $bean) {
            $house_id = $bean['house_id'];
            $staff_name = $bean['title'];
            if (!array_key_exists($house_id, $color_map)) {
                $color_map[$house_id] = array_shift($colors);
            }
        }



        $bw_color_map = array();
        foreach ($roster as $bean) {
            $house_id = $bean['house_id'];
            $staff_name = $bean['title'];
            if (!array_key_exists($house_id, $bw_color_map)) {
                $bw_color_map[$house_id] = array_shift($bw_colors);
            }
        }

        // Loop through the shift beans and assign a color to each staff member
        
        foreach ($roster as &$bean) {
            $editable = $data['editable'];
            $bean['backgroundColor'] = $bw_color_map[$bean['house_id']]."33";
            $bean['backgroundColor'] = $color_map[$bean['house_id']]."33";
            
            
            if( $bean['start_date'] >= $billing_period_start_date && $editable){
                $editable = true;
            }else{
                $editable = false;
            }


            // if($editable){
                $bean['backgroundColor'] = $color_map[$bean['house_id']]."cc";
            // }


            if (is_null($bean['title'])) {
                $bean['title'] = "Unassigned";
                // $bean['backgroundColor'] = "#ff0000";
            }

            if($bean['shift_type']=="group" && !empty($bean['group_ids'])){
                $bean['group_ids'] = json_decode($bean['group_ids'], true);
                $bean['backgroundColor'] = "#ffcc00";
            }

            $duration = (strtotime($bean['end']) - strtotime($bean['start'])) / 60/60;

            if($bean['shift_type']=="group"){
                $bean['titleHTML'] = "GROUP - <span style='font-weight:normal'>".$duration."hrs</span>";
            }else{
                $bean['titleHTML'] = $this->convertNameFormat($bean['title'])."</span>";
            }
            
            if($bean['kms']>0) $bean['titleHTML'] .= " - <span style='font-weight:normal; font-style:italic'>".$bean['kms']."kms</span>";
            if($bean['do_not_bill']==1) $bean['titleHTML'] .= " - <span style='font-weight:normal; font-style:italic'>Do Not Bill</span>";

            $bean['editable'] = false;//$editable;
            
        }


        usort($roster, function ($a, $b){
            if ($a['house_id'] == $b['house_id']) {
                return strtotime($a['start']) - strtotime($b['start']);
            }
            return $a['house_id'] - $b['house_id'];
        });


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
