<?php
namespace NDISmate\Models\SIL\House\Form;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetCSVExport{

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

        $report_type = $data['report_type'];
        $participant_id = $data['participant_id'];

        $filter = [
            'date' => [
                '$gte' => $start_date,
                '$lte' => $end_date
            ],
            'report_type' => [
                '$eq' => $report_type
            ],
            'participant_id' => [
                '$eq' => $participant_id
            ]
        ];

        

        $cursor = $forms->find($filter, [
            // 'projection' => [
            //     'date' => 1,
            //     'time' => 1,
            //     'start_shift' => 1,
            //     'end_shift' => 1,
            //     'staff_id' => 1,
            //     'staff_name' => 1,
            //     'participant_id' => 1,
            //     'participant_name' => 1,
            //     'kilometers_travelled' => 1,
            //     'report_type' => 1,
            //     'note' => 1,
            // ],
            'sort' => ['date' => 1, 'time' => 1, '_id' => 1]
        ])->toArray();

        // foreach($cursor as $key => $value){
        //     $staff = R::load('staffs', $value['staff_id']);
        //     $participant = R::load('clients', $value['participant_id']);
        //     $cursor[$key]['staff_name'] = $staff['name'];
        //     $cursor[$key]['participant_name'] = $participant['name'];
        //     $cursor[$key]['id'] = $cursor[$key]->_id->__toString(); 
        // }

        // remove edit history from export
        foreach($cursor as $key => $value){
            if (isset($cursor[$key]['edit_history'])){
                unset($cursor[$key]['edit_history']);
            }
        }


        // Convert the BSONDocument object to an array
        $firstRow = reset($cursor);
        $firstRowArray = (array) $firstRow;

        // Open a file in write mode ('w')
        $csvFile = fopen('php://temp', 'w');

        // Output the column headings
        fputcsv($csvFile, array_keys($firstRowArray));

        // Function to convert non-scalar values (like arrays or objects) to strings
        $stringify = function($item) {
            if (is_array($item) || is_object($item)) {
                return json_encode($item);
            } else {
                return $item;
            }
        };

        // Loop through the cursor and output each row
        foreach ($cursor as $row) {
            $rowArray = (array) $row;
            $rowArray = array_map($stringify, $rowArray);
            fputcsv($csvFile, $rowArray);
        }

        // Rewind the file
        rewind($csvFile);

        // Read the contents of the file into a string
        $csvString = stream_get_contents($csvFile);

        // Close the file
        fclose($csvFile);

        // return JsonResponse::ok($cursor);
        return JsonResponse::ok($csvString);

    }
}


