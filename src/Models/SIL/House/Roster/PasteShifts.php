<?php
namespace NDISmate\Models\SIL\House\Roster;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;
use \NDISmate\CORE\CRUD;
use Respect\Validation\Validator as v; 

class PasteShifts{

    function __invoke($data){

        $houseroster = new CRUD("houseroster", [
            'id' => [v::numericVal()],
            'house_id' => [v::stringType()],
            'start_date' => [v::stringType()],
            'start_time' => [v::stringType()],
            'end_date' => [v::stringType()],
            'end_time' => [v::stringType()],
            'kms' => [v::numericVal()],
            'staff_id' => [v::stringType()],
            'shift_type' => [v::stringType()],
            'group_ids' => [v::stringType()], 
            'do_not_bill' => [v::boolVal()],  
        ]);

        // get difference between from start_date and to start_date in days
        $from = new \DateTime($data['from_date']);
        $to = new \DateTime($data['to_date']);
        $diff = $from->diff($to);
        $days = $diff->days;


        $shifts = (new \NDISmate\Models\SIL\House\Roster\GetShifts)([
            "client_id"=>$data['client_id'],
            "start_date"=>$data['from_date'],
            "end_date"=>date('Y-m-d', strtotime($data['from_date'].' +6 day'))
        ]);

        

        //need to get the house id from the client id
        $house = R::findOne('houses',' client_id=:client_id ',[":client_id"=>$data['client_id']]);
        if ($house){
            $data['house_id'] = $house->id;
        }

        

        foreach($shifts as $shift){
            $shift['house_id'] = $data['house_id'];
            // adjust the shift date 
            $shift['start_date'] = date('Y-m-d', strtotime($shift['start_date'].' +'.$days.' day'));
            $shift['end_date'] = date('Y-m-d', strtotime($shift['end_date'].' +'.$days.' day'));

            $shift_exists = R::findOne('houserosters',' 
            house_id=:house_id AND 
            start_date=:start_date AND 
            start_time=:start_time AND 
            end_date=:end_date AND 
            end_time=:end_time AND '.
            (($shift['staff_id'])?'staff_id='.$shift['staff_id']:'staff_id IS NULL')
            .' AND '.
            (($shift['group_ids'])?'group_ids='.$shift['group_ids']:'group_ids IS NULL')
            .' AND '.
            (($shift['do_not_bill'])?'do_not_bill='.$shift['do_not_bill']:'do_not_bill IS NULL')
            .' AND '.
            (($shift['shift_type'])?'shift_type="'.$shift['shift_type'].'"':'shift_type IS NULL')
        ,[
            ':house_id'=>$shift['house_id'],
            ':start_date'=>$shift['start_date'],
            ':start_time'=>$shift['start_time'],
            ':end_date'=>$shift['end_date'],
            ':end_time'=>$shift['end_time']
        ]);

        if ($shift_exists) continue;


            // "paste" the new shift
            $result = $houseroster->create([
                'house_id'=>$shift['house_id'],
                'start_date'=>$shift['start_date'],
                'start_time'=>$shift['start_time'],
                'end_date'=>$shift['end_date'],
                'end_time'=>$shift['end_time'],
                // 'kms'=>$shift['kms'],
                'staff_id'=>$shift['staff_id'],
                'group_ids'=>$shift['group_ids'], // an array of staff ids
                'shift_type'=>$shift['shift_type'],  //individual or group
                'do_not_bill'=>$shift['do_not_bill'],
            ]);
        }

        return ["http_code"=>201, "result"=>$query];

    }

}


