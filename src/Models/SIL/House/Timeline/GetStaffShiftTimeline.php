<?php

namespace NDISmate\Models\SIL\House\Timeline;

use \RedBeanPHP\R as R;

class GetStaffShiftTimeline {

    function __invoke($data) {

        try {

            $endDate = $data['date'];
            $startTime = $data['start_time'];
            $endTime = $data['end_time'];

            if (strtotime($endTime) < strtotime($startTime)) {
                $startDate = date('Y-m-d', strtotime($endDate . ' -1 day'));
            } else {
                $startDate = $endDate;
            }

            // if date is before 3 OCT 2023, make the date 3 OCT 2023
            if ($startDate < "2023-10-03") { $startDate = "2023-10-03"; }
            if ($endDate < "2023-10-03") { $endDate = "2023-10-03"; }

            // Create the query conditions
            $conditions = [
                'created >= ? AND created <= ? AND participant_id = ? AND staff_id = ? AND ((created = ? AND time >= ?) OR (created = ? AND time <= ?))',
                [$startDate, $endDate, $data['participant_id'], $data['staff_id'], $startDate, $startTime, $endDate, $endTime]
            ];

            // Fetch data using RedBeanPHP
            $forms = R::find('forms', $conditions[0], $conditions[1]);

            // Return or process $forms as needed
            return $forms;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

    }

     
}