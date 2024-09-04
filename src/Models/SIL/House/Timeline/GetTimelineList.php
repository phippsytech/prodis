<?php

namespace NDISmate\Models\SIL\House\Timeline;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetTimelineList {

    function __invoke($data) {
        //TODO: add pagination
        try {
            $filter = ' participant_id = :participant_id and created >= 2023-10-03';
            $params = [":participant_id" => $data['participant_id']];
            
            if ($data['summary'] == true) {
                $twentyFourHoursAgo = strtotime('-24 hours');

                $twentyFourHoursAgoDate = date('Y-m-d', $twentyFourHoursAgo);
                
                $filter = ' participant_id = :participant_id and created >= :twentyFourHoursAgoDate';
                
                $params = [":participant_id" => $data['participant_id'], ":twentyFourHoursAgoDate" => $twentyFourHoursAgoDate];
            }

            $sql = "SELECT t.*, s.name as staff_name 
                    FROM timelines t 
                    JOIN staffs s ON t.staff_id = s.id 
                    WHERE $filter 
                    ORDER BY t.created DESC
                    LIMIT 100";
            
            $timelines = R::getAll($sql, $params);
            
            if (!$timelines || empty($timelines)) {
                throw new \Exception("No timelines found", 404);
            }

            foreach ($timelines as $key => $value) {
                $timelines[$key]['form_data'] = json_decode($value['form_data'], true);
            }

            return $timelines;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}