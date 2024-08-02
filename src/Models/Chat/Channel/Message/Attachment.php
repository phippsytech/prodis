<?php
namespace NDISmate\Models\Chat\Channel\Message;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class Attachment extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("chatchannelmessageattachment", [
            'id' => [v::numericVal()],
            'message_id' => [v::numericVal()],
            'type' => [v::stringType()],
            'url' => [v::stringType()],
            'data' => [v::stringType()]
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addAttachment", "create", true, ["super","sysadmin"]],
            ["listAttachments", "getAll", true, []],
            ["getAttachment", "getOne", true, []],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:
    // function getClientsByPlanManagerId($data){
    //     return (new \NDISmate\Models\PlanManager\GetClientsByPlanManagerId)($data);
    // }

}