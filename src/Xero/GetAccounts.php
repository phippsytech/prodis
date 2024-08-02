<?php

namespace NDISmate\Xero;


use \RedBeanPHP\R as R;


class GetAccounts {

    function __invoke($obj){
        
        // $xeroTenantId = "YOUR_XERO_TENANT_ID";
        // $ifModifiedSince = new DateTime("2020-02-06T12:17:43.202-08:00");
        // $where = "Status=="' . \XeroAPI\XeroPHP\Models\Accounting\Account::STATUS_ACTIVE . '"";
        // $order = "Name ASC";
        
        try {
          $result = $obj->accountingApi->getAccounts($obj->tenant_id);
          return ["http_code"=>200,"result"=>$result];    

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }

    }

}