<?php
namespace NDISmate\Models\SIL\House\Form;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetAvailableReportTypes{

    function __invoke($data){

        // print_r($data);

        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $participant_id = $data['participant_id'];

// if date is before 3 OCT 2023, make the date 3 OCT 2023
if ($start_date < "2023-10-03"){ $start_date = "2023-10-03"; }
if ($end_date < "2023-10-03"){ $end_date = "2023-10-03"; }


        $mongo_db = new \MongoDB\Client("mongodb://localhost:27017",[
            "username" => MONGODB_USER,
            "password" => MONGODB_PASSWORD,
            "authSource"  => MONGODB_DATABASE
        ]);
        
        $forms = $mongo_db->crystelcare->forms;

        // Define the filter
        $filter = [
            'date' => [
                '$gte' => $start_date,
                '$lte' => $end_date
            ],
            'participant_id' => $participant_id
        ];

        // Define the pipeline
        $pipeline = [
            [
                '$match' => $filter
            ],
            [
                '$group' => [
                    '_id' => '$report_type',
                    'count' => [ '$sum' => 1 ]
                ]
            ],
            [
                '$project' => [
                    'report_type' => '$_id',
                    'count' => 1,
                    '_id' => 0
                ]
            ]
        ];

        

        // Execute the aggregation
        $cursor = $forms->aggregate($pipeline)->toArray();

        // $report_types = array_map(function($item) {
        //     return $item['_id'];
        // }, $cursor);


        // The result will be an array of documents, each containing a unique report_type and an array of occurrences.
        // foreach($cursor as $key => $value){
        //     $participant = R::load('clients', $value['participant_id']);
        //     $cursor[$key]['participant_name'] = $participant['name'];
        // }

        return JsonResponse::ok($cursor);

    }
}

