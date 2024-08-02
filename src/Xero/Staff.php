<?php
namespace NDISmate\Xero;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 
use \RedBeanPHP\R as R;


class Staff extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("xerostaff", [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()], 
            'employee_ref' => [v::stringType()],
            'xero_super_membership_ref' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addStaffer", "create", true, ["admin"]],
            ["listStaff", "getAll", true, []],
            ["getStaffer", "getOne", true, []],
            ["updateStaffer", "update", true, []],
            ["deleteStaffer", "delete", true, ["admin"]],
        ];

        return $this->invoke($request, $response, $args, $this);

    }


}