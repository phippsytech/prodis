<?php
namespace NDISmate\Models\Staff\Credential;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\CRUD;
use Respect\Validation\Validator as v; 
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;


class ListCredentials{

    function __invoke($data){

        $beans = R::getAll( 
            //google_file_ref,
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
            ' );
        
        // if (!$bean->id) return ["http_code"=>404, "error_message"=>"Not found."];


        foreach ($beans as $key => $bean) {
            $beans[$key]['staff_groups'] = json_decode($bean['staff_groups']);

            


        }
        
        return ["http_code"=>200, "result"=>$beans];
        // return ["http_code"=>200, "result"=>$bean->export()];
        
    }

}

