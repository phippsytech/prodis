<?php
namespace NDISmate\Models\Payroll\Template;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class TaxLine extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('payrolltemplatetaxline', [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'manual_tax_type' => [v::stringType()],
            'name' => [v::stringType()],
            'amount' => [v::numericVal()],
            // 'description' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['addTaxLine', 'create', true, ['payroll']],
            ['listTaxLinesByStaffId', 'listTaxLinesByStaffId', false, ['payroll']],
            ['deleteTaxLine', 'delete', true, ['payroll']],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    function listTaxLinesByStaffId($data)
    {
        $result = (new \NDISmate\Models\Payroll\Template\TaxLine\ListTaxLinesByStaffId)($data);
        return $result;
    }
}
