<?php
namespace NDISmate\Models\SIL\House\Form;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetTimeline
{
    function __invoke($data)
    {
        $mongo_db = new \MongoDB\Client('mongodb://'.MONGODB_HOST.':'.MONGODB_PORT, [
            'username' => MONGODB_USER,
            'password' => MONGODB_PASSWORD,
            'authSource' => MONGODB_AUTHSOURCE
        ]);

        $forms = $mongo_db->crystelcare->forms;

        $filter['participant_id'] = $data['participant_id'];
        $filter['date'] = ['$gte' => '2023-10-03'];
        if ($data['summary'] == true) {
            $twentyFourHoursAgo = strtotime('-24 hours');
            $twentyFourHoursAgoDate = date('Y-m-d', $twentyFourHoursAgo);
            $filter['date'] = ['$gte' => $twentyFourHoursAgoDate];
        }

        $cursor = $forms->find($filter, [
            // 'projection' => [
            //     'date' => 1,
            //     'time' => 1,
            //     'staff_id' => 1,
            //     'report_type' => 1,
            //     'note' => 1,
            // ],
            'sort' => ['date' => -1, 'time' => -1, '_id' => -1],
            'limit' => 100,
        ])->toArray();

        foreach ($cursor as $key => $value) {
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
