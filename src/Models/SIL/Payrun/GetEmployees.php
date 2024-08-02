<?php
namespace NDISmate\Models\SIL\Payrun;

use \RedBeanPHP\R as R;


class GetEmployees {

    function __invoke($xero){

        $where = 'Status=="ACTIVE"';

        try {
            $apiResponse = $xero->payrollAuApi->getEmployees($xero->payroll_tenant_id, null, $where);
            return ["http_code"=>200,"result"=>$apiResponse];    
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }
        
    }

}
