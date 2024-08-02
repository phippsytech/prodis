<?php
namespace NDISmate\Xero\Payroll;

use \RedBeanPHP\R as R;


class GetSettings {

    function __invoke($xero){

        try {
            $apiResponse = $xero->payrollAuApi->getSettings($xero->payroll_tenant_id);
            $result = $apiResponse->getSettings()['accounts'];
            print_r($result);
            return ["http_code"=>200,"result"=>$result];    
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }

    }
}