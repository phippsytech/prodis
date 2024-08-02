<?php
namespace NDISmate\Models\Staff\Credential;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\CRUD;
use Respect\Validation\Validator as v; 
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;


class ListDueCredentials{

    function __invoke($data){

        $beans = R::getAll( 
            //google_file_ref,
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
            ' );
        
        // if (!$bean->id) return ["http_code"=>404, "error_message"=>"Not found."];

        $due=[];

        foreach ($beans as $key => $bean) {
            
            // if there isn't a date collection option, then we don't need to worry about expiry
            if(!in_array($bean['date_collection_option'],["issued","expires"])) continue;

            if ($bean['date_collection_option']=="issued"){
                if(!$bean['years_until_expiry']) continue;
                $expiry_date = date('Y-m-d', strtotime($bean['credential_date']. ' + '.$bean['years_until_expiry'].' years'));
            }

            if ($bean['date_collection_option']=="expires"){
                $expiry_date = $bean['credential_date'];
            }

            // if the expiry date is in the past, then we need to flag it as expired
            if (
                !(strtotime($expiry_date) < strtotime(date('Y-m-d')))
                && (strtotime($expiry_date) < strtotime(date('Y-m-d').' + 2 month'))
                ){
                
                $due[]=[
                    "staff_id"=>$bean['staff_id'],
                    "credential_id"=>$bean['id'],
                    "name"=>$bean['name'],
                    "credential"=>$bean['credential'],
                    "expiry_date"=>$expiry_date
                ];
                
            }

        }
        
        return ["http_code"=>200, "result"=>$due];
        // return ["http_code"=>200, "result"=>$bean->export()];
        
    }

}

