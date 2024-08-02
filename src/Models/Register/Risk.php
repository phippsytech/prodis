<?php
namespace NDISmate\Models\Register;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class Risk extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("risk", [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'date_identified' => [v::stringType()],
            'date_resolved' => [v::stringType()],
            'type' => [v::stringType()],
            'description' => [v::stringType()],
            'resolution' => [v::stringType()],
            'submitted_by' => [v::stringType()],
            'status' => [v::stringType()], // open, closed

        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addRisk", "create", true, ["admin"]],
            ["listRisks", "getAll", true, []],
            ["getRisk", "getOne", true, []],
            ["updateRisk", "update", true, ["admin"]],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function create($data){

        $data['date_identified'] = date("Y-m-d");
        return $this->CRUD->create($data);

    }

    function update($data){
            
        // this prevents us from updating a closed risk unless we are opening it
        
        $original = $this->CRUD->getOne($data['id']);
        if($original['result']['status'] == "closed"){

            if($data['status'] == "open"){
                return $this->CRUD->update($data);
            }

            return ["http_code"=>409, "error_message"=>"Can't update closed risk"];

        }

        if($data['status'] == "closed"){
            $data['date_resolved'] = date("Y-m-d");
        }
            
        return $this->CRUD->update($data);

    }



}