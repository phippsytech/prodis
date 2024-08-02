<?php
namespace NDISmate\Xero\Staff;

use \RedBeanPHP\R as R;


class GetEmployees {

    function __invoke($xero, $data){

        $ifModifiedSince = new DateTime("2020-02-06T12:17:43.202-08:00");
        $where = 'Status=="ACTIVE"';
        $order = "EmailAddress%20DESC";
        $page = 56;

        try {            
            $result = $xero->payrollAuApi->getEmployees($xero->payroll_tenant_id, $ifModifiedSince, $where, $order, $page);
            return ["http_code"=>200,"result"=>$apiResponse];    
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }

    }

    
}