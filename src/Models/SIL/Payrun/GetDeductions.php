<?php
namespace NDISmate\Models\SIL\Payrun;


class GetDeductions {

    function __invoke($xero){
        
        // Step 1: Get PayItems from the cache
        $jsonData = (new \NDISmate\Cache\Retrieve)('xeroPayItems');

        // return ["http_code"=>200,"result"=>json_decode($jsonData,true)];    

        // TODO: do a check if the file exists, otherwise use FetchPayItems instead.

        // Step 2: Parse the JSON data into a PHP array
        $data = json_decode($jsonData, true);

        // Step 3: Loop through the array to filter and collect the required data
        $filteredData = [];
        foreach ($data['DeductionTypes'] as $deductionType) {
            if ($deductionType['CurrentRecord'] === true) {
                // Step 4: Collect the required data (Name and EarningsRateID) in the $filteredData array
                $filteredData[] = [
                    'Name' => $deductionType['Name'],
                    'DeductionTypeID' => $deductionType['DeductionTypeID'],
                ];
            }
        }

        // Step 5: Return the filtered data
        return ["http_code"=>200,"result"=>["DeductionTypes"=>$filteredData]];    

    }
}