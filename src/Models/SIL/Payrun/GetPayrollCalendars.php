<?php
namespace NDISmate\Models\SIL\Payrun;

use \RedBeanPHP\R as R;


class GetPayrollCalendars {

    function __invoke($xero){

        try {
            $apiResponse = $xero->payrollAuApi->getPayrollCalendars($xero->payroll_tenant_id);

            return ["http_code"=>200,"result"=>$apiResponse];    

        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }
        
    }

}
