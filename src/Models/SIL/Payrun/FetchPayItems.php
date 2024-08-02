<?php
namespace NDISmate\Models\SIL\Payrun;


class FetchPayItems {

    function __invoke($xero){

        try {
            $apiResponse = $xero->payrollAuApi->getPayItems($xero->payroll_tenant_id);

            $jsonData = json_encode($apiResponse);

            // Write JSON data to Cache
            $cache = (new \NDISmate\Cache\Store)('xeroPayItems', $jsonData);
            
            return ["http_code"=>200,"result"=>$apiResponse];    

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }
        
    }

}
