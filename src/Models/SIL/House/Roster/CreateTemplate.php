<?php
namespace NDISmate\Models\SIL\House\Roster;

use \NDISmate\CORE\JsonResponse;


class CreateTemplate{

    function __invoke($obj, $data){

        $template = '[
            {
                "day": "Monday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "14:00", "end": "22:00", "staff_id": null}, 
                    {"start": "14:00", "end": "20:00", "staff_id": null}, 
                    {"start": "22:00", "end": "06:00", "staff_id": null}
                ]
            },
            {
                "day": "Tuesday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "14:00", "end": "22:00", "staff_id": null}, 
                    {"start": "14:00", "end": "20:00", "staff_id": null}, 
                    {"start": "22:00", "end": "06:00", "staff_id": null} 
                ]
            },
            {
                "day": "Wednesday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "14:00", "end": "22:00", "staff_id": null}, 
                    {"start": "14:00", "end": "20:00", "staff_id": null}, 
                    {"start": "22:00", "end": "06:00", "staff_id": null} 
                ]
            },
            {
                "day": "Thursday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "14:00", "end": "22:00", "staff_id": null}, 
                    {"start": "14:00", "end": "20:00", "staff_id": null}, 
                    {"start": "22:00", "end": "06:00", "staff_id": null} 
                ]
            },
            {
                "day": "Friday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "14:00", "end": "22:00", "staff_id": null}, 
                    {"start": "14:00", "end": "20:00", "staff_id": null}, 
                    {"start": "22:00", "end": "06:00", "staff_id": null} 
                ]
            },
            {
                "day": "Saturday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "14:00", "end": "22:00", "staff_id": null}, 
                    {"start": "14:00", "end": "20:00", "staff_id": null}, 
                    {"start": "22:00", "end": "06:00", "staff_id": null} 
                ]
            },
            {
                "day": "Sunday",
                "shifts": [
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "06:00", "end": "14:00", "staff_id": null}, 
                    {"start": "14:00", "end": "22:00", "staff_id": null},
                    {"start": "14:00", "end": "20:00", "staff_id": null},
                    {"start": "22:00", "end": "06:00", "staff_id": null}
                ]
            }
            ]';
        
        $data['template'] = json_encode(json_decode($template, true)); //decode / encode to clean up the json

        $result = $obj->CRUD->create($data);
        return $template;
        // return ["http_code"=>200, "result"=>$result];

    }
}
