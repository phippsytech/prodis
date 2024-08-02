<?php

namespace NDISmate\Utilities;

class TimeElapsed{

    function __invoke($start_date, $end_date){
        // Calculate the difference between the two dates
        $diff = abs(strtotime($end_date) - strtotime($start_date));
            
        // Calculate the number of days in the time range
        $days = floor($diff / (60 * 60 * 24));

        // Calculate the number of days that have passed since the start date
        $elapsed = abs(strtotime(date('Y-m-d')) - strtotime($start_date));

        // Calculate the percentage of time that has elapsed
        $percent = 100 * ($elapsed / ($days * 60 * 60 * 24));

        // Return the percentage of time that has elapsed
        return $percent;
    }

}

// $timeElapsed = new \NDISmate\Utilities\TimeElapsed($start_date, $end_date);