<?php
namespace NDISmate\Models\Payroll\PayGrade;

use \RedBeanPHP\R as R;

class ListPayGradesByStaffId
{
    function __invoke($data)
    {
        $sql = 'select xeropaygrades.* from xeropaygrades
        join staffs on (staffs.paygrade_id = xeropaygrades.id)
        where staffs.id = :staff_id';

        $params = [':staff_id' => $data['staff_id']];

        $result = R::getRow($sql, $params);

        unset($result['id']);
        unset($result['name']);

        foreach ($result as $key => $value) {
            $earnings_rate_ids[] = $value;
        }

        // Step 1: Get PayItems from the cache
        $jsonData = (new \NDISmate\Cache\Retrieve)('xeroPayItems');

        // TODO: do a check if the file exists, otherwise use FetchPayItems instead.

        // Step 2: Parse the JSON data into a PHP array
        $json = json_decode($jsonData, true);

        // return $json['EarningsRates'];

        // Step 3: Loop through the array to filter and collect the required data
        $filteredData = [];

        foreach ($json['EarningsRates'] as $earningRate) {
            // Step 4: Collect the required data (Name and EarningsRateID) in the $filteredData array
            if (in_array($earningRate['EarningsRateID'], $earnings_rate_ids)) {
                $filteredData[] = [
                    'Name' => $earningRate['Name'],
                    'EarningsRateID' => $earningRate['EarningsRateID'],
                    // 'RatePerUnit' => $earningRate['RatePerUnit'],
                ];
            }
        }

        // Step 5: Return the filtered data

        return $filteredData;
    }
}
