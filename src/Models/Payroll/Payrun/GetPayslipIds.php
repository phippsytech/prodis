<?php
namespace NDISmate\Models\Payroll\Payrun;

// use \RedBeanPHP\R as R;

class GetPayslipIds
{
    function __invoke($xero, $force = false)
    {
        // if force is false, check cache first
        if ($force == false) {
            $expiryDateTime = new \DateTime();
            $expiryDateTime->setTime(0, 0, 0);
            $jsonData = (new \NDISmate\Cache\Retrieve)('xeroPayslipIds', $expiryDateTime);
        }

        if (empty($jsonData)) {  // Cache doesn't exist, fetch data
            // print "cache didn't exist";
            $where = 'PayRunStatus=="DRAFT"';

            try {
                $apiResponse = $xero->payrollAuApi->getPayruns($xero->payroll_tenant_id, null, $where);
                sleep(1);  // to avoid rate limit

                $payrun_id = $apiResponse->getPayRuns()[0]->getPayRunId();

                $apiResponse = $xero->payrollAuApi->getPayrun($xero->payroll_tenant_id, $payrun_id);
                sleep(1);  // to avoid rate limit

                $payslip_ids = [];

                foreach ($apiResponse->getPayRuns()[0]->getPayslips() as $payslip) {
                    $payslip_ids[$payslip->getEmployeeId()] = $payslip->getPayslipId();
                }

                $jsonData = json_encode($payslip_ids);

                $cache = (new \NDISmate\Cache\Store)('xeroPayslipIds', $jsonData);
            } catch (\Exception $e) {
                print_r($e);
                $error = $e->getMessage();
                $error = json_decode($e->getResponseBody(), true);
                return ['http_code' => 400, 'error' => $error];
            }
        } else {
            // print "using cache";
            $payslip_ids = json_decode($jsonData, true);
        }

        return $payslip_ids;
    }
}
