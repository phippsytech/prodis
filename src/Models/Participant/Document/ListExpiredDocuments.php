<?php

namespace NDISmate\Models\Staff\Credential;

use \RedBeanPHP\R as R;


class ListExpiredCredentials
{

    function __invoke($data)
    {

        $beans = R::getAll(

            'SELECT 
                staffcredentials.id as id,
                staffs.id as staff_id,
                staffs.name as name, 
                staffcredentials.credential_date,
                credentials.name as credential, 
                credentials.date_collection_option,
                credentials.years_until_expiry
            FROM staffcredentials
            JOIN credentials ON (staffcredentials.credential_id = credentials.id)
            JOIN staffs ON (staffs.id = staffcredentials.staff_id)
            ORDER BY staffs.name
            '
        );

        $expired = [];

        foreach ($beans as $key => $bean) {

            // if there isn't a date collection option, then we don't need to worry about expiry
            if (!in_array($bean['date_collection_option'], ["issued", "expires"])) continue;

            if ($bean['date_collection_option'] == "issued") {
                if (!$bean['years_until_expiry']) continue;
                $expiry_date = date('Y-m-d', strtotime($bean['credential_date'] . ' + ' . $bean['years_until_expiry'] . ' years'));
            }

            if ($bean['date_collection_option'] == "expires") {
                $expiry_date = $bean['credential_date'];
            }

            // if the expiry date is in the past, then we need to flag it as expired
            if (strtotime($expiry_date) < strtotime(date('Y-m-d'))) {

                $expired[] = [
                    "staff_id" => $bean['staff_id'],
                    "credential_id" => $bean['id'],
                    "name" => $bean['name'],
                    "credential" => $bean['credential'],
                    "expiry_date" => $expiry_date
                ];
            }
        }

        return $expired;
    }
}
