<?php
namespace NDISmate\Xero\Payroll;

use \RedBeanPHP\R as R;


class GetSuperannuationFunds {

    function __invoke($xero){

        try {
            $apiResponse = $xero->payrollAuApi->getSuperfunds($xero->payroll_tenant_id);


            $superannuation_funds = $xero->payrollAuApi->getSuperfunds($xero->payroll_tenant_id);
            



            foreach($superannuation_funds as $superannuation_fund){


                // $superannuation_fund = $superannuation_fund->getSuperfunds();
                // print($superannuation_fund->getSuperFundID());
                // return ["http_code"=>200,"result"=>(Array)$superannuation_fund];    

                // print_r($superannuation_fund);
                // $superFundID = $superannuation_fund->getSuperFundID();
                // $result = $xero->payrollAuApi->getSuperfund($xero->payroll_tenant_id, $superannuation_fund->getSuperFundID());
                // print_r((Array)$superannuation_fund);
                // print_r((Array)$result->getSuperfunds());
                // return;
                // $apiResponse['superannuation_funds'][]=[
                //     'summary' => (Array) $superannuation_fund,
                //     'detail' => (Array) $result,
                // ]; 

                $apiResponse['superannuation_funds'][]=(Array) $superannuation_fund;

                // $apiResponse['superannuation_funds'][] = (Array) $result;

                // if($superannuation_fund->getCurrentRecord() == true){
                //     $apiResponse['superannuation_funds'][] = (Array) $superannuation_fund;
                // } 
            }






            return ["http_code"=>200,"result"=>$apiResponse];    
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }

    }
}