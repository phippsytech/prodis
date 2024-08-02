<?php
namespace NDISmate\Models;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class Billing extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('billing', [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],  // Required
            'client_id' => [v::numericVal()],  // Required
            'planmanager_id' => [v::numericVal()],  // Required
            'start_date' => [v::stringType()],  // Required
            'start_time' => [v::stringType()],
            'end_date' => [v::stringType()],
            'end_time' => [v::stringType()],
            'quantity' => [v::numericVal()],  // Required // this is the duration for time based billing
            'unit_price' => [v::numericVal()],  // Required // the rate at time of billing
            'support_item_number' => [v::stringType()],
            'claim_type' => [v::stringType()],  // "ClaimType",
            'cancellation_reason' => [v::stringType()],  // "CancellationReason"
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['addBilling', 'create', true, []],
            ['getBilling', 'getOne', true, []],
            ['listBillings', 'getAll', true, []],
            ['listFilteredBillings', 'listFilteredBillings', true, []],
            ['updateBilling', 'update', true, []],
            ['deleteBilling', 'delete', true, []],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    function listFilteredBillings($data)
    {
        return (new \NDISmate\Models\Billing\ListFilteredBillings)($data);
    }
}
