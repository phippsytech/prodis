<?php
namespace NDISmate\Models\SIL\House;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 
use \RedBeanPHP\R as R;


class Staff extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("housestaff", [
            'id' => [v::numericVal()],
            'house_id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'level' => [v::numericVal()],
            'house_lead' => [v::boolVal()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addHouseStaffer" , "create", true, [] ],
            ["getHouseStaffer", "getOne", true, [] ],
            ["deleteHouseStaffer", "delete", true, [] ],
            ["listHouseStaff", "listHouseStaff", false, [] ],
            ["listHouseStaffByClientId", "listHouseStaffByClientId", false, []],
            ["listHouseStaffByHouseId", "listHouseStaffByHouseId", false, []],
            ["makeHouseLead", "makeHouseLead", false, [] ],
            ["removeHouseLead", "removeHouseLead", false, [] ],
            ["updateHouseStaffer", "update", true, [] ],
            // ["archiveHouse", "archive", true, [] ],
            // ["restoreHouse", "restore", true, [] ],
            
            
        ];



        return $this->invoke($request, $response, $args, $this);
    }


    // Additional Methods and overrides here:

    function listHouseStaff($data){

        $query="SELECT 
            staffs.name, 
            staffs.id as staff_id
        FROM housestaffs 
        JOIN staffs on (staffs.id=housestaffs.staff_id)
        GROUP BY staffs.id
        ";

        $beans = R::getAll($query);
        
        if (count($beans)==0) return ["http_code"=>404, "error_message"=>"Not found."];
        
        return ["http_code"=>200, "result"=>$beans];  //R::exportAll($beans, TRUE)
        
    }   

    function listHouseStaffByHouseId($data){

        $query="SELECT 
            staffs.name, 
            staffs.id as staff_id,  
            housestaffs.id, 
            housestaffs.house_lead,
            housestaffs.level  
        FROM housestaffs 
        JOIN staffs on (staffs.id=housestaffs.staff_id)
        join houses on (houses.id=housestaffs.house_id)
        WHERE houses.id=:house_id";
        
        $params = [':house_id'=>$data['house_id']];
        
        $beans = R::getAll($query, $params);
        
        if (count($beans)==0) return ["http_code"=>404, "error_message"=>"Not found."];
        
        return ["http_code"=>200, "result"=>$beans];  //R::exportAll($beans, TRUE)
        
    }   


    function listHouseStaffByClientId($data){

        $query="SELECT 
            staffs.name, 
            staffs.id as staff_id,  
            housestaffs.id, 
            housestaffs.house_lead,
            housestaffs.level  
        FROM housestaffs 
        JOIN staffs on (staffs.id=housestaffs.staff_id)
        join houses on (houses.id=housestaffs.house_id)
        WHERE houses.client_id=:client_id";
        
        $params = [':client_id'=>$data['client_id']];
        
        $beans = R::getAll($query, $params);
        
        if (count($beans)==0) return ["http_code"=>404, "error_message"=>"Not found."];
        
        return ["http_code"=>200, "result"=>$beans];  //R::exportAll($beans, TRUE)
        
    }   

    function makeHouseLead($data){

        // checking for existing record
        $bean = R::findOne('housestaffs', 'staff_id=:staff_id and house_id=:house_id', [':staff_id'=>$data['staff_id'], ':house_id'=>$data['house_id']]);

        if(count($bean)==0){ // no existing record.
            return ["http_code"=>404, "error_message"=>"Staff not found"]; // return 201 to indicate created
        }

        // first make the staff member house_lead
        // This will create the house_lead column if it does not exist.
        
        $result = $this->getOne(['id'=>$bean['id']]);
        
        $result = $result['result'];
        $result['house_lead']=true;
        $update_query= "UPDATE housestaffs set house_lead=false";
 
        $result = $this->update($result);
        $result = $result['result'];

        $update_query = $update_query.' WHERE house_id=:house_id AND staff_id!=:staff_id';

        R::exec( 
            $update_query, 
            [":house_id"=>$data['house_id'], ":staff_id"=>$data['staff_id']] 
        );
        
        return ["http_code"=>200, "result"=>$result];

    }


    
    function removeHouseLead($data){

        // checking for existing record
        $bean = R::findOne('housestaffs', 'staff_id=:staff_id and house_id=:house_id', [':staff_id'=>$data['staff_id'], ':house_id'=>$data['house_id']]);  

        if(count($bean)==0){ // no existing record.
            return ["http_code"=>404, "error_message"=>"Staff not found"]; // return 201 to indicate created
        }

        $result = $this->getOne(['id'=>$bean['id']]);
        $result = $result['result'];
        $result['house_lead']=false;
        $result = $this->update($result);
        $result = $result['result'];
        
        return ["http_code"=>200, "result"=>$result];
    }











}