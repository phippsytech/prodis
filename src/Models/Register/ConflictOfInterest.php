<?php
namespace NDISmate\Models\Register;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class ConflictOfInterest extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("conflictofinterest", [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'date_identified' => [v::stringType()],
            'date_resolved' => [v::stringType()],
            'submitted_by' => [v::stringType()],
            'parties_involved' => [v::stringType()],
            'description' => [v::stringType()],
            'type' => [v::stringType()],
            'resolution' => [v::stringType()],
            
            'status' => [v::stringType()], // open, closed
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addConflictOfInterest", "create", true, ["admin"]],
            ["listConflictOfInterests", "getAll", true, []],
            ["getConflictOfInterest", "getOne", true, []],
            ["updateConflictOfInterest", "update", true, ["admin"]],
            ["deleteConflictOfInterest", "delete", true, ["admin"]],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function create($data){

        $data['date_identified'] = date("Y-m-d");
        return $this->CRUD->create($data);

    }

    function update($data){
            
        // this prevents us from updating a closed conflict of interest unless we are opening it
        
        $original = $this->CRUD->getOne($data['id']);
        if($original['result']['status'] == "resolved"){

            if($data['status'] == "unresolved" || $data['status'] == 'ongoing'){
                return $this->CRUD->update($data);
            }

            return ["http_code"=>409, "error_message"=>"Can't update closed conflict of interest"];

        }

        if($data['status'] == "resolved"){
            $data['date_resolved'] = date("Y-m-d");
        }
            
        return $this->CRUD->update($data);

    }

    function delete($data) {

        $original = $this->CRUD->getOne($data['id']);

        if (isset($original['result'])) {
           return  $this->CRUD->delete($data['id']);
        }
    }

}