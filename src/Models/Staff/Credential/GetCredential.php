<?php
namespace NDISmate\Models\Staff\Credential;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\CRUD;
use Respect\Validation\Validator as v; 
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;


class GetCredential{

    function __invoke($data){

        $bean = R::getRow( 
            //google_file_ref,
            'SELECT 
                id,
                details,
                google_drive_file_ref,
                vultr_storage_ref,
                credential_date
            FROM staffcredentials 
            WHERE staff_id=:staff_id
                AND credential_id=:credential_id
            ',
            [ 
                ":staff_id"=>$data['staff_id'],
                ":credential_id"=>$data["credential_id"]
            
            ] );
        
        // if (!$bean->id) return ["http_code"=>404, "error_message"=>"Not found."];
        
        return ["http_code"=>200, "result"=>$bean];
        // return ["http_code"=>200, "result"=>$bean->export()];
        
    }

}