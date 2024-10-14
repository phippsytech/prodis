<?php

namespace NDISmate\Models\Staff\Credential;

use \RedBeanPHP\R as R;


class ListCredentialsByStaffId
{

    function __invoke($data)
    {

        // collect_from_therapist,
        // collect_from_sil,

        // required, optional, do_not_collect

        $staffer = R::load("staffs", $data['staff_id']);
        $staff_groups = json_decode($staffer->groups, true);

        if ($staff_groups == null  || $staff_groups == []) {
            return ["http_code" => 200, "result" => []];
        }

        $therapist_query = <<<HEREDOC
select 
        credentials.id,
        credentials.name,
        credentials.description,
        credentials.date_collection_option,
        credentials.years_until_expiry,
        IF(
            credentials.collect_from_therapist = 'required',
            TRUE,
            FALSE
        ) AS required
        from credentials
        left join staffcredentials on (credentials.id = staffcredentials.credential_id AND staffcredentials.staff_id = :staff_id)
    where 
    (credentials.collect_from_therapist in ('required', 'optional') and :set_member_therapist = 1)
    
    ORDER BY required desc, credentials.name
HEREDOC;

        $sil_query = <<<HEREDOC
select 
        credentials.id,
        credentials.name,
        credentials.description,
        credentials.date_collection_option,
        credentials.years_until_expiry,
        IF(
            credentials.collect_from_sil = 'required',
            TRUE,
            FALSE
        ) AS required
        from credentials
        left join staffcredentials on (credentials.id = staffcredentials.credential_id AND staffcredentials.staff_id = :staff_id)
    where 
    (credentials.collect_from_sil in ('required', 'optional') and :set_member_sil = 1)
    
    ORDER BY required desc, credentials.name
HEREDOC;

        $both_query = <<<HEREDOC
select 
    credentials.id,
    credentials.name,
    credentials.description,
    credentials.date_collection_option,
    credentials.years_until_expiry,
    IF(
        credentials.collect_from_sil = 'required' OR credentials.collect_from_therapist = 'required',
        TRUE,
        FALSE
    ) AS required
from credentials
left join staffcredentials on credentials.id = staffcredentials.credential_id AND staffcredentials.staff_id = :staff_id
where 
    (credentials.collect_from_sil in ('required', 'optional') and :set_member_sil = 1)
    OR (credentials.collect_from_therapist in ('required', 'optional') and :set_member_therapist = 1)
    
ORDER BY required desc, credentials.name
HEREDOC;


        $params = [
            ":staff_id" => $data['staff_id'],

        ];

        switch (true) {
            case (in_array("sil", $staff_groups) && !in_array("therapist", $staff_groups)):
                $params[":set_member_sil"] = in_array("sil", $staff_groups) ? 1 : 0;
                $query = $sil_query;
                break;
            case (!in_array("sil", $staff_groups) && in_array("therapist", $staff_groups)):
                $params[":set_member_therapist"] = in_array("therapist", $staff_groups) ? 1 : 0;
                $query = $therapist_query;
                break;
            case (in_array("sil", $staff_groups) && in_array("therapist", $staff_groups)):
                $params[":set_member_sil"] = in_array("sil", $staff_groups) ? 1 : 0;
                $params[":set_member_therapist"] = in_array("therapist", $staff_groups) ? 1 : 0;
                $query = $both_query;
                break;
        }



        $bean = R::getAll($query, $params);

        foreach ($bean as &$row) {
            $row['required'] = ($row['required'] == 1) ? true : false;
        }

        return $bean;
    }
}
