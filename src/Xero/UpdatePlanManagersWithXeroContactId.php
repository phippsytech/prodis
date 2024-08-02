<?php

namespace NDISmate\Xero;

use \RedBeanPHP\R as R;
use \NDISmate\CORE\TransactionalDatabase;

class updatePlanManagersWithXeroContactId {

    
    function __invoke($obj){

        $beans = R::getAll( 
            "SELECT 
                id,
                email, 
                xero_contact_ref
            FROM planmanagers 
            WHERE xero_contact_ref is null");

        if (empty($beans)) return ["http_code"=>404, "error_message"=>"Not found."];
        
        foreach($beans as $bean){

            if (empty($bean['email'])) continue; // don't get xero contact id if no email address
            
            sleep(1); // to avoid rate limit

            unset($apiResponse, $contacts, $xero_contact_ref);
            try {

                $where = "EmailAddress==\"".$bean['email']."\"";
                $apiResponse = $obj->accountingApi->getContacts($obj->tenant_id, null, $where, null, null, null, null, true, null);
                $contacts = $apiResponse->getContacts();
                
                // if (empty($contacts)) continue;
                if (empty($contacts)){
                    // print $bean['id'].":".$bean['email']."\n";
                    continue;
                };
                $xero_contact_ref = $contacts[0]->getContactId();

                // print $xero_contact_ref;

                $planmanager = R::load( 'planmanagers', $bean['id'] );
                $planmanager->xero_contact_ref = $xero_contact_ref;
                $thang = TransactionalDatabase::store($planmanager);
                
            } catch (\XeroAPI\XeroPHP\ApiException $e) {
                // print_r($e);
                $error = json_decode($e->getResponseBody(), true);
                return ["http_code"=>400, "error"=>$error];
            }
            
        }

        return ["http_code"=>200];

    }
    
}