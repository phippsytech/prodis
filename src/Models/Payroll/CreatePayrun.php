<?php
namespace NDISmate\Models\Payroll;

use \RedBeanPHP\R as R;

class CreatePayrun
{
    function __invoke($xero)
    {
        $payRun = new \XeroAPI\XeroPHP\Models\PayrollAu\PayRun;
        $payRun->setPayrollCalendarID('abc7db8d-6331-4fcf-95eb-10e780c4dec3');  // Hardcoded for now
        $payRuns[] = $payRun;

        try {
            $result = $xero->payrollAuApi->createPayRun($xero->payroll_tenant_id, $payRuns);

            // Generate the payslips here because it makes the next step faster
            (new \NDISmate\Models\Payroll\Payrun\GetPayslipIds)(xero: $xero, force: true);

            return ['http_code' => 200, 'result' => $result];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ['http_code' => 400, 'error' => $error];
        }
    }
}
