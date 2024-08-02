<?php
namespace NDISmate\Models\Payroll;

use \RedBeanPHP\R as R;

class GetEmployeeV2
{
    function __invoke($xero, $data)
    {
        $employee_id = $data['xero_employee_id'];

        try {
            $apiResponse = $xero->payrollAuApi->getEmployee($xero->payroll_tenant_id, $employee_id);

            $employee = $apiResponse[0];

            return $employee;
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ['http_code' => 400, 'error' => $error];
        }
    }
}
