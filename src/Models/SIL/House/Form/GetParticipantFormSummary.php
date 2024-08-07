<?php
namespace NDISmate\Models\SIL\House\Form;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetParticipantFormSummary{

    function __invoke($data){

        $mongo_db = new \MongoDB\Client('mongodb://'.MONGODB_HOST.':'.MONGODB_PORT, [
            'username' => MONGODB_USER,
            'password' => MONGODB_PASSWORD,
            'authSource' => MONGODB_AUTHSOURCE
        ]);
        
        $forms = $mongo_db->crystelcare->forms;

        $start_date = $data['start_date'];
        $end_date = $data['end_date'];

        // if date is before 3 OCT 2023, make the date 3 OCT 2023
        if ($start_date < "2023-10-03"){ $start_date = "2023-10-03"; }
        if ($end_date < "2023-10-03"){ $end_date = "2023-10-03"; }

        // $filter = [
            
        // ];

        // $filter = [
        //     'date' => [
        //         '$gte' => $start_date,
        //         '$lte' => $end_date
        //     ],
        //     'report_type' => [
        //         '$in' => ['EndOfShift','EndOfShiftAlex','EndOfShiftCJ','EndOfShiftLogan', 'EndOfShiftRhys']
        //     ],
        //     '$and' => [
        //         ['kilometers_travelled' => ['$ne' => null]],   // Not null
        //         ['kilometers_travelled' => ['$ne' => '']]      // Not an empty string
        //     ]
            
        // ];


        $pipeline = [

            // filter
            
            [
                '$match' => [

                    'participant_id' => "580",
                    'report_type' => [
                        '$in' => ['Incident']
                    ],
                    
                ]
            ],


            // // 1. Initial Grouping by date, report_type, and participant_id
            [
                '$group' => [
                    '_id' => [
                        'participant_id' => '$participant_id',
                        'date' => '$date',
                        'report_type' => '$report_type',
                        // 'report_required'=> '$report_required'
                    ],
                    'count' => ['$sum' => 1]
                ]
            ],
            // Sort by date (descending)
            [
                '$sort' => [
                    '_id.date' => -1,
                ]
            ],
        
            // // 2. Group by participant_id and date
            // [
            //     '$group' => [
            //         '_id' => [
            //             'participant_id' => '$_id.participant_id',
            //             'date' => '$_id.date'
            //         ],
            //         'reports' => [
            //             '$push' => [
            //                 'report_type' => '$_id.report_type',
            //                 'count' => '$count'
            //             ]
            //         ]
            //     ]
            // ],
            // // Sort again by participant_id (ascending) to ensure correct order in the final output
            // [
            //     '$sort' => [
            //         '_id.participant_id' => 1,
            //         '_id.date' => -1,
            //         '_id.report_type' => 1
            //     ]
            // ],
        
            // // 3. Final Group by participant_id
            // [
            //     '$group' => [
            //         '_id' => '$_id.participant_id',
            //         'dates' => [
            //             '$push' => [
            //                 'date' => '$_id.date',
            //                 'reports' => '$reports'
            //             ]
            //         ]
            //     ]
            // ],
        
            // Optionally reshape the output to match the desired format
            [
                '$project' => [
                    'participant_id' => '$_id.participant_id',
                    'date' => '$_id.date',
                    'report_type' => '$_id.report_type',
                    // 'report_required' => '$_id.report_required',

                    'count' => '$count',
                    '_id' => 0
                ]
            ]
        ];
        
        
        $cursor = $forms->aggregate($pipeline)->toArray();
        
        // print_r($cursor);

        // $cursor = $forms->find($filter, [
        //     'projection' => [
        //         'date' => 1,
        //         'time' => 1,
        //         'start_shift' => 1,
        //         'end_shift' => 1,
        //         'staff_id' => 1,
        //         'staff_name' => 1,
        //         'participant_id' => 1,
        //         'participant_name' => 1,
        //         'kilometers_travelled' => 1,
        //         'report_type' => 1,
        //         'note' => 1,
        //     ],
        //     'sort' => ['participant_id'=>1, 'date' => 1, 'time' => 1, '_id' => 1]
        // ])->toArray();

            // print_r($cursor);

            // return JsonResponse::ok($cursor);

        // foreach($cursor as $key => $value){
        //     // $staff = R::load('staffs', $value['staff_id']);
        //     $participant = R::load('clients', $value['participant_id']);
        //     // $cursor[$key]['staff_name'] = $staff['name'];
        //     $cursor[$key]['participant_name'] = $participant['name'];
        //     // $cursor[$key]['id'] = $cursor[$key]->_id->__toString(); 
        // }

        return JsonResponse::ok($cursor);

    }
}

