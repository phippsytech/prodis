<?php
namespace NDISmate\Models\Staff\Credential;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\CRUD;
use Respect\Validation\Validator as v; 
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;


class ListMissingCredentials{

    function __invoke($data){

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

        return ["http_code"=>200, "result"=>$beans];


// Hopefully that works better! Below is the older code, which I've left in for reference.



        $credentials = R::getAll(
            'SELECT 
                *
            FROM credentials
                '
        );

        

        $staff = R::getAll(
            'SELECT 
                id,
                name,
                groups
            FROM staffs
            WHERE archived is not true
                '
        );

        // return ["http_code"=>200, "result"=>$staff];

        $missing=[];

        foreach($staff as $staffer){
            if (!$staffer['groups']) continue;
            
            $staff_groups = json_decode($staffer['groups'], true);
            
            // return ["http_code"=>200, "result"=>$staff_groups];
            foreach($credentials as $credential){
                
                // print_r($staff_groups);

                // var_dump(
                //     $credential['collect_from_sil']=="required" 
                //     // && in_array("sil",$staff_groups)
                // );


                // var_dump(($credential['collect_from_therapist']=="required" && in_array("therapist",$staff_groups)) 
                // || ($credential['collect_from_sil']=="required" && in_array("sil",$staff_groups)));
                
                if(!(
                    ($credential['collect_from_therapist']=="required" && in_array("therapist",$staff_groups)) 
                    || ($credential['collect_from_sil']=="required" && in_array("sil",$staff_groups))
                 )) continue;

                
                //  print_r($credential);
                
                // return;


                $staffcredential = R::findOne(
                    'staffcredentials',
                    ' staff_id = :staff_id AND credential_id = :credential_id ',
                    [
                        ':staff_id'=>$staffer['id'],
                        ':credential_id'=>$credential['id']
                    ]
                );
                if (!$staffcredential){
                    $missing[]=[
                        "staff_id"=>$staffer['id'],
                        "credential_id"=>$credential['id'],
                        "name"=>$staffer['name'],
                        "credential"=>$credential['name']
                    ];    
                }
            }
        }

        

        return ["http_code"=>200, "result"=>$missing];
        // return ["http_code"=>200, "result"=>$bean->export()];
        
    }

}

