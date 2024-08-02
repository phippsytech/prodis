<?php
namespace NDISmate\Models\Payroll;

class GetReimbursementItems
{
    function __invoke($xero, $data)
    {
        // Step 1: Get PayItems from the cache
        $jsonData = (new \NDISmate\Cache\Retrieve)('xeroPayItems');

        // TODO: do a check if the file exists, otherwise use FetchPayItems instead.

        // Step 2: Parse the JSON data into a PHP array
        $json = json_decode($jsonData, true);

        // Step 3: Loop through the array to filter and collect the required data
        $filteredData = [];
        foreach ($json['EarningsRates'] as $earningRate) {
            // Step 4: Collect the required data (Name and ReimbursementTypeID) in the $filteredData array
            if (isset($data['earnings_rate_id'])) {
                if ($earningRate['CurrentRecord'] === true && $earningRate['ReimbursementTypeID'] == $data['earnings_rate_id']) {
                    $filteredData = [
                        'Name' => $earningRate['Name'],
                        'ReimbursementTypeID' => $earningRate['ReimbursementTypeID'],
                        'RatePerUnit' => $earningRate['RatePerUnit'],
                    ];
                    break;
                }
            } else {
                if ($earningRate['CurrentRecord'] === true) {
                    $filteredData[] = [
                        'Name' => $earningRate['Name'],
                        'ReimbursementTypeID' => $earningRate['ReimbursementTypeID'],
                        'RatePerUnit' => $earningRate['RatePerUnit'],
                    ];
                }
            }
        }

        // Step 5: Return the filtered data
        if (empty($filteredData))
            return null;

        return $filteredData;
    }
}
