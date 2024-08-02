<?php
namespace NDISmate\Models\SIL\House\Roster;

use \NDISmate\CORE\JsonResponse;


class GetTemplate{

    function __invoke($data){

        $json = '[
            {
                "day": "Monday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": 1}, 
                    {"start": "06:00", "end": "14:00", "staff_id": 2}, 
                    {"start": "14:00", "end": "22:00", "staff_id": 3}, 
                    {"start": "14:00", "end": "20:00", "staff_id": 4}, 
                    {"start": "22:00", "end": "06:00", "staff_id": 3}
                ]
            },
            {
                "day": "Tuesday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": 4}, 
                    {"start": "06:00", "end": "14:00", "staff_id": 3}, 
                    {"start": "14:00", "end": "22:00", "staff_id": 5}, 
                    {"start": "14:00", "end": "20:00", "staff_id": 1}, 
                    {"start": "22:00", "end": "06:00", "staff_id": 5} 
                ]
            },
            {
                "day": "Wednesday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": 5}, 
                    {"start": "06:00", "end": "14:00", "staff_id": 2}, 
                    {"start": "14:00", "end": "22:00", "staff_id": 4}, 
                    {"start": "14:00", "end": "20:00", "staff_id": 1}, 
                    {"start": "22:00", "end": "06:00", "staff_id": 4} 
                ]
            },
            {
                "day": "Thursday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": 4}, 
                    {"start": "06:00", "end": "14:00", "staff_id": 1}, 
                    {"start": "14:00", "end": "22:00", "staff_id": 6}, 
                    {"start": "14:00", "end": "20:00", "staff_id": 2}, 
                    {"start": "22:00", "end": "06:00", "staff_id": 6} 
                ]
            },
            {
                "day": "Friday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": 4}, 
                    {"start": "06:00", "end": "14:00", "staff_id": 7}, 
                    {"start": "14:00", "end": "22:00", "staff_id": 5}, 
                    {"start": "14:00", "end": "20:00", "staff_id": 6}, 
                    {"start": "22:00", "end": "06:00", "staff_id": 5} 
                ]
            },
            {
                "day": "Saturday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": 5}, 
                    {"start": "06:00", "end": "14:00", "staff_id": 7}, 
                    {"start": "14:00", "end": "22:00", "staff_id": 3}, 
                    {"start": "14:00", "end": "20:00", "staff_id": 6}, 
                    {"start": "22:00", "end": "06:00", "staff_id": 3} 
                ]
            },
            {
                "day": "Sunday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": 1}, 
                    {"start": "06:00", "end": "14:00", "staff_id": 7}, 
                    {"start": "14:00", "end": "22:00", "staff_id": 2},
                    {"start": "14:00", "end": "20:00", "staff_id": 5},
                    {"start": "22:00", "end": "06:00", "staff_id": 2}
                ]
            }
            ]';

            
        $shifts = json_decode($json, true);

        return $shifts;

    }
}
