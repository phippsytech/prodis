<?php
namespace NDISmate\Models\SIL\House;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 
use \RedBeanPHP\R as R;
use \NDISmate\CORE\KeyValue;


class Roster extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("houseroster", [
            'id' => [v::numericVal()],
            'house_id' => [v::stringType()],
            'start_date' => [v::stringType()],
            'start_time' => [v::stringType()],
            'end_date' => [v::stringType()],
            'end_time' => [v::stringType()],
            'kms' => [v::numericVal()],
            'staff_id' => [v::stringType()],
            // 'shift_type' => [v::stringType()],
            // 'group_ids' => [v::stringType()], 
            'do_not_bill' => [v::boolVal()],  
            'bill_kms' => [v::boolVal()],  
            'vehicle_type' => [v::stringType()],
            'passive' => [v::boolVal()],  
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addShift" , "create", false, ["house.lead", "admin"] ],
            ["getShift", "getOne", true, ["house","house.lead","admin"] ],
            ["listShifts", "getAll", true, ["admin"] ],
            ["updateShift", "update", true, ["house.lead", "admin"] ],
            ["deleteShift", "delete", true, ["house.lead","admin"] ],
            ["deleteShifts", "deleteShifts", true, ["admin"] ],
            ["getRoster" , "getRoster", false, [] ],
            ["getRosters" , "getRosters", false, [] ],
            ["getShifts" , "getShifts", false, [] ],
            ["getShiftsByStaffId" , "getShiftsByStaffId", false, [] ],
            ["getBilling" , "getBilling", false, [] ],
            ["getKmsByStaffId", "getKmsByStaffId", false, [] ],
            ["generateSessions" , "generateSessions", false, [] ],
            // ["addShiftsFromTemplate" , "addShiftsFromTemplate", false, [] ],
            ["pasteShifts" , "pasteShifts", false, [] ],
            ["payrollSnafu","payrollSnafu", false, [] ],
            // ["archiveHouse", "archive", true, [] ],
            // ["restoreHouse", "restore", true, [] ],
            
        ];

        return $this->invoke($request, $response, $args, $this);
    }










    // Additional Methods and overrides here:


    function create($data){

        $billing_period_start_date = (string) KeyValue::get('sil_billing_period_start_date');

        if( $data['start_date'] < $billing_period_start_date){
            return ["http_code"=>400, "error_message"=>"This date has been locked from editing."];
        }


        if (!isset($data['end_date'])){
            $data['end_date'] = $data['start_date'];
            if ($data['end_time']<$data['start_time']){
                $data['end_date'] = date('Y-m-d', strtotime($data['end_date'].' +1 day'));
            }
        }

        // if ($data['shift_type']=="group"){
        //     $data['staff_id'] = null;
        //     $data['group_ids']=json_encode($data['group_ids']);
        //     $data['kms']=null;
        // }

        // if ($data['shift_type']=="individual"){
        //     $data['group_ids'] = null;
        // }

        if ($data['client_id']){
            $house = R::findOne('houses',' client_id=:client_id ',[":client_id"=>$data['client_id']]);
            if ($house){
                $data['house_id'] = $house->id;
            } 
        }


        $result = $this->CRUD->create([
            'house_id'=>$data['house_id'],
            'start_date'=>$data['start_date'],
            'start_time'=>$data['start_time'],
            'end_date'=>$data['end_date'],
            'end_time'=>$data['end_time'],
            'kms'=>$data['kms'],
            'staff_id'=>$data['staff_id'],
            // 'group_ids'=>$data['group_ids'], // an array of staff ids
            // 'shift_type'=>$data['shift_type'],  //individual or group

            // this is a bit silly.  I need to make this consistent.
            'do_not_bill'=>$data['do_not_bill'], // boolean 1 = do not bill.  0 or null = bill.
            'bill_kms'=>$data['bill_kms'], // boolean  1= bill.  0 or null = don't bill.
            'vehicle_type'=>$data['vehicle_type'],


            'passive'=>$data['passive'],
        ]);
        
        return ["http_code"=>201, "result"=>$result];

    }


    function update($data){

        // TODO: if an id doesn't exist, what are we updating?  should no id return an error?
        if(isset($data['id'])) $shift['id'] = $data['id'];

        if (array_key_exists('staff_id', $data)) $shift['staff_id'] = $data['staff_id'];
        if (array_key_exists('passive', $data)) $shift['passive'] = $data['passive']; 
        
        if(isset($data['do_not_bill'])) $shift['do_not_bill'] = $data['do_not_bill'];
        if(isset($data['bill_kms'])) $shift['bill_kms'] = $data['bill_kms'];
        

        if(isset($data['house_id'])) $shift['house_id'] = $data['house_id'];
        if(isset($data['start_date'])) $shift['start_date'] = $data['start_date'];
        if(isset($data['start_time'])) $shift['start_time'] = $data['start_time'];
        if(isset($data['end_date'])) $shift['end_date'] = $data['end_date'];
        if(isset($data['end_time'])) $shift['end_time'] = $data['end_time'];
        if(isset($data['kms'])) $shift['kms'] = $data['kms'];
        if(isset($data['vehicle_type'])) $shift['vehicle_type'] = $data['vehicle_type'];


        $result = $this->CRUD->update($shift);
        
        return ["http_code"=>200, "result"=>$result['result']];

    }


    function getOne($data){
        
        $result = $this->CRUD->getOne($data['id']);
        $result = $result['result'];

        // $result['group_ids'] = json_decode($result['group_ids'],true);

        return ["http_code"=>201, "result"=>$result];

    }

    function deleteShifts($data){
        return (new \NDISmate\Models\SIL\House\Roster\DeleteShifts)($data);
    }

    function getKmsByStaffId($data){
        $kms = (new \NDISmate\Models\SIL\House\Roster\GetKmsByStaffId)($data);
        return ["http_code"=>200, "result"=>$kms];
    }

    function getRoster($data){
        return (new \NDISmate\Models\SIL\House\Roster\GetRoster)($data);
    }

    function getRosters($data){
        return (new \NDISmate\Models\SIL\House\Roster\GetRosters)($data);
    }

    function pasteShifts($data){
        return (new \NDISmate\Models\SIL\House\Roster\PasteShifts)($data);
    }

    function payrollSnafu(){
        return (new \NDISmate\Models\SIL\House\Roster\PayrollSnafu)();
    }

    function getShifts($data){
        $shifts = (new \NDISmate\Models\SIL\House\Roster\GetShifts)($data);
        return ["http_code"=>200, "result"=>$shifts];
    }

    function getShiftsByStaffId($data){
        $shifts = (new \NDISmate\Models\SIL\House\Roster\GetShiftsByStaffId)($data);
        return ["http_code"=>200, "result"=>$shifts];
    }


    function addShiftsFromTemplate($data){
        return (new \NDISmate\Models\SIL\House\Roster\AddShiftsFromTemplate)($this, $data);
    }

}