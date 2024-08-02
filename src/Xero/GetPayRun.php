<?php
namespace NDISmate\Xero;

use \RedBeanPHP\R as R;


class GetPayRun {

    function __invoke($xero){

        $payrun_id = "6ac18371-d60f-4234-a9c4-d04cd0a46b40";



/**
 
 "result": [
    {
      "PayrollCalendarID": "ee516e62-f2c2-4bcf-80d2-15585b4e8dca",
      "PayRunID": "6ac18371-d60f-4234-a9c4-d04cd0a46b40",
      "PayRunPeriodStartDate": "/Date(1688947200000+0000)/",
      "PayRunPeriodEndDate": "/Date(1690070400000+0000)/",
      "PayRunStatus": "POSTED",
      "PaymentDate": "/Date(1690243200000+0000)/",
      "UpdatedDateUTC": "/Date(1690252109000+0000)/",
       "Payslips": [
        {
          "EmployeeID": "2ee3d290-82a7-4540-8928-2599890605cd",
          "PayslipID": "6532ba7d-a93c-4c79-b2fb-5a616f47759d",
          "FirstName": "Aaron",
          "LastName": "Hape",
          "Wages": 3268.32,
          "Deductions": 0,
          "Tax": 762,
          "Super": 359.52,
          "Reimbursements": 0,
          "NetPay": 2506.32,
          "UpdatedDateUTC": "/Date(1690252109000+0000)/"
        },




        6532ba7d-a93c-4c79-b2fb-5a616f47759d


           try {

                    $payslipId = $payslip->getPayslipID();                
                    $employeeId = $payslip->getEmployeeId();

                    if (!$payslipId) continue; // skip if there is no payslip id

                    // load the actual payslip so we can get existing earnings lines
                    $payslip_obj = $xero->payrollAuApi->getPayslip($xero->payroll_tenant_id, $payslipId);
                    sleep(1); // to avoid rate limit

                    $earningsLines = $payslip_obj->getPayslip()->getEarningsLines();

 
 */




        try {
            $apiResponse = $xero->payrollAuApi->getPayrun($xero->payroll_tenant_id, $payrun_id);
            sleep(1); // to avoid rate limit

            // $payrun = $apiResponse->getPayRuns()[0];

            foreach($payrun as $payslip){
                
                $payslip_obj = $xero->payrollAuApi->getPayslip($xero->payroll_tenant_id, $payslip->getPayslipID());
                sleep(1); // to avoid rate limit

                $earningsLines = $payslip_obj->getPayslip()->getEarningsLines();
                foreach($earningsLines as $earningsLine){
                    $earningsLine->getEarningsRateID()
                }

            }


            return ["http_code"=>200,"result"=>$apiResponse];    
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }

    }
}