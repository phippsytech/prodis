<?php

namespace NDISmate\Models\Client\Staff;

use RedBeanPHP\R;

class CheckAccess
{
    public function __invoke($data)
    {
        $clientStaffer = R::findOne(
            'clientstaffassignments',  // Table name
            'staff_id = :staff_id AND client_id = :client_id',  // WHERE condition
            [
                ':staff_id' => $data['staff_id'],
                ':client_id' => $data['client_id']
            ]
        );

        if (!$clientStaffer) {
            return ['http_code' => 404, 'result' => false];  // I'm using 404 because 403 currently causes authorisation issues
        }

        return ['http_code' => 200, 'result' => true];
    }
}
