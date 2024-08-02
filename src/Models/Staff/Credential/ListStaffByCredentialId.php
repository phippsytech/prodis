<?php
namespace NDISmate\Models\Staff\Credential;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\CRUD;
use Respect\Validation\Validator as v; 
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;


class ListStaffByCredentialId{

    function __invoke($data){

        $bean = R::getAll( 
            //google_file_ref,
            'SELECT 
                *
            FROM staffcredentials 
            WHERE credential_id=:credential_id
            ',
            [ 
                ":credential_id"=>$data["credential_id"]
            
            ] );
        
        // if (!$bean->id) return ["http_code"=>404, "error_message"=>"Not found."];
        
        return ["http_code"=>200, "result"=>$bean];
        // return ["http_code"=>200, "result"=>$bean->export()];
        
    }

}