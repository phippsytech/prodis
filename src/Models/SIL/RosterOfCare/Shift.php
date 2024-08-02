<?php
namespace NDISmate\Models\SIL\RosterOfCare;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 
use \RedBeanPHP\R as R;


class Shift extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("rosterofcareshift", [
            'id' => [v::numericVal()],
            'rosterofcare_id' => [v::numericVal()],
            'support_item_number' => [v::stringType()],
            'start_date' => [v::stringType()],
            'start_time' => [v::stringType()],
            'end_date' => [v::stringType()],
            'end_time' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addShift" , "create", false, [ "admin" ] ],
            ["getShift", "getOne", true, [ "admin" ] ],
            ["getShifts", "getShifts", true, [ "admin" ] ],
            ["updateShift", "update", true, [ "admin" ] ],
            ["deleteShift", "delete", true, ["admin" ] ],
        ];

        return $this->invoke($request, $response, $args, $this);
    }

    function getShifts($data){
        return (new \NDISmate\Models\SIL\RosterOfCare\Shift\GetShifts)($data);
    }


}