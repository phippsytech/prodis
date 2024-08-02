<?php
namespace NDISmate\Models\SIL\Payrun;

use \RedBeanPHP\R as R;

class CreatePayrun
{
    function __invoke($xero, $data)
    {
        $payrollCalendarId = 'abc7db8d-6331-4fcf-95eb-10e780c4dec3';

        try {
            $payRun = new \XeroAPI\XeroPHP\Models\PayrollAu\PayRun();
            $payRun->setPayrollCalendarID($payrollCalendarId);
            $payRun->setPaymentDate(new \DateTime(date('Y-m-d')));

            // Set these dates if you are creating an unscheduled pay run.
            $payRun->setPayRunPeriodStartDate(new \DateTime($data['start_date']));
            $payRun->setPayRunPeriodEndDate(new \DateTime($data['end_date']));

            $payRun->setPayRunStatus('DRAFT');

            $payRunResult = $xero->payrollAuApi->createPayRun($xero->payroll_tenant_id, [$payRun]);
            $payRunId = $payRunResult->getPayRuns()[0]->getPayRunID();

            return ['http_code' => 200, 'result' => $payRunResult];
        } catch (\XeroAPI\XeroPHP\ApiException $e) {
            $error = json_decode($e->getResponseBody(), true);
            return ['http_code' => 400, 'error' => $error];
        }
    }
}
