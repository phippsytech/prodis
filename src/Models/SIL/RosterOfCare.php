<?php
namespace NDISmate\Models\SIL;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v; 
use \RedBeanPHP\R as R;


class RosterOfCare extends BaseModel {

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {

        $this->CRUD = new CRUD("rosterofcare", [
            'id' => [v::numericVal()],
            'participant_name' => [v::numericVal()],

            'start_date' => [v::stringType()],
            'start_time' => [v::stringType()],
            'end_date' => [v::stringType()],
            'end_time' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addRosterOfCare" , "create", false, [ "admin" ] ],
            ["getRosterOfCare", "getOne", true, [ "admin" ] ],
            ["listRosterOfCares", "getAll", true, [ "admin" ] ],
            ["updateRosterOfCare", "update", true, [ "admin" ] ],
            ["deleteRosterOfCare", "delete", true, ["admin" ] ],
        ];

        return $this->invoke($request, $response, $args, $this);
    }


}