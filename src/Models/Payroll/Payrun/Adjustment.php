<?php
namespace NDISmate\Models\Payroll\Payrun;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class Adjustment extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('payrunadjustment', [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'pay_item_xero_ref' => [v::stringType()],
            'pay_item_type' => [v::stringType()],
            'quantity' => [v::numericVal()],
            'rate' => [v::numericVal()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['addAdjustment', 'create', true, ['payroll']],
            ['getAdjustment', 'getOne', true, ['payroll']],
            ['listAdjustments', 'listAdjustments', true, ['payroll']],
            ['listAdjustmentsByStaffId', 'listAdjustmentsByStaffId', false, []],
            ['updateAdjustment', 'update', true, ['payroll']],
            ['deleteAdjustment', 'delete', true, ['payroll']]
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    // Additional Methods and overrides here:

    function listAdjustments($data)
    {
        $adjustments = (new \NDISmate\Models\Payroll\Payrun\Adjustment\ListAdjustments)($data);
        return ['http_code' => 200, 'result' => $adjustments];
    }

    function listAdjustmentsByStaffId($data)
    {
        $adjustments = (new \NDISmate\Models\Payroll\Payrun\Adjustment\ListAdjustmentsByStaffId)($data);
        return ['http_code' => 200, 'result' => $adjustments];
    }
}
