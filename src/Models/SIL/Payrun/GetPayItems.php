<?php
namespace NDISmate\Models\SIL\Payrun;


class GetPayItems {

    function __invoke($xero){
        
        // Step 1: Get PayItems from the cache
        $jsonData = (new \NDISmate\Cache\Retrieve)('xeroPayItems');

        // TODO: do a check if the file exists, otherwise use FetchPayItems instead.

        // Step 2: Parse the JSON data into a PHP array
        $data = json_decode($jsonData, true);

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
        return ["http_code"=>200,"result"=>["EarningsRates"=>$filteredData]];    

    }
}