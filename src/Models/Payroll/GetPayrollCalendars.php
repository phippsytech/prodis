<?php
namespace NDISmate\Models\Payroll;

use \RedBeanPHP\R as R;

class GetPayrollCalendars
{
    function __invoke($xero)
    {
        try {
            $result = $xero->payrollAuApi->getPayrollCalendars($xero->payroll_tenant_id);
            return ['http_code' => 200, 'result' => $result];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ['http_code' => 400, 'error' => $error];
        }
    }
}
