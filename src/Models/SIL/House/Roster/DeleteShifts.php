<?php
namespace NDISmate\Models\SIL\House\Roster;

use \RedBeanPHP\R as R;

class DeleteShifts {

    public function __invoke($data) {

        if (!isset($data['client_id'])) {
            return ["http_code"=>400, "error_message"=>"client_id is required"];
        }

        $start_date = $data['start_date'];
        $end_date = date('Y-m-d', strtotime($start_date . ' +1 week'));

        $start_timestamp = strtotime($start_date);        
        $current_timestamp = time();

        if ($start_timestamp < $current_timestamp ) {
            return ["http_code"=>400, "error_message"=>"Shifts before the end of the current week cannot be deleted.","current_timestamp"=>$current_timestamp, "end_timestamp"=>$end_timestamp ,"end_date"=>$end_date, "start_date"=>$start_date];
        }

        $house_id = R::findOne('houses', ' client_id=:client_id ', [":client_id"=>$data['client_id']])->id;

        $bean = R::exec( 
            'DELETE
            FROM houserosters

            WHERE house_id=:house_id
                AND houserosters.start_date >= :start_date 
                AND houserosters.start_date < :end_date ',
            [ 
                ":house_id"=>$house_id,
                ":start_date"=>$start_date,
                ":end_date"=>$end_date,
            ]
        );
    
        return ["http_code"=>200, "result"=>$bean];

    }
}



