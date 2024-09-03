<?php

namespace NDISmate\Models\SIL\House\Timeline;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetTimelineList {

    function __invoke($data) {
        //TODO: add pagination
        try {
            $filter = ' participant_id = ? AND date >= ? ';
            $params = [$data['participant_id'], '2023-10-03'];
            
            if ($data['summary'] == true) {
                $twentyFourHoursAgo = strtotime('-24 hours');
                $twentyFourHoursAgoDate = date('Y-m-d', $twentyFourHoursAgo);
                $filter = ' participant_id = ? AND date >= ? ';
                $params = [$data['participant_id'], $twentyFourHoursAgoDate];
            }

            $sql = "SELECT t.*, s.name as staff_name 
                    FROM timeline t 
                    JOIN staffs s ON t.staff_id = s.id 
                    WHERE $filter 
                    ORDER BY t.date DESC, t.time DESC 
                    LIMIT 100";

            $timelines = R::getAll($sql, $params);

            if (!$timelines || empty($timelines)) {
                return JsonResponse::notFound();
            }

            return JsonResponse::ok($timelines);

        } catch (\Exception $e) {
            return JsonResponse::error($e->getMessage());
        }
    }
}