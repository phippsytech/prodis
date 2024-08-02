<?php
namespace NDISmate\Xero\Staff;

use \RedBeanPHP\R as R;


class GetEmployee {

    function __invoke($xero, $data){

        $employee_id = $data['employee_id'];//"a66e32ec-3380-4b2b-b4f7-ac726cd7eb99"; // this is hannah


        try {
            $result = $xero->payrollAuApi->getEmployee($xero->payroll_tenant_id, $employee_id);
            
            // this is a hack to convert the object to an array.
            $employee =  json_decode(json_encode($result),true)[0];
            
           $employee['DateOfBirth'] = $this->convertXeroDate($employee['DateOfBirth']);
        //    $employee['StartDate'] = $this->convertXeroDate($employee['StartDate']);

            return ["http_code"=>200,"result"=>$employee];    
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }

    }

    
    function convertXeroDate($xeroDateStr){
        $date = str_replace('/Date(', '', $xeroDateStr);
        $parts = explode('+', $date);
        return date("Y-m-d", $parts[0] / 1000);


        // Extract the timestamp from the Xero date string
        $timestamp = intval(substr($xeroDateStr, 6, 13));

        // Create the DateTime object using the timestamp (in seconds)
        $xeroDateObj = (new \DateTime())->setTimestamp($timestamp / 1000);

        // Now you can format the date as needed
        $formattedDate = $xeroDateObj->format("Y-m-d");


    }

    
}