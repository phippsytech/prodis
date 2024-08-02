<?php
namespace NDISmate\Models\Register;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class Feedback extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("feedback", [
            'id' => [v::numericVal()],
            'date_identified' => [v::stringType()],
            'date_resolved' => [v::stringType()],
            'type' => [v::stringType()],  // compliment, complaint
            'message' => [v::stringType()],
            'resolution' => [v::stringType()],
            'email' => [v::stringType()],
            'name' => [v::stringType()],
            'phone' => [v::stringType()],
            'status' => [v::stringType()], // open, closed
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addFeedback", "create", false, []],
            ["listFeedbacks", "getAll", true, ["admin"]],
            ["getFeedback", "getOne", true, ["admin"]],
            ["updateFeedback", "update", true, ["admin"]],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function create($data){

        $data['date_identified'] = date("Y-m-d");
        $data['status'] = "open";
        return $this->CRUD->create($data);

    }

    function update($data){
            
        // this prevents us from updating a closed risk unless we are opening it
        
        $original = $this->CRUD->getOne($data['id']);
        if($original['result']['status'] == "closed"){

            if($data['status'] == "open"){
                return $this->CRUD->update($data);
            }

            return ["http_code"=>409, "error_message"=>"Can't update closed feedback"];

        }

        if($data['status'] == "closed"){
            $data['date_resolved'] = date("Y-m-d");
        }
            
        return $this->CRUD->update($data);

    }



}