<?php
namespace NDISmate\Xero\Staff;

use \RedBeanPHP\R as R;


class CreateEmployee {

    function __invoke($xero, $data){

        $dateOfBirth = new DateTime($data['date_of_birth']);

        $homeAddress = new \XeroAPI\XeroPHP\Models\PayrollAu\HomeAddress;
        $homeAddress->setAddressLine1($data['address_line_1']);
        $homeAddress->setAddressLine2($data['address_line_2']);
        $homeAddress->setRegion($data['state']);
        $homeAddress->setPostalCode($data['postcode']);
        $homeAddress->setCity($data['suburb']);

        $employee = new \XeroAPI\XeroPHP\Models\PayrollAu\Employee;
        $employee->setFirstName($data['first_name']);
        $employee->setLastName($data['last_name']);
        $employee->setDateOfBirth($dateOfBirth);
        $employee->setHomeAddress($homeAddress);

        try {
            $result = $xero->payrollAuApi->createEmployee($xero->payroll_tenant_id, $employee);
            return ["http_code"=>200,"result"=>$apiResponse];    
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ["http_code"=>400, "error"=>$error];
        }

    }


}