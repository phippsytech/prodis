<?php
namespace NDISmate\Models\Chat;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class Channel extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("chatchannel", [
            'id' => [v::numericVal()],
            'name' => [v::stringType()], // Name of Channel
            'members' => [v::stringType()], // contains a list of user ids
            
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addChannel", "create", true, ["super","sysadmin"]],
            ["listChannels", "getAll", true, []],
            ["getChannel", "getOne", true, []],
            ["updateChannel", "update", true, ["super","sysadmin"]],           
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:
    // function getClientsByPlanManagerId($data){
    //     return (new \NDISmate\Models\PlanManager\GetClientsByPlanManagerId)($data);
    // }

}