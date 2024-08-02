<?php

namespace NDISmate\Utilities;

use NDISmate\CORE\JsonResponse;
use RedBeanPHP\R;

class GetUserType
{
    function __invoke($data, $fields, $guard)
    {
        $user_id = $guard->user_id;

        $user = R::load('users', $user_id);

        $client = $this->getSILClientByUserId($user);
        if ($client) {
            return JsonResponse::response(['http_code' => 200, 'result' => [
                'type' => 'client',
                'client_id' => $client['id'],
                'client_name' => $client['name']
            ]]);
        } else {
            // $staff = $this->getStaffByUserId($user);
            // if($staff){
            return JsonResponse::response(['http_code' => 200, 'result' => [
                'type' => 'staff',
                // "staff"=>$staff
            ]]);
            // }
        }

        return JsonResponse::response(['http_code' => 404, 'error_message' => 'SIL client not found', 'data' => $data]);
    }

    function getSILClientByUserId($user)
    {
        $client = R::findOne('clients', ' sil_email=:email ', [':email' => $user['email']]);

        if (!$client) {
            return false;
        }
        return $client;
    }

    function getStaffByUserId($user)
    {
        $staff = R::getRow('SELECT staffs.id, staffs.email, staffs.name, staffs.phone_number from staffs where staffs.email=:email', [':email' => $user['email']]);

        if (!$staff) {
            return false;
        }
        return $staff;
    }
}
