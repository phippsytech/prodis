<?php
namespace NDISmate\Models\Credential;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 
use \RedBeanPHP\R as R;


class Rule extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

/*
        {
            name:"100 Points of ID",
            staff_groups:["therapist", "sil"],
            verification_method:"points",
            point_goal:100,
            credentials:[
                {id: 1, points: 70 },
                {id: 2, points: 70 },
                {id: 3, points: 70 },
                {id: 4, points: 70 },
                {id: 5, points: 40 },
                {id: 6, points: 40 },
                {id: 7, points: 40 },
                {id: 8, points: 40 },
            ]
        }
*/


        $this->CRUD = new CRUD("credentialrule", [
            'id' => [v::numericVal()],
            'name' => [v::numericVal()],
            'staff_groups' => [v::stringType()],
            'verfification_method' => [v::stringType()],
            'point_goal' => [v::numericVal()],
            'credentials' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addRule" , "addRule", true, ["admin"] ],
            ["getRule", "getRule", true, ["admin"] ],
            ["listRules", "listRules", true, ["admin"] ],
            ["updateRule", "updateRule", true, ["admin"] ],
            ["deleteRule", "delete", true, ["admin"] ],
        ];

        return $this->invoke($request, $response, $args, $this);
    }


    // Additional Methods and overrides here:

    function addRule($data){
        if(isset($data['staff_groups'])){
            $data['staff_groups'] = json_encode($data['staff_groups']);
        }
        if(isset($data['credentials'])){
            $data['credentials'] = json_encode($data['credentials']);
        }
        $result = $this->create($data);
        $result = $result['result'];
        return ["http_code"=>201, "result"=>$result];
    }

    function getRule($data){
        $result = $this->getOne($data);
        $result = $result['result'];
        $result['staff_groups'] = json_decode($result['staff_groups'], true);
        $result['credentials'] = json_decode($result['credentials'], true);
        return ["http_code"=>200, "result"=>$result];
    }

    function listRules($data){
        $result = $this->getAll($data);
        $result = $result['result'];
        foreach($result as &$row){
            $row['staff_groups'] = json_decode($row['staff_groups'], true);
            $row['credentials'] = json_decode($row['credentials'], true);
        }
        return ["http_code"=>200, "result"=>$result];
    }

    function updateRule($data){
        if(array_key_exists('staff_groups', $data)){
            $data['staff_groups'] = json_encode($data['staff_groups']);
        }
        if(array_key_exists('credentials', $data)){
            $data['credentials'] = json_encode($data['credentials']);
        }
        $result = $this->update($data);
        $result = $result['result'];
        return ["http_code"=>200, "result"=>$data];
    }

}