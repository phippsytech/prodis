<?php

namespace NDISmate\Models\Staff\Credential;

use \RedBeanPHP\R as R;

class ListCredentials
{

    function __invoke($data)
    {

        $beans = R::getAll(

            'SELECT 
                staffcredentials.id as id,
                staffs.id as staff_id,
                staffs.name as name, 
                staffs.groups as staff_groups,
                staffcredentials.credential_date,
                credentials.name as credential, 
                credentials.collect_from_therapist,
                credentials.collect_from_sil,
                credentials.date_collection_option,
                credentials.years_until_expiry
            FROM staffcredentials
            JOIN credentials ON (staffcredentials.credential_id = credentials.id)
            JOIN staffs ON (staffs.id = staffcredentials.staff_id)
            '
        );

        foreach ($beans as $key => $bean) {
            $beans[$key]['staff_groups'] = json_decode($bean['staff_groups']);
        }

        return $beans;
    }
}
