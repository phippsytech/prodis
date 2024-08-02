<?php
namespace NDISmate\Models\SIL\Payrun;

use \RedBeanPHP\R as R;


class GetEmployee {

    function __invoke($xero){

        $employee_id = '96b3142a-6ea2-408a-89b4-f1cbc1c781d7';

        try {
            $apiResponse = $xero->payrollAuApi->getEmployee($xero->payroll_tenant_id, $employee_id);
            $employee = $apiResponse[0]->getSuperMemberships()[0]->getSuperMembershipID();
            return ["http_code"=>200,"result"=>$employee];    
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }
        
    }

}


