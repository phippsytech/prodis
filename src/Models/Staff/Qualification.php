<?php

namespace NDISmate\Models\Staff;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;
// use NDISmate\CORE\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;


class Qualification extends BaseModel
{

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $this->CRUD = new CRUD("staffqualification", [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'qualification_id' => [v::numericVal()],
            'training_provider' => [v::stringType()], // eg Cert 4 in Disability support
            'qualification_date' => [v::stringType()]
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addQualification", "create", true, []],
            ["getQualification", "getOne", true, []],
            ["listQualifications", "getAll", true, []],
            ["listQualificationsByStaffId", "listQualificationsByStaffId", true, []],
            ["updateQualification", "update", true, []],


        ];

        return $this->invoke($request, $response, $args, $this);
    }


    // Additional Methods and overrides here:

    function listQualificationsByStaffId($data)
    {
        return (new \NDISmate\Models\Staff\Credential\ListQualificationsByStaffId)($data);
    }
}
