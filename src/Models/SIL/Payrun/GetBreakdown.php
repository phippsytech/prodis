<?php
namespace NDISmate\Models\SIL\Payrun;

use \RedBeanPHP\R as R;

class GetBreakdown
{
    function __invoke($xero, $data)
    {
        $houses = [
            // 1 => 'CJ',
            2 => 'Rhys',
            3 => 'Logan',
            // 4 => 'Alex',
            5 => 'Jamie',
            6 => 'Mia',
            7 => 'Kayla'
        ];

        $earnings = (new \NDISmate\Models\SIL\Payrun\GetEarnings)($data);

        // return ["http_code"=>200, "result"=>$earnings];

        $earnings = $this->reformatEarningsData($earnings, $data['start_date']);

        // return ["http_code"=>200, "result"=>$earnings];

        // $pay_items = json_decode(file_get_contents(BASE_PATH.'/src/Models/SIL/Payrun/earning_items.json'), true);

        foreach ($earnings as $item) {
            // if xero_employee_ref is empty or null skip this staff member
            if ($item['earnings_rate'] == 0)
                continue;

            // This house lead check is flawed
            if ($item['house_lead'] == 1 and $item['shift_type'] != 'SLEEP')
                continue;

            $earningsLine = [
                'StaffID' => $staffId,
                'earningsRateID' => $pay_items[$shift_type][$staffer['level']],
                'numberOfUnits' => $numberOfUnits
            ];

            if (!isset($earningsLines[$item['house_id']][$item['earnings_name']])) {
                $earningsLines[$item['house_id']][$item['earnings_name']] = [
                    'earnings_rate' => $item['earnings_rate'],
                    'duration' => 0
                ];
            }

            $earningsLines[$item['house_id']][$item['earnings_name']]['duration'] += $item['duration'];
        }

        $output = [];

        foreach ($earningsLines as $houseId => $earnings) {
            $entries = [];
            foreach ($earnings as $earningsName => $details) {
                $entries[] = [
                    'earnings_name' => $earningsName,
                    'earnings_rate' => $details['earnings_rate'],
                    'duration' => $details['duration'],
                    'total' => round(($details['duration'] * $details['earnings_rate']), 2),
                ];
            }
            sort($entries);
            $output[] = [
                'house_id' => $houses[$houseId],
                'entries' => $entries
            ];
            sort($output);
        }

        return ['http_code' => 200, 'result' => $output];
    }

    function reformatEarningsData($earnings, $period_start)
    {
        $result = [];

        $startDate = new \DateTime($period_start);

        foreach ($earnings as $row) {
            $staffId = $row['staff_id'];
            $shift_type = $row['shift_type'];

            $staffer = R::getRow('SELECT staffs.name, staffs.xero_employee_ref, staffs.paygrade_id, clientstaffassignments.is_primary FROM staffs join clientstaffassignments on (staffs.id=clientstaffassignments.staff_id) WHERE staffs.id=:staff_id', [':staff_id' => $staffId]);

            $earnings = $this->getPaygrade($staffer['paygrade_id'], $shift_type);

            if ($shift_type == 'SLEEP')
                $row['duration'] = 60;

            $result[] = [
                // "staff_name"=>$staffer['name'],
                'staff_id' => $staffId,
                'shift_type' => $shift_type,
                'duration' => $row['duration'] / 60,
                'house_lead' => $staffer['is_primary'],
                'house_id' => $row['house_id'],
                'earnings_name' => $earnings['name'],
                'earnings_rate' => $earnings['rate'],
            ];
        }
        return $result;
    }

    function getPaygrade($paygrade_id, $shift_type)
    {
        // Step 1: Read the contents of the xeroPayItems.json file
        $jsonData = (new \NDISmate\Cache\Retrieve)('xeroPayItems');

        // TODO: do a check if the file exists, otherwise use FetchPayItems instead.

        // Step 2: Parse the JSON data into a PHP array
        $data = json_decode($jsonData, true);

        $paygrade = R::getRow('SELECT * FROM xeropaygrades WHERE id=:paygrade_id', [':paygrade_id' => $paygrade_id]);

        $earnings_rate_ids = [
            'DAY' => $paygrade['day'],
            'EVE' => $paygrade['evening'],
            'NIGHT' => $paygrade['night'],
            'SAT' => $paygrade['saturday'],
            'SUN' => $paygrade['sunday'],
            'PUB' => $paygrade['public_holiday'],
            'SLEEP' => $paygrade['sleep_over']
        ];

        $earnings_rate_id = $earnings_rate_ids[$shift_type];

        // Step 3: Loop through the array to filter and collect the required data

        foreach ($data['EarningsRates'] as $earningRate) {
            if ($earningRate['EarningsRateID'] === $earnings_rate_id) {
                // Step 4: Collect the required data

                return [
                    'name' => $earningRate['Name'],
                    'rate' => $earningRate['RatePerUnit'],
                ];
            }
        }

        // Step 5: Return the filtered data
        return ['name' => '', 'rate' => 0];
    }
}
