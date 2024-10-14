<?php

namespace NDISmate\Models\Staff\Credential;

use \RedBeanPHP\R as R;


class GetCredential
{

    function __invoke($data)
    {

        $bean = R::getRow(
            'SELECT 
                id,
                details,
                vultr_storage_ref,
                credential_date
            FROM staffcredentials 
            WHERE staff_id=:staff_id
                AND credential_id=:credential_id
            ',
            [
                ":staff_id" => $data['staff_id'],
                ":credential_id" => $data["credential_id"]
            ]
        );

        return $bean;
    }
}
