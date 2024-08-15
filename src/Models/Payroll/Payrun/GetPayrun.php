<?php
namespace NDISmate\Models\Payroll\Payrun;

// use \RedBeanPHP\R as R;

class GetPayrun
{
    function __invoke($xero, $data, $force = true)
    {
        if (!isset($data['payrun_id']))
            $errors[] = 'payrun_id is required';

        $payrun_id = $data['payrun_id'];

        if (isset($errors))
            return ['http_code' => 400, 'error' => $errors];

        $cache_name = 'xeroPayrun' . $payrun_id;

        if ($force == false) {
            $jsonData = (new \NDISmate\Cache\Retrieve)($cache_name);
        }

        if (empty($jsonData)) {  // Cache doesn't exist, fetch data
            try {
                $apiResponse = $xero->payrollAuApi->getPayrun($xero->payroll_tenant_id, $data['payrun_id']);
                $payrun = $apiResponse[0];
                $payrun = \XeroAPI\XeroPHP\PayrollAuObjectSerializer::sanitizeForSerialization($payrun);
                $jsonData = json_encode($payrun);
                $expiryInSeconds = 86400 ;
                $cache = (new \NDISmate\Cache\Store)($cache_name, $jsonData, $expiryInSeconds);
            } catch (\XeroAPI\XeroPHP\ApiException $e) {
                $error = json_decode($e->getResponseBody(), true);
                return ['http_code' => 400, 'error' => $error, 'staff_id' => $staff_id];
            }
        } else {
            // print "using cache";
            $payrun = json_decode($jsonData, true);
        }

        return ['http_code' => 200, 'result' => $payrun];
    }
}
