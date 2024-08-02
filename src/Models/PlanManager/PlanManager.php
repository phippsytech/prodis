<?php
namespace NDISmate\Models;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class PlanManager extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("planmanager", [
            'id' => [v::numericVal()],
            'name' => [v::stringType()], // Name of PlanManager
            'email' => [v::stringType()], // Email address to send invoices to.
            'xero_contact_ref' => [v::stringType()], // Xero Contact Id of this plan manager in Xero.
            'archived' => [v::boolVal()],  // true or false (turns into 1 or 0)
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addPlanManager", "create", true, ["admin"]],
            ["listPlanManagers", "getAll", true, []],
            ["getPlanManager", "getOne", true, []],
            ["updatePlanManager", "update", true, ["admin"]],
            ["archivePlanManager", "archive", true, ["admin"]],
            ["getClientsByPlanManagerId", "getClientsByPlanManagerId", true, ["admin"]],
            ["restorePlanManager", "restore", true, ["admin"]],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:


    function getClientsByPlanManagerId($data){
        return (new \NDISmate\Models\PlanManager\GetClientsByPlanManagerId)($data);
    }
}