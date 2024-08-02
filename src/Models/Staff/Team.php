<?php
namespace NDISmate\Models\Staff;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 
use \RedBeanPHP\R as R;


class Team extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("team", [
            'id' => [v::numericVal()],
            'supervisor_id' => [v::numericVal()], // The staff id of the supervisor
            'staff_id' => [v::numericVal()], // The staff id of the team member
        ]);


        // action, class method, guard, roles
        $this->actions = [
            ["addTeamMember", "addTeamMember", true, ["admin"]],
            ["removeTeamMember", "removeTeamMember", true, ["admin"]],
            ["listStaffBySupervisorId", "listStaffBySupervisorId", true, ["admin"]],
            ["listSupervisorsByStaffId", "listSupervisorsByStaffId", true, ["admin"]],
        ];

        return $this->invoke($request, $response, $args, $this);
    }


    // Additional Methods and overrides here:

    function addTeamMember($data){
        $result = (new \NDISmate\Models\Staff\Team\Add)($data);
        return ["http_code"=>201, "result"=>$result];
    }

    function removeTeamMember($data){
        $result =  (new \NDISmate\Models\Staff\Team\Remove)($data);
        return ["http_code"=>200, "result"=>$result];
    }

    function listStaffBySupervisorId($data){
        $result = (new \NDISmate\Models\Staff\Team\ListStaffBySupervisorId)($data);
        return ["http_code"=>200, "result"=>$result];
    }

    function listSupervisorsByStaffId($data){
        $result =  (new \NDISmate\Models\Staff\Team\ListSupervisorsByStaffId)($data);
        return ["http_code"=>200, "result"=>$result];
    }

}