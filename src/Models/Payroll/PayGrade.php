<?php
namespace NDISmate\Models\Payroll;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator as v;
use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;

class PayGrade extends BaseModel
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->CRUD = new CRUD('xeropaygrade', [
            'id' => [v::numericVal()],
            'name' => [v::stringType()],
            'day' => [v::stringType()],
            'evening' => [v::stringType()],
            'night' => [v::stringType()],
            'saturday' => [v::stringType()],
            'sunday' => [v::stringType()],
            'public_holiday' => [v::stringType()],
            'sleep_over' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ['addPayGrade', 'create', true, ['admin']],
            ['getPayGrade', 'getOne', true, ['admin']],
            ['listPayGrades', 'getAll', true, ['admin']],
            ['listPayGradesByStaffId', 'listPayGradesByStaffId', false, []],
            ['updatePayGrade', 'update', true, ['admin']],
            // ["archivePayGrade", "archive", true, ["admin"] ],
            // ["restorePayGrade", "restore", true, ["admin"] ],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    function listPayGradesByStaffId($data)
    {
        $times = (new \NDISmate\Models\Payroll\PayGrade\ListPayGradesByStaffId)($data);
        return ['http_code' => 200, 'result' => $times];
    }
}
