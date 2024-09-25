<?php

namespace NDISmate\Models\SIL;

use \NDISmate\CORE\BaseModel;
use \NDISmate\CORE\CRUD;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;
use \RedBeanPHP\R as R;


class House extends BaseModel
{

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $this->CRUD = new CRUD("house", [
            'id' => [v::numericVal()],
            'client_id' => [v::numericVal()],
            'name' => [v::stringType()],
            'email' => [v::stringType()],
        ]);

        // action, class method, guard, roles
        $this->actions = [
            ["addHouse", "create", true, []],
            ["getHouse", "getOne", true, []],
            ["listHouses", "getAll", true, ["house"]],
            ["getHouseByUserId", "getHouseByUserId", true, ["house"]],
            ["listHousesByStaffId", "listHousesByStaffId", true, ["house"]],
            ["updateHouse", "update", true, []],
            // ["archiveHouse", "archive", true, [] ],
            // ["restoreHouse", "restore", true, [] ],


        ];

        return $this->invoke($request, $response, $args, $this);
    }


    // Additional Methods and overrides here:

    function getHouseByUserId($data)
    {

        $user = R::load("users",);

        $house = R::findOne("houses", " email=:email ", [":email" => $user['email']]);
        if (!$house) {
            return ["http_code" => 404, "error_message" => "House not found"];
        }
        return (["http_code" => 200, "result" => $house]);
    }

    function listHousesByStaffId($data)
    {

        $houses = R::getAll("SELECT houses.id, houses.name, houses.email, houses.client_id from houses join housestaffs on (houses.id=housestaffs.house_id) where housestaffs.staff_id=:staff_id", [":staff_id" => $data['staff_id']]);
        if (!$houses) {
            return ["http_code" => 404, "error_message" => "Houses not found"];
        }
        return (["http_code" => 200, "result" => $houses]);
    }
}
