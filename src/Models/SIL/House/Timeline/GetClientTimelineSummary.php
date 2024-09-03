<?php

namespace NDISmate\Models\SIL\House\Form;

use \NDISmate\CORE\JsonResponse;
use \RedBeanPHP\R as R;

class GetParticipantFormSummary {

    function __invoke($data) {
        
        // Step 1: Filter by participant_id and report_type within JSON column
        $participantId = 580;
        $reportTypes = ['Incident'];
        
        $placeholders = implode(',', array_fill(0, count($reportTypes), '?'));
        $reports = R::find('timelines', 'participant_id = ? AND JSON_EXTRACT(form_data, "$.report_type") IN ('.$placeholders.')', array_merge([$participant_id], $report_types));
        
        // Step 2: Group by date, report_type, and participant_id
        $groupedReports = R::getAll(
            'SELECT 
                participant_id, 
                created, 
                JSON_EXTRACT(form_data, "$.report_type") as report_type, 
                COUNT(*) as count
             FROM timelines
             WHERE 
                participant_id = ? AND JSON_EXTRACT(form_data, "$.report_type") IN ('.$placeholders.')
             GROUP BY participant_id, created, report_type
             ORDER BY created DESC',
            array_merge([$participantId], $reportTypes)
        );
        
        // Step 3: Further group by participant_id and date if needed
        $finalGroupedReports = [];
        foreach ($groupedReports as $report) {
            $key = $report['participant_id'] . '_' . $report['created'];
            if (!isset($finalGroupedReports[$key])) {
                $finalGroupedReports[$key] = [
                    'participant_id' => $report['participant_id'],
                    'created' => $report['created'],
                    'reports' => []
                ];
            }
            $finalGroupedReports[$key]['reports'][] = [
                'report_type' => $report['report_type'],
                'count' => $report['count']
            ];
        }
        
        // Convert finalGroupedReports to array values
        $finalGroupedReports = array_values($finalGroupedReports);
        
        // Output or further process $finalGroupedReports as needed

        return JsonResponse::ok($finalGroupedReports);
    }
}