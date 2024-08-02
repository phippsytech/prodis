<?php
namespace NDISmate\Models\Staff\Downtime;

use \NDISmate\CORE\JsonResponse;
use \NDISmate\CORE\CRUD;
use Respect\Validation\Validator as v; 
use \NDISmate\CORE\KeyValue;
use \RedBeanPHP\R as R;


class GetAvailableStaff{

    function __invoke($data){
        $start_date_time = $data['start_date_time'];
        $end_date_time = $data['end_date_time'];
        $house_id = $data['house_id'];

        // Convert the date/time strings to DateTime objects
        $start_date_time_obj = new DateTime($start_date_time);
        $end_date_time_obj = new DateTime($end_date_time);

        // Fetch the recurring downtime data within the specified date range
        $recurring_downtimes = R::find('downtime', 'recurrence_type != ""');
        $exception_downtimes = R::find('downtime_exceptions');

        // Store staff IDs with recurring downtime in a separate array
        $staff_ids_with_recurring_downtime = [];

        // Generate a list of recurring dates within the specified range
        $current_date = clone $start_date_time_obj;
        while ($current_date <= $end_date_time_obj) {
            foreach ($recurring_downtimes as $recurring_downtime) {
                $rec_start_date_time = new DateTime($recurring_downtime->recurring_start_date . ' ' . $recurring_downtime->start_time);
                $rec_end_date_time = new DateTime($recurring_downtime->recurring_end_date . ' ' . $recurring_downtime->end_time);
                
                // Check if the recurring downtime overlaps with the current date
                if ($rec_start_date_time->format('Y-m-d') <= $current_date->format('Y-m-d') && $rec_end_date_time->format('Y-m-d') >= $current_date->format('Y-m-d')) {
                    $staff_ids_with_recurring_downtime[] = $recurring_downtime->staff_id;
                    break;
                }
            }

            $current_date->modify('+1 day');
        }

        // Extract the unique staff IDs from the downtime lists
        $staff_ids = [];
        foreach ($recurring_downtimes as $recurring_downtime) {
            if (!in_array($recurring_downtime->staff_id, $staff_ids) && !in_array($recurring_downtime->staff_id, $staff_ids_with_recurring_downtime)) {
                $staff_ids[] = $recurring_downtime->staff_id;
            }
        }

        // Fetch the staff IDs that have shifts during the specified date/time range
        $shift_staff_ids = R::getCol('SELECT DISTINCT staff_id FROM shifts WHERE start_date <= ? AND end_date >= ? AND house_id = ?', [$end_date_time, $start_date_time, $house_id]);

        // Remove the staff IDs with shifts from the available staff IDs
        $available_staff_ids = array_diff($staff_ids, $shift_staff_ids);

        // Handle exceptional downtime entries
        foreach ($exception_downtimes as $exception_downtime) {
            $start_date_time_obj = new DateTime($exception_downtime->start_date . ' ' . $exception_downtime->start_time);
            $end_date_time_obj = new DateTime($exception_downtime->end_date . ' ' . $exception_downtime->end_time);

            // Check if the exceptional downtime overlaps with the specified date range
            if ($start_date_time_obj <= $end_date_time_obj && $start_date_time_obj <= $end_date_time_obj) {
                $index = array_search($exception_downtime->staff_id, $available_staff_ids);
                if ($index !== false) {
                    unset($available_staff_ids[$index]);
                }
            }
        }

        return array_values($available_staff_ids);
    }

}

// // Example usage:
// $start_date_time = '2023-07-22 10:00:00';
// $end_date_time = '2023-07-22 15:00:00';
// $house_ID = 1; // Replace with the desired house ID
// $available_staff = getAvailableStaff($start_date_time, $end_date_time, $house_id);

// // Output the result
// print_r($available_staff);

