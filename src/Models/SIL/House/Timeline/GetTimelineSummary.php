<?php

namespace NDISmate\Models\SIL\House\Form;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetTimelineSummary {
    //TODO: test this properly
    function __invoke($data) {
        $sql = "
        SELECT 
            participant_id, 
            created, 
            JSON_UNQUOTE(JSON_EXTRACT(form_data, '$.report_type')) AS report_type, 
            COUNT(*) as count 
        FROM 
            timelines 
        GROUP BY 
            participant_id, 
            created, 
            report_type 
        ORDER BY 
            participant_id ASC, 
            created DESC, 
            report_type ASC
        ";
        
        // Execute the query and get the results
        $initialGrouping = R::getAll($sql);
        
        // Step 2: Group by participant_id and date
        $finalGrouping = [];
        foreach ($initialGrouping as $row) {
            $participantId = $row['participant_id'];
            $date = $row['created'];
            if (!isset($finalGrouping[$participantId])) {
                $finalGrouping[$participantId] = [];
            }
            if (!isset($finalGrouping[$participantId][$date])) {
                $finalGrouping[$participantId][$date] = [];
            }
            $finalGrouping[$participantId][$date][] = [
                'report_type' => $row['report_type'],
                'count' => $row['count']
            ];
        }
        
        // Output the final grouped data
        return JsonResponse::success($finalGrouping);
    }
}