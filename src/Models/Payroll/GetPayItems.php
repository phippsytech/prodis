<?php
namespace NDISmate\Models\SIL\Payrun;

class GetPayItems
{
    function __invoke($xero)
    {
        // Step 1: Get PayItems from the cache
        $expiryDateTime = new \DateTime();
        $expiryDateTime->setTime(0, 0, 0);

        $jsonData = (new \NDISmate\Cache\Retrieve)('xeroPayItems', $expiryDateTime);

        // TODO: do a check if the file exists, otherwise use FetchPayItems instead.
        if (empty($jsonData)) {  // Cache doesn't exist, fetch data
            try {
                $apiResponse = $xero->payrollAuApi->getPayItems($xero->payroll_tenant_id);

                $jsonData = json_encode($apiResponse);

                // Write JSON data to Cache
                $cache = (new \NDISmate\Cache\Store)('xeroPayItems', $jsonData);

                return ['http_code' => 200, 'result' => $apiResponse];
            } catch (\XeroAPI\XeroPHP\ApiException $e) {
                $error = json_decode($e->getResponseBody(), true);
                return ['http_code' => 400, 'error' => $error];
            }
        } else {
            $data = json_decode($jsonData, true);
        }

        // Step 2: Parse the JSON data into a PHP array

        // Step 3: Loop through the array to filter and collect the required data
        $filteredData = [];
        foreach ($data['EarningsRates'] as $earningRate) {
            if ($earningRate['CurrentRecord'] === true) {
                // Step 4: Collect the required data (Name and EarningsRateID) in the $filteredData array
                $filteredData[] = [
                    'Name' => $earningRate['Name'],
                    'EarningsRateID' => $earningRate['EarningsRateID'],
                    'RatePerUnit' => $earningRate['RatePerUnit'],
                ];
            }
        }

        // Step 5: Return the filtered data
        return ['http_code' => 200, 'result' => ['EarningsRates' => $filteredData]];
    }
}
