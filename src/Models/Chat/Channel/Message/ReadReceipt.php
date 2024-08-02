<?php
namespace NDISmate\Models\Chat\Channel\Message;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class ReadReceipt extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("chatchannelmessageattachment", [
            'id' => [v::numericVal()],
            'message_id' => [v::numericVal()],
            'user_id' => [v::numericVal()],
            // 'created' => [v::stringType()]
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addReadReceipt", "create", true, ["super","sysadmin"]],
            ["listReadReceipts", "getAll", true, []],
            ["getReadReceipt", "getOne", true, []],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:
    // function getClientsByPlanManagerId($data){
    //     return (new \NDISmate\Models\PlanManager\GetClientsByPlanManagerId)($data);
    // }

}