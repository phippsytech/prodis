<?php
namespace NDISmate\Models\Payroll\Payrun;

use \RedBeanPHP\R as R;

class GetDraftPayruns
{
    function __invoke($xero)
    {
        $where = 'PayRunStatus=="DRAFT"';

        try {
            $apiResponse = $xero->payrollAuApi->getPayruns($xero->payroll_tenant_id, null, $where);

            $payrun_id = $apiResponse->getPayRuns()[0]->getPayRunId();

            $apiResponse = $xero->payrollAuApi->getPayrun($xero->payroll_tenant_id, $payrun_id);

            $payslip_ids = [];

            foreach ($apiResponse->getPayRuns()[0]->getPayslips() as $payslip) {
                $payslip_ids[$payslip->getEmployeeId()] = $payslip->getPayslipId();
            }

            return ['http_code' => 200, 'result' => $payslip_ids];
        } catch (\Exception $e) {
            print_r($e);
            $error = $e->getMessage();
            $error = json_decode($e->getResponseBody(), true);
            return ['http_code' => 400, 'error' => $error];
        }
    }
}
