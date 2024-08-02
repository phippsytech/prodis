<?php
namespace NDISmate\Models\SIL\Payrun;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetEarnings
{
    function __invoke($data)
    {
        $shifts = (new \NDISmate\Models\SIL\House\Roster\GetShifts)($data);

        if (count($shifts) > 0) {
            foreach ($shifts as $shift) {
                // if($shift['do_not_bill']==1) continue;

                $results[] = $this->calculateTimeWorked($shift);
            }

            $billing_items = array_merge(...$results);
            return $billing_items;
        } else {
            return [];
        }
    }

    function isPublicHoliday($date)
    {
        // Load the public holiday data from the JSON file or source
        $publicHolidays = json_decode(file_get_contents(BASE_PATH . '/src/Models/SIL/Billing/sa_public_holidays.json'), true);

        // Check if the given date is a public holiday
        foreach ($publicHolidays as $holiday) {
            if ($holiday['date'] === $date) {
                return true;
            }
        }

        return false;
    }

    function calculateTimeWorked($shift)
    {
        $shiftTable = json_decode(file_get_contents(BASE_PATH . '/src/Models/SIL/Payrun/shift_type_table.json'), true);

        $start_date = $shift['start_date'];
        $end_date = $shift['end_date'];
        $start_time = $shift['start_time'];
        $end_time = $shift['end_time'];
        $start_timestamp = strtotime("$start_date $start_time");
        $end_timestamp = strtotime("$end_date $end_time");
        $day_of_week = date('l', strtotime($start_date));
        $staff_id = $shift['staff_id'];
        $house_id = $shift['house_id'];
        $passive = $shift['passive'];

        $result = [];

        if ($passive == 1) {
            $billing_end_timestamp = strtotime('+1 day', $end_timestamp);

            $row = [
                'start_date' => date('Y-m-d', max($start_timestamp, $billing_start_timestamp)),
                'start_time' => date('H:i', max($start_timestamp, $billing_start_timestamp)),
                'end_date' => date('Y-m-d', min($end_timestamp, $billing_end_timestamp)),
                'end_time' => date('H:i', min($end_timestamp, $billing_end_timestamp)),
                'duration' => (min($end_timestamp, $billing_end_timestamp) - max($start_timestamp, $billing_start_timestamp)) / 60,
                'staff_id' => $staff_id,
                'house_id' => $house_id,
                'shift_type' => 'SLEEP'
            ];

            $result[] = $row;
            return $result;
        }

        // Iterate over the days between the start and end dates (inclusive)
        $current_billing_date = $start_date;
        while ($current_billing_date <= $end_date) {
            $day_of_week = date('l', strtotime($current_billing_date));;
            if ($this->isPublicHoliday($current_billing_date)) {
                $day_of_week = 'Public Holiday';
            }

            foreach ($shiftTable[$day_of_week] as $shiftRateItem) {
                $billing_start_timestamp = strtotime("$current_billing_date {$shiftRateItem['start']}");
                $billing_end_timestamp = strtotime("$current_billing_date {$shiftRateItem['end']}");

                if ($billing_end_timestamp < $billing_start_timestamp) {
                    $billing_end_timestamp = strtotime('+1 day', $billing_end_timestamp);
                }

                if ($start_timestamp <= $billing_end_timestamp && $end_timestamp >= $billing_start_timestamp) {
                    $row = [
                        'start_date' => date('Y-m-d', max($start_timestamp, $billing_start_timestamp)),
                        'start_time' => date('H:i', max($start_timestamp, $billing_start_timestamp)),
                        'end_date' => date('Y-m-d', min($end_timestamp, $billing_end_timestamp)),
                        'end_time' => date('H:i', min($end_timestamp, $billing_end_timestamp)),
                        'duration' => (min($end_timestamp, $billing_end_timestamp) - max($start_timestamp, $billing_start_timestamp)) / 60,
                        'staff_id' => $staff_id,
                        'house_id' => $house_id,
                        'shift_type' => $shiftRateItem['shift_type']
                    ];
                    if ($row['duration'] > 0 && $row['staff_id'] != null) {
                        $result[] = $row;
                    }
                }
            }

            $current_billing_date = date('Y-m-d', strtotime($current_billing_date . ' +1 day'));
        }

        return $result;
    }
}
