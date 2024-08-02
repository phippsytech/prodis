<?php
namespace NDISmate\Models\Payroll\Template;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class EarningsLine extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('payrolltemplateearningsline', [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'earnings_item_ref' => [v::stringType()],
            'name' => [v::stringType()],
            'quantity' => [v::numericVal()],
            'rate' => [v::numericVal()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['addEarningsLine', 'create', true, ['payroll']],
            ['listEarningsLinesByStaffId', 'listEarningsLinesByStaffId', false, ['payroll']],
            ['deleteEarningsLine', 'delete', true, ['payroll']],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    function listEarningsLinesByStaffId($data)
    {
        $result = (new \NDISmate\Models\Payroll\Template\EarningsLine\ListEarningsLinesByStaffId)($data);
        return $result;
    }
}
