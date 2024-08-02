<?php
namespace NDISmate\Models\Staff\Downtime;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use \RedBeanPHP\R as R;
// use NDISmate\CORE\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 


class Exception extends BaseModel {


    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("staffdowntimeexception", [
            'id' => [v::numericVal()],
            'staff_id' => [v::numericVal()],
            'downtime_id' => [v::numericVal()],
            'available_date' => [v::stringType()], // Date when the recurring downtime is made available as an exception.
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addDowntimeException" , "create", true, [] ],
            ["getDowntimeException", "getOne", true, [] ],
            ["listDowntimeExceptions", "getAll", true, [] ],
            ["updateDowntimeException", "update", true, [] ],
            ["deleteDowntimeException", "delete", true, [] ],
        ];

        return $this->invoke($request, $response, $args, $this);
        
    }


}