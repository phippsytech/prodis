<?php

namespace NDISmate\Models\Staff\Credential;

use \RedBeanPHP\R as R;


class ListMissingCredentials
{

    function __invoke($data)
    {

        $query = <<<HEREDOC
SELECT 
    s.id as staff_id,
    c.id as credential_id,
    s.name as name,
    c.name as credential
FROM staffs s
CROSS JOIN credentials c
WHERE NOT EXISTS (
    SELECT 1
    FROM staffcredentials sc
    WHERE sc.staff_id = s.id AND sc.credential_id = c.id
)
AND (
    (c.collect_from_therapist = 'required' AND JSON_CONTAINS(s.groups, '["therapist"]'))
    OR (c.collect_from_sil = 'required' AND JSON_CONTAINS(s.groups, '["sil"]'))
)
AND s.archived IS NOT TRUE
HEREDOC;

        $beans = R::getAll($query);

        return $beans;
    }
}
