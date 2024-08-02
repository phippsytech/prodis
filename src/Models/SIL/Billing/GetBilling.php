<?php
namespace NDISmate\Models\SIL\Billing;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetBilling
{
    function __invoke($data)
    {
        $shifts = (new \NDISmate\Models\SIL\House\Roster\GetShifts)($data);

        if (count($shifts) > 0) {
            foreach ($shifts as $shift) {
                if ($shift['do_not_bill'] == 1)
                    continue;

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
        switch ($shift['house_id']) {
            case 1:  // cj
            case 2:  // rhys
            case 3:  // logan
                $billingTable = json_decode(file_get_contents(BASE_PATH . '/src/Models/SIL/Billing/sil_hi_billing_table.json'), true);
                break;

                // $billingTable = json_decode(file_get_contents(BASE_PATH.'/src/Models/SIL/Billing/sca_billing_table.json'), true);
                // break;
        }

        $start_date = $shift['start_date'];
        $end_date = $shift['end_date'];
        $start_time = $shift['start_time'];
        $end_time = $shift['end_time'];
        $start_timestamp = strtotime("$start_date $start_time");
        $end_timestamp = strtotime("$end_date $end_time");
        $day_of_week = date('l', strtotime($start_date));
        $staff_id = $shift['staff_id'];

        $result = [];

        // Iterate over the days between the start and end dates (inclusive)
        $current_billing_date = $start_date;
        while ($current_billing_date <= $end_date) {
            $day_of_week = date('l', strtotime($current_billing_date));;
            if ($this->isPublicHoliday($current_billing_date)) {
                $day_of_week = 'Public Holiday';
            }

            foreach ($billingTable[$day_of_week] as $billingCode) {
                $billing_start_timestamp = strtotime("$current_billing_date {$billingCode['start']}");
                $billing_end_timestamp = strtotime("$current_billing_date {$billingCode['end']}");

                if ($billing_end_timestamp < $billing_start_timestamp) {
                    $billing_end_timestamp = strtotime('+1 day', $billing_end_timestamp);
                }

                if ($start_timestamp <= $billing_end_timestamp && $end_timestamp >= $billing_start_timestamp) {
                    $row = [
                        'start_date' => date('Y-m-d', max($start_timestamp, $billing_start_timestamp)),
                        'start_time' => date('H:i', max($start_timestamp, $billing_start_timestamp)),
                        'end_date' => date('Y-m-d', min($end_timestamp, $billing_end_timestamp)),
                        'end_time' => date('H:i', min($end_timestamp, $billing_end_timestamp)),
                        'session_duration' => (min($end_timestamp, $billing_end_timestamp) - max($start_timestamp, $billing_start_timestamp)) / 60,
                        'staff_id' => $staff_id,
                        'service_code' => $billingCode['service_code']
                    ];
                    if ($row['session_duration'] > 0) {
                        $result[] = $row;
                    }
                }
            }

            $current_billing_date = date('Y-m-d', strtotime($current_billing_date . ' +1 day'));
        }

        return $result;
    }
}
