<?php
namespace NDISmate\Models\SIL\House\Form;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetStafferShiftTimeline{

    function __invoke($data){

        $mongo_db = new \MongoDB\Client('mongodb://'.MONGODB_HOST.':'.MONGODB_PORT, [
            'username' => MONGODB_USER,
            'password' => MONGODB_PASSWORD,
            'authSource' => MONGODB_AUTHSOURCE
        ]);
        
        $forms = $mongo_db->crystelcare->forms;


        $end_date = $data['date'];
        $start_time = $data['start_time'];
        $end_time = $data['end_time'];

        
        
        if (strtotime($end_time) < strtotime($start_time)) {
            $start_date = date('Y-m-d', strtotime($end_date . ' -1 day'));
            
        }else {
            $start_date = $end_date;
        }   

        // if date is before 3 OCT 2023, make the date 3 OCT 2023
        if ($start_date < "2023-10-03"){ $start_date = "2023-10-03"; }
        if ($end_date < "2023-10-03"){ $end_date = "2023-10-03"; }

        $filter = [
            'date' => [
                '$gte' => $start_date,
                '$lte' => $end_date
            ],
            '$and' => [
                [
                    'date' => $start_date,
                    'time' => ['$gte' => $start_time]
                ],
                [
                    'date' => $end_date,
                    'time' => ['$lte' => $end_time]
                ]
            ]
        ];


        $filter['participant_id'] = $data['participant_id'];
        $filter['staff_id'] = $data['staff_id'];
        // if($data['summary'] == true){
        //         $twentyFourHoursAgo = strtotime('-24 hours');
        //         $twentyFourHoursAgoDate = date('Y-m-d', $twentyFourHoursAgo);
        //         $filter['date'] = ['$gt' => $twentyFourHoursAgoDate];
        // }

        $cursor = $forms->find($filter, [
            // 'projection' => [
            //     'date' => 1,
            //     'time' => 1,
            //     'staff_id' => 1,
            //     'report_type' => 1,
            //     'note' => 1,
            // ],
            'sort' => ['date' => -1, 'time' => -1, '_id' => -1]
        ])->toArray();

        foreach($cursor as $key => $value){
            $staff = R::load('staffs', $value['staff_id']);
            $cursor[$key]['staff_name'] = $staff['name'];
            $cursor[$key]['id'] = $cursor[$key]->_id->__toString(); 
        }

        return JsonResponse::ok($cursor);

    }
}



// $cursor = $forms->find([
// //   "incident_types.name" => "Property Damage"
// ], [
//     // 'projection' => [
//     //     'name' => 1
//     // ]
// ]);

// foreach ($cursor as $document) {

//     print_r($document);
//     // print($document['name']).PHP_EOL;
//     // echo $document['_id'], "\n";
// }
// exit;
