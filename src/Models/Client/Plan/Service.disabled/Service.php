<?php

/*
 * TODO: think about issues with updating a service that has timesheet data
 * Timesheet data if the service is wrong
 */

namespace NDISmate\Models\Client\Plan;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class Service extends BaseModel
{
    // var $model;
    // var $actions;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('clientplanservice', [
            'id' => [v::numericVal()],
            'plan_id' => [v::numericVal()],
            'service_id' => [v::numericVal()],
            'budget' => [v::digit('.')],  // a dollar amount
            'include_travel' => [v::boolType()],
            'kilometer_budget' => [v::numericVal()],
            'max_claimable_travel_duration' => [v::numericVal()],
            'xero_account_code' => [v::numericVal()],
            'plan_manager_id' => [v::numericVal()],
            // 'billing_name' => [v::stringType()],
            // 'billing_email' => [v::stringType()],
            // 'hours_allocated' => [v::numericVal()], // hourly rate for provision of service
            // 'start_date' => [v::stringType()],  // Max allowable travel for service (between 0 and 30 minutes)
            // 'end_date' => [v::stringType()], // optional
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['addClientPlanService', 'create', true, []],
            ['getClientPlanService', 'getOne', true, []],
            ['listClientPlanServices', 'getAll', true, []],
            ['listClientPlanServicesByClientId', 'getAllByClientId', true, []],
            ['listProviderTravelByClientId', 'getProviderTravelByClientId', true, []],
            ['updateClientPlanService', 'update', true, []],
            ['deleteClientPlanService', 'delete', true, ['admin']],
            ['getClientServiceSummary', 'getClientServiceSummary', false, []],
            ['getAvailableSessionDuration', 'getAvailableSessionDuration', false, []],
            // ["archiveClientPlanService", "archive", true, [] ],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function getOne($data)
    {
        return (new \NDISmate\Models\Client\Plan\Service\GetOne)($data);
    }

    function getAll($data)
    {
        return (new \NDISmate\Models\Client\Plan\Service\GetAll)($data);
    }

    function getAvailableSessionDuration($data)
    {
        return (new \NDISmate\Models\Client\Plan\Service\GetAvailableSessionDuration)($data);
    }

    function getAllByClientId($data)
    {
        return (new \NDISmate\Models\Client\Plan\Service\GetAllByClientId)($data);
    }

    function getProviderTravelByClientId($data)
    {
        return (new \NDISmate\Models\Client\Plan\Service\GetProviderTravelByClientId)($data);
    }

    function getClientServiceSummary($data)
    {
        return (new \NDISmate\Models\Client\Plan\Service\GetClientServiceSummary)($data);
    }
}
